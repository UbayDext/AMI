<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use App\Models\AuditorDecree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuditorDecreeController extends Controller
{
    public function index(Request $request)
    {
        $years = AccreditationYear::orderByDesc('year')->get();

        $q = AuditorDecree::query()->with('year')->orderByDesc('id');

        if ($request->filled('year')) {
            $q->where('accreditation_year_id', $request->integer('year'));
        }

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $q->where(function ($qq) use ($s) {
                $qq->where('period_label', 'like', "%{$s}%")
                   ->orWhere('decree_number', 'like', "%{$s}%")
                   ->orWhere('original_name', 'like', "%{$s}%");
            });
        }

        $items = $q->paginate(10)->withQueryString();

        return view('admin.auditor-decrees.index', compact('items', 'years'));
    }

    public function create()
    {
        $years = AccreditationYear::orderByDesc('year')->get();
        return view('admin.auditor-decrees.create', compact('years'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'accreditation_year_id' => ['nullable', 'exists:accreditation_years,id'],
            'period_label' => ['nullable', 'string', 'max:100'],
            'decree_number' => ['nullable', 'string', 'max:150'],
            'decree_date' => ['nullable', 'date'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date'],
            'file' => ['nullable', 'file', 'max:10240'], // 10MB
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('auditor-decrees', 'public');

            $data['file_path'] = $path;
            $data['original_name'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getClientMimeType();
            $data['size'] = $file->getSize();
        }

        AuditorDecree::create($data);

        return redirect()->route('admin.auditor-decrees.index')->with('success', 'Data SK Auditor dibuat.');
    }

    public function edit(AuditorDecree $auditorDecree)
    {
        $years = AccreditationYear::orderByDesc('year')->get();
        return view('admin.auditor-decrees.edit', compact('auditorDecree', 'years'));
    }

    public function update(Request $request, AuditorDecree $auditorDecree)
    {
        $data = $request->validate([
            'accreditation_year_id' => ['nullable', 'exists:accreditation_years,id'],
            'period_label' => ['nullable', 'string', 'max:100'],
            'decree_number' => ['nullable', 'string', 'max:150'],
            'decree_date' => ['nullable', 'date'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date'],
            'file' => ['nullable', 'file', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        if ($request->hasFile('file')) {
            // hapus file lama kalau ada
            if ($auditorDecree->file_path) {
                Storage::disk('public')->delete($auditorDecree->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('auditor-decrees', 'public');

            $data['file_path'] = $path;
            $data['original_name'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getClientMimeType();
            $data['size'] = $file->getSize();
        }

        $auditorDecree->update($data);

        return back()->with('success', 'Data SK Auditor diupdate.');
    }

    public function destroy(AuditorDecree $auditorDecree)
    {
        if ($auditorDecree->file_path) {
            Storage::disk('public')->delete($auditorDecree->file_path);
        }

        $auditorDecree->delete();

        return redirect()->route('admin.auditor-decrees.index')->with('success', 'Data SK Auditor dihapus.');
    }
}
