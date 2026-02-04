<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Standard;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with('standard')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(30);

        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        $standards = Standard::orderBy('code')->get();
        $types = ['text', 'textarea', 'number', 'select', 'radio', 'checkbox', 'file'];

        return view('admin.questions.create', compact('standards', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'standard_id' => ['nullable', 'exists:standards,id'],
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

        return redirect()
            ->route('admin.questions.edit', $q)
            ->with('success', 'Soal dibuat.');
    }

    public function edit(Question $question)
    {
        $question->load('options');
        $standards = Standard::orderBy('code')->get();
        $types = ['text', 'textarea', 'number', 'select', 'radio', 'checkbox', 'file'];

        return view('admin.questions.edit', compact('question', 'standards', 'types'));
    }

   public function update(Request $request, Question $question)
{
    $data = $request->validate([
        'standard_id' => ['nullable', 'exists:standards,id'],
        'label' => ['required', 'string', 'max:255'],
        'type' => ['required', 'in:text,textarea,number,select,radio,checkbox,file'],
        'reference' => ['nullable', 'string', 'max:5000'],
        'is_required' => ['nullable', 'boolean'],
        'sort_order' => ['nullable', 'integer', 'min:0'],
        'is_active' => ['nullable', 'boolean'],
    ]);

    $question->standard_id = $data['standard_id'] ?? null;
    $question->label       = $data['label'];
    $question->type        = $data['type'];
    $question->reference   = $data['reference'] ?? null; // ✅ penting
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

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Soal dihapus.');
    }

    private function syncYesNoOptions(Question $question): void
    {
        $question->options()->delete();

        $question->options()->createMany([
            ['label' => 'Ya', 'value' => 'ya', 'sort_order' => 1],
            ['label' => 'Tidak', 'value' => 'tidak', 'sort_order' => 2],
        ]);
    }
}
