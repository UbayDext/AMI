<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class QuestionCategoryController extends Controller
{
    public function index()
    {
        $categories = QuestionCategory::query()
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // supaya gampang render bertingkat di blade
        $tree = $this->buildTree($categories);

        return view('admin.question-categories.index', compact('categories', 'tree'));
    }

    public function create()
    {
        $parents = QuestionCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $tree = $this->buildTree($parents);

        return view('admin.question-categories.create', compact('parents', 'tree'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'parent_id'   => ['nullable', 'exists:question_categories,id'],
            'code'        => ['nullable', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active']  = (bool) ($data['is_active'] ?? true);

        QuestionCategory::create($data);

        return redirect()
            ->route('admin.question-categories.index')
            ->with('success', 'Kategori dibuat.');
    }

    public function edit(QuestionCategory $question_category)
    {
        $parents = QuestionCategory::query()
            ->where('id', '!=', $question_category->id) // cegah parent ke dirinya sendiri
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $tree = $this->buildTree($parents);

        return view('admin.question-categories.edit', [
            'category' => $question_category,
            'parents'  => $parents,
            'tree'     => $tree,
        ]);
    }

    public function update(Request $request, QuestionCategory $question_category)
    {
        $data = $request->validate([
            'parent_id'   => ['nullable', 'exists:question_categories,id'],
            'code'        => ['nullable', 'string', 'max:50'],
            'name'        => ['required', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        // cegah parent jadi anaknya sendiri (cycle sederhana)
        if (!empty($data['parent_id']) && (int)$data['parent_id'] === (int)$question_category->id) {
            return back()->withErrors(['parent_id' => 'Parent tidak boleh dirinya sendiri.'])->withInput();
        }

        $question_category->parent_id  = $data['parent_id'] ?? null;
        $question_category->code       = $data['code'] ?? null;
        $question_category->name       = $data['name'];
        $question_category->sort_order = (int) ($data['sort_order'] ?? 0);
        $question_category->is_active  = (bool) ($data['is_active'] ?? true);
        $question_category->save();

        return back()->with('success', 'Kategori diupdate.');
    }

    public function destroy(QuestionCategory $question_category)
    {
        // Optional: kalau punya children, kamu bisa blok delete.
        if ($question_category->children()->exists()) {
            return back()->with('success', 'Kategori punya sub-kategori. Hapus sub-kategori dulu.');
        }

        $question_category->delete();

        return redirect()
            ->route('admin.question-categories.index')
            ->with('success', 'Kategori dihapus.');
    }

    /**
     * Build tree dari collection flat (parent_id).
     */
    private function buildTree($categories)
    {
        $items = $categories->groupBy('parent_id');

        $walk = function ($parentId = null) use (&$walk, $items) {
            return ($items[$parentId] ?? collect())->map(function ($cat) use (&$walk) {
                return [
                    'item' => $cat,
                    'children' => $walk($cat->id),
                ];
            })->values();
        };

        return $walk(null);
    }
}
