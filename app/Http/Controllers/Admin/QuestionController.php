<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmiChecklistQuestion;
use App\Models\PreparationStage;
use App\Models\Question;
use App\Models\Standard;
use Illuminate\Http\Request;
use App\Models\QuestionCategory;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['standard', 'category'])
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->latest('id');

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('standard_id')) {
            $query->where('standard_id', $request->standard_id);
        }
        if ($request->filled('search')) {
            $query->where('label', 'like', '%' . $request->search . '%');
        }

        $questions = $query->paginate(100)->appends($request->query());

        // Nested grouping: Category -> Standard -> Questions
        $nestedGroups = $questions->getCollection()
            ->groupBy(fn($q) => $q->category_id ?? 0)
            ->map(fn($catItems) => $catItems->groupBy(fn($q) => $q->standard_id ?? 0));

        $categories = QuestionCategory::orderBy('name')->get();
        $standards = Standard::all()->sortBy(fn($s) => (int) filter_var($s->code, FILTER_SANITIZE_NUMBER_INT));

        return view('admin.questions.index', compact('questions', 'nestedGroups', 'categories', 'standards'));
    }


    /** AJAX: return preparation tasks for a given standard */
    public function preparationTasks(Request $request)
    {
        $standardId = $request->integer('standard_id');
        $tasks = PreparationStage::with('tasks')
            ->where('standard_id', $standardId)
            ->orderBy('sort_order')
            ->get()
            ->flatMap(fn($stage) => $stage->tasks->map(fn($task) => [
                'id'    => $task->id,
                'label' => $stage->title . ' – ' . $task->title,
                'value' => $stage->title . ' – ' . $task->title,
            ]));

        return response()->json($tasks->values());
    }

    public function create()
    {
        $standards = Standard::all()->sortBy(fn($s) => (int) filter_var($s->code, FILTER_SANITIZE_NUMBER_INT));
        $categories = QuestionCategory::orderBy('name')->get();

        $checklistStandards = AmiChecklistQuestion::select('standard_code', 'standard_name', 'bidang', 'auditi')
            ->distinct()
            ->orderBy('standard_code')
            ->get();

        return view('admin.questions.create', compact('standards', 'categories', 'checklistStandards'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'standard_id' => ['nullable', 'exists:standards,id'],
            'category_id' => ['nullable', 'exists:question_categories,id'],
            'label'       => ['required', 'string', 'max:5000'],
            'type'        => ['required', 'in:text,textarea,radio,select,file'],
            'reference'   => ['nullable', 'string', 'max:5000'],
            'is_required' => ['nullable', 'boolean'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_required'] = (bool) ($data['is_required'] ?? false);
        $data['is_active']   = (bool) ($data['is_active'] ?? true);

        $maxOrder = Question::where('standard_id', $data['standard_id'] ?? null)->max('sort_order') ?? 0;
        $data['sort_order'] = $maxOrder + 1;

        $q = Question::create($data);

        if (in_array($q->type, ['select', 'radio'], true)) {
            $this->syncYesNoOptions($q);
        }

        return redirect()->route('admin.questions.edit', $q)->with('success', 'Soal dibuat.');
    }

    public function edit(Question $question)
    {
        $question->load('options');

        // Manual sorting to ensure ST1, ST2... ST10 order
        $standards = Standard::all()->sortBy(function ($s) {
            return (int) filter_var($s->code, FILTER_SANITIZE_NUMBER_INT);
        });

        $categories = QuestionCategory::orderBy('name')->orderBy('id')->get();
        $types = ['text', 'textarea', 'number', 'select', 'radio', 'checkbox', 'file'];

        return view('admin.questions.edit', compact('question', 'standards', 'categories', 'types'));
    }

    public function update(Request $request, Question $question)
    {
        $data = $request->validate([
            'standard_id' => ['nullable', 'exists:standards,id'],
            'category_id' => ['nullable', 'exists:question_categories,id'], // ✅ TAMBAH INI
            'label' => ['required', 'string', 'max:5000'],
            'type' => ['required', 'in:text,textarea,radio,select,file'],
            'reference' => ['nullable', 'string', 'max:5000'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $question->standard_id = $data['standard_id'] ?? null;
        $question->category_id = $data['category_id'] ?? null; // ✅ sekarang ada isinya
        $question->label       = $data['label'];
        $question->type        = $data['type'];
        $question->reference   = $data['reference'] ?? null;
        $question->is_required = (bool) ($data['is_required'] ?? false);
        $question->is_active   = (bool) ($data['is_active'] ?? true);
        $question->sort_order  = (int) ($data['sort_order'] ?? 0);

        $question->save();

        if (in_array($question->type, ['select', 'radio'], true)) {
            $this->syncYesNoOptions($question);
        } else {
            $question->options()->delete();
        }

        return back()->with('success', 'Soal diupdate.');
    }


    /** AJAX: return checklist questions filtered by standard_code or standard_id */
    public function checklistQuestions(Request $request)
    {
        $code = $request->get('standard_code');

        // resolve code from standard_id when code not given directly
        if (! $code && $request->filled('standard_id')) {
            $standard = Standard::find($request->integer('standard_id'));
            $code = $standard?->code;
        }

        // Normalize standard code (e.g. ST1 -> ST01) to match database format
        if ($code && preg_match('/^ST([1-9])$/i', $code, $matches)) {
            $code = 'ST0' . $matches[1];
        }

        $items = AmiChecklistQuestion::when($code, fn($q) => $q->where('standard_code', $code))
            ->orderBy('standard_code')
            ->orderBy('question_number')
            ->get(['id', 'standard_code', 'standard_name', 'bidang', 'auditi', 'question_number', 'question_text']);

        return response()->json($items);
    }

    /** GET: show bulk import form */
    public function importChecklistForm(Request $request)
    {
        $standards = Standard::all()->sortBy(fn($s) => (int) filter_var($s->code, FILTER_SANITIZE_NUMBER_INT));
        $categories = QuestionCategory::orderBy('name')->get();

        $checklistStandards = AmiChecklistQuestion::select('standard_code', 'standard_name', 'bidang', 'auditi')
            ->distinct()
            ->orderBy('standard_code')
            ->get();

        // Pre-resolve checklist standard code from the standard_id query param (passed from create page)
        $preselectedCode = '';
        if ($request->filled('standard_id')) {
            $standard = Standard::find($request->integer('standard_id'));
            if ($standard) {
                $code = $standard->code;
                // Normalize ST1 → ST01 to match checklist DB format
                if (preg_match('/^ST([1-9])$/i', $code, $m)) {
                    $code = 'ST0' . $m[1];
                }
                $preselectedCode = $code;
            }
        }

        return view('admin.questions.import-checklist', compact('standards', 'categories', 'checklistStandards', 'preselectedCode'));
    }

    /** POST: bulk-create questions from selected checklist items */
    public function importChecklist(Request $request)
    {
        $data = $request->validate([
            'standard_id'     => ['nullable', 'exists:standards,id'],
            'category_id'     => ['nullable', 'exists:question_categories,id'],
            'type'            => ['nullable', 'in:text,textarea,radio,select,file'],
            'reference'       => ['nullable', 'string', 'max:5000'],
            'checklist_ids'   => ['required', 'array', 'min:1'],
            'checklist_ids.*' => ['exists:ami_checklist_questions,id'],
        ]);

        $type = $data['type'] ?? 'radio';

        $items = AmiChecklistQuestion::whereIn('id', $data['checklist_ids'])
            ->orderBy('standard_code')
            ->orderBy('question_number')
            ->get();

        foreach ($items as $item) {
            $maxOrder = Question::where('standard_id', $data['standard_id'] ?? null)->max('sort_order') ?? 0;

            $q = Question::create([
                'standard_id' => $data['standard_id'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'label'       => $item->question_text,
                'type'        => $type,
                'reference'   => $data['reference'] ?? null,
                'is_required' => true,
                'is_active'   => true,
                'sort_order'  => $maxOrder + 1,
            ]);

            if (in_array($q->type, ['select', 'radio'], true)) {
                $this->syncYesNoOptions($q);
            }
        }

        return redirect()
            ->route('admin.questions.index')
            ->with('success', $items->count() . ' soal berhasil diimport dari Checklist AMI.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('admin.questions.index')->with('success', 'Soal dihapus.');
    }

    private function syncYesNoOptions(Question $question): void
    {
        $question->options()->delete();

        $question->options()->createMany([['label' => 'Ya', 'value' => 'ya', 'sort_order' => 1], ['label' => 'Tidak', 'value' => 'tidak', 'sort_order' => 2]]);
    }
}
