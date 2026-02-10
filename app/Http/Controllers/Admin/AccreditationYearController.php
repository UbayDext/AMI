<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use Illuminate\Http\Request;

class AccreditationYearController extends Controller
{
    public function index()
    {
        $years = AccreditationYear::withCount('assessments')
            ->orderBy('year', 'desc')
            ->get();

        return view('admin.accreditation-years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.accreditation-years.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100', 'unique:accreditation_years,year'],
        ]);

        AccreditationYear::create($data);

        return redirect()
            ->route('admin.accreditation-years.index')
            ->with('success', 'Tahun akreditasi berhasil ditambahkan.');
    }

    public function show(AccreditationYear $accreditation_year)
    {
        $accreditation_year->load(['assessments.assessor', 'assessments.accreditationYear']);

        return view('admin.accreditation-years.show', compact('accreditation_year'));
    }

    public function edit(AccreditationYear $accreditation_year)
    {
        return view('admin.accreditation-years.edit', compact('accreditation_year'));
    }

    public function update(Request $request, AccreditationYear $accreditation_year)
    {
        $data = $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100', 'unique:accreditation_years,year,' . $accreditation_year->id],
        ]);

        $accreditation_year->update($data);

        return back()->with('success', 'Tahun akreditasi berhasil diupdate.');
    }

    public function destroy(AccreditationYear $accreditation_year)
    {
        if ($accreditation_year->assessments()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus tahun yang masih memiliki assessment.');
        }

        $accreditation_year->delete();

        return redirect()
            ->route('admin.accreditation-years.index')
            ->with('success', 'Tahun akreditasi berhasil dihapus.');
    }
}
