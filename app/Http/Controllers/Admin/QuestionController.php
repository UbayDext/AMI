<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        $questions = $query->paginate(100)->appends($request->query());

        // Nested grouping: Category -> Standard -> Questions
        $nestedGroups = $questions->getCollection()
            ->groupBy(fn($q) => $q->category_id ?? 0)
            ->map(fn($catItems) => $catItems->groupBy(fn($q) => $q->standard_id ?? 0));

        $categories = QuestionCategory::orderBy('name')->get();
        $standards = Standard::all()->sortBy(fn($s) => (int) filter_var($s->code, FILTER_SANITIZE_NUMBER_INT));

        return view('admin.questions.index', compact('questions', 'nestedGroups', 'categories', 'standards'));
    }


    public function create()
    {
        // Manual sorting to ensure ST1, ST2... ST10 order
        $standards = Standard::all()->sortBy(function ($s) {
            return (int) filter_var($s->code, FILTER_SANITIZE_NUMBER_INT);
        });

        $categories = QuestionCategory::orderBy('name')->orderBy('id')->get();

        $types = ['text', 'textarea', 'number', 'select', 'radio', 'checkbox', 'file'];

        return view('admin.questions.create', compact('standards', 'categories', 'types'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'standard_id' => ['nullable', 'exists:standards,id'],
            'category_id' => ['nullable', 'exists:question_categories,id'], // ✅
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:text,textarea,number,select,radio,checkbox,file'],
            'reference' => ['nullable', 'string', 'max:5000'],
            'is_required' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_required'] = (bool) ($data['is_required'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

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
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:text,textarea,number,select,radio,checkbox,file'],
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
