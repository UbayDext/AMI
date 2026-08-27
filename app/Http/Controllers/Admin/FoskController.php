<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccreditationYear;
use App\Models\FoskCriteria;
use App\Models\FoskDocument;
use App\Models\FoskEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoskController extends Controller
{
    /**
     * Landing page: 9 criteria cards with progress.
     */
    public function index(Request $request)
    {
        $yearId = $request->query('year_id');
        $years = AccreditationYear::orderByDesc('year')->get();

        if (!$yearId && $years->isNotEmpty()) {
            $yearId = $years->first()->id;
        }

        $criteria = FoskCriteria::orderBy('sort_order')
            ->withCount([
                'documents as total_docs' => fn($q) => $q->where('accreditation_year_id', $yearId),
                'documents as final_docs' => fn($q) => $q->where('accreditation_year_id', $yearId)->where('status', 'final'),
                'documents as lkpt_count' => fn($q) => $q->where('accreditation_year_id', $yearId)->where('type', 'lkpt'),
                'documents as lkpd_count' => fn($q) => $q->where('accreditation_year_id', $yearId)->where('type', 'lkpd'),
            ])
            ->get();

        return view('admin.fosk.index', compact('criteria', 'years', 'yearId'));
    }

    /**
     * Detail page: documents for a specific criteria.
     */
    public function show(Request $request, FoskCriteria $criteria)
    {
        $yearId = $request->query('year_id');
        $years = AccreditationYear::orderByDesc('year')->get();

        if (!$yearId && $years->isNotEmpty()) {
            $yearId = $years->first()->id;
        }

        $type = $request->query('type', 'lkpt');

        $documents = FoskDocument::where('criteria_id', $criteria->id)
            ->where('accreditation_year_id', $yearId)
            ->where('type', $type)
            ->with(['evidences', 'creator'])
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        return view('admin.fosk.show', compact('criteria', 'documents', 'years', 'yearId', 'type'));
    }

    /**
     * Store a new document.
     */
    public function storeDocument(Request $request, FoskCriteria $criteria)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'type' => 'required|in:lkpt,lkpd',
            'year_id' => 'required|exists:accreditation_years,id',
            'description' => 'nullable|string',
            'data_value' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
        ]);

        FoskDocument::create([
            'criteria_id' => $criteria->id,
            'accreditation_year_id' => $request->year_id,
            'type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'data_value' => $request->data_value,
            'pic' => $request->pic,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Dokumen berhasil ditambahkan.');
    }

    /**
     * Update a document.
     */
    public function updateDocument(Request $request, FoskDocument $document)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'data_value' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,review,final',
        ]);

        $document->update($request->only(['title', 'description', 'data_value', 'pic', 'status']));

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    /**
     * Delete a document.
     */
    public function destroyDocument(FoskDocument $document)
    {
        // Delete associated files
        foreach ($document->evidences as $ev) {
            if ($ev->file_path) {
                Storage::disk('public')->delete($ev->file_path);
            }
        }

        $document->delete();

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Store evidence (file upload or link).
     */
    public function storeEvidence(Request $request, FoskDocument $document)
    {
        $request->validate([
            'title' => 'required|string|max:500',
            'link_url' => 'nullable|url|max:2000',
            'file' => 'nullable|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,zip,rar',
        ]);

        $data = [
            'document_id' => $document->id,
            'title' => $request->title,
            'link_url' => $request->link_url,
            'uploaded_by' => auth()->id(),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('fosk-evidence', 'public');
            $data['original_name'] = $file->getClientOriginalName();
            $data['mime_type'] = $file->getMimeType();
            $data['size'] = $file->getSize();
        }

        FoskEvidence::create($data);

        return back()->with('success', 'Bukti berhasil ditambahkan.');
    }

    /**
     * Delete evidence.
     */
    public function destroyEvidence(FoskEvidence $evidence)
    {
        if ($evidence->file_path) {
            Storage::disk('public')->delete($evidence->file_path);
        }

        $evidence->delete();

        return back()->with('success', 'Bukti berhasil dihapus.');
    }
}
