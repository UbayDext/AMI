<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AmiQuestionTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\AmiQuestionRowsImport;
use App\Models\AmiChecklistQuestion;
use App\Models\AmiQuestionImportBatch;
use App\Models\AmiQuestionImportRow;
use App\Models\AuditArea;
use App\Models\Prodi;
use App\Models\Standard;
use App\Models\OnboardingProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AmiQuestionController extends Controller
{
    public function index(): View
    {
        $standards = Standard::withCount(['questions'])->orderByRaw('LENGTH(code), code')->get();
        $questionCounts = AmiChecklistQuestion::selectRaw('standard_code, count(*) as total')->groupBy('standard_code')->pluck('total', 'standard_code');
        $batches = AmiQuestionImportBatch::with('creator')->latest()->limit(8)->get();
        $prodis = Prodi::where('is_active', true)->orderBy('name')->get();
        $auditAreas = AuditArea::where('is_active', true)->orderBy('sort_order')->orderBy('code')->get();
        $previewId = session('preview_batch_id') ?: request('batch');
        $previewBatch = $previewId ? AmiQuestionImportBatch::with(['standard', 'rows' => fn ($query) => $query->limit(100)])->find($previewId) : null;
        $amiQuestionOnboarding = request()->user()->hasRole('admin')
            ? OnboardingProgress::firstOrCreate(
                ['user_id' => request()->user()->id, 'onboarding_key' => 'admin_ami_questions', 'version' => 1],
                ['current_step' => 0, 'status' => 'started', 'started_at' => now(), 'last_seen_at' => now()]
            ) : null;
        return view('admin.ami.questions.index', compact('standards', 'questionCounts', 'batches', 'prodis', 'auditAreas', 'previewBatch', 'amiQuestionOnboarding'));
    }

    public function data(Request $request): JsonResponse
    {
        $data = $request->validate(['standard_id' => 'required|exists:standards,id', 'prodi_id' => 'nullable|exists:prodis,id', 'search' => 'nullable|string|max:100', 'page' => 'nullable|integer|min:1']);
        $standard = Standard::findOrFail($data['standard_id']);
        $codes = array_unique([$standard->code, $this->normalizeCode($standard->code)]);
        $questions = AmiChecklistQuestion::with(['prodis:id,code,name', 'auditArea:id,code,name'])
            ->where(fn ($q) => $q->where('standard_id', $standard->id)->orWhereIn('standard_code', $codes))
            ->when($data['prodi_id'] ?? null, fn ($q, $id) => $q->where(fn ($scope) => $scope->whereDoesntHave('prodis')->orWhereHas('prodis', fn ($prodi) => $prodi->whereKey($id))))
            ->when($data['search'] ?? null, fn ($q, $search) => $q->where('question_text', 'like', "%{$search}%"))
            ->orderBy('sort_order')->orderBy('question_number')->paginate(30);
        return response()->json($questions);
    }

    public function upload(Request $request): RedirectResponse
    {
        $data = $request->validate(['standard_id' => ['required', 'exists:standards,id'], 'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120']]);
        $selectedStandard = Standard::findOrFail($data['standard_id']);
        $file = $request->file('file');
        $batch = AmiQuestionImportBatch::create(['standard_id' => $selectedStandard->id, 'original_filename' => $file->getClientOriginalName(), 'file_hash' => hash_file('sha256', $file->getRealPath()), 'status' => 'validating', 'created_by' => $request->user()->id]);
        try {
            $sheets = Excel::toCollection(new AmiQuestionRowsImport, $file);
            $records = collect();
            $hasRecognizedSheet = false;
            foreach ($sheets as $sheet) {
                $heading = $sheet->shift()?->map(fn ($value) => $this->importHeading((string) $value))->all() ?? [];
                if (! in_array('question_text', $heading, true)) {
                    continue;
                }
                $hasRecognizedSheet = true;
                foreach ($sheet as $offset => $values) {
                    $records->push(['heading' => $heading, 'values' => $values, 'row_number' => $offset + 2]);
                }
            }
            if (! $hasRecognizedSheet) {
                $batch->update(['status' => 'failed', 'error_message' => 'Kolom Kode Standar dan Pertanyaan AMI tidak ditemukan.']);

                return back()->with('error', 'Template tidak dikenali. Pastikan kolom Pertanyaan AMI tidak diubah.');
            }
            $prodis = Prodi::all()->keyBy(fn ($item) => strtoupper($item->code));
            $auditAreas = AuditArea::where('is_active', true)->get()->keyBy(fn ($item) => strtoupper($item->code));
            $counts = ['total' => 0, 'valid' => 0, 'invalid' => 0, 'duplicate' => 0];
            $seen = [];
            $nextNumbers = [];
            foreach ($records as $record) {
                $heading = $record['heading'];
                $values = $record['values'];
                if ($values->filter(fn ($v) => $v !== null && $v !== '')->isEmpty()) continue;
                $counts['total']++;
                $row = array_combine($heading, array_slice(array_pad($values->all(), count($heading), null), 0, count($heading)));
                $code = strtoupper(trim((string) ($row['standard_code'] ?? $selectedStandard->code)));
                $standard = $selectedStandard;
                $text = trim((string) ($row['question_text'] ?? ''));
                $rawProdis = trim((string) ($row['prodi_codes'] ?? ''));
                $prodiCodes = collect(preg_split('/[|,;]/', $rawProdis, -1, PREG_SPLIT_NO_EMPTY))
                    ->map(fn ($v) => strtoupper(trim($v)))
                    ->reject(fn ($value) => in_array($value, ['SEMUA', 'ALL', '-'], true))
                    ->unique()->values();
                $errors = [];
                if ($this->normalizeCode($code) !== $this->normalizeCode($selectedStandard->code)) $errors[] = "File ini bukan untuk standar {$selectedStandard->code}";
                if ($text === '') $errors[] = 'Pertanyaan AMI wajib diisi';
                foreach ($prodiCodes as $prodiCode) if (! $prodis->has($prodiCode)) $errors[] = "Program Studi {$prodiCode} tidak ditemukan";
                $areaCode = strtoupper(trim((string) ($row['bidang'] ?? '')));
                $auditArea = $auditAreas->get($areaCode);
                if ($areaCode === '') $errors[] = 'Kode Bidang wajib diisi';
                elseif (! $auditArea) $errors[] = "Kode Bidang {$areaCode} tidak ditemukan di Bank Bidang";
                $providedNumber = filter_var($row['question_number'] ?? null, FILTER_VALIDATE_INT);
                if ($standard && ($providedNumber === false || $providedNumber < 1)) {
                    $nextNumbers[$standard->id] ??= AmiChecklistQuestion::where('standard_id', $standard->id)
                        ->orWhere('standard_code', $this->normalizeCode($standard->code))->max('question_number') ?? 0;
                    $number = ++$nextNumbers[$standard->id];
                } else {
                    $number = $providedNumber;
                }
                $hash = $standard ? hash('sha256', $standard->id.'|'.$number.'|'.Str::lower($text)) : null;
                $normalizedCode = $standard ? $this->normalizeCode($standard->code) : $code;
                $duplicateKey = $normalizedCode.'|'.$number;
                $textKey = $normalizedCode.'|'.Str::lower($text);
                $duplicate = $standard && (isset($seen[$duplicateKey]) || isset($seen[$textKey]) || AmiChecklistQuestion::where(fn ($q) => $q->where('standard_id', $standard->id)->orWhere('standard_code', $normalizedCode))->where(fn ($q) => $q->where('source_hash', $hash)->orWhere('question_number', $number)->orWhere('question_text', $text))->exists());
                $status = $errors ? 'invalid' : ($duplicate ? 'duplicate' : 'valid');
                if (! $errors) { $seen[$duplicateKey] = true; $seen[$textKey] = true; }
                $counts[$status]++;
                AmiQuestionImportRow::create(['import_batch_id'=>$batch->id,'row_number'=>$record['row_number'],'standard_code'=>$selectedStandard->code,'standard_id'=>$standard->id,'audit_area_id'=>$auditArea?->id,'question_number'=>$number ?: null,'question_text'=>$text ?: null,'reference'=>$row['reference'] ?? null,'bidang'=>$areaCode ?: null,'auditi'=>$auditArea?->name,'prodi_codes'=>$prodiCodes->all(),'is_required'=>$this->boolean($row['is_required'] ?? 1),'is_active'=>$this->boolean($row['is_active'] ?? 1),'sort_order'=>(int)($row['sort_order'] ?? $number ?: 0),'source_hash'=>$hash,'status'=>$status,'validation_errors'=>$errors ?: null]);
            }
            $batch->update(['status'=>'ready','total_rows'=>$counts['total'],'valid_rows'=>$counts['valid'],'invalid_rows'=>$counts['invalid'],'duplicate_rows'=>$counts['duplicate']]);
            return back()->with('success', "File selesai diperiksa: {$counts['valid']} baris siap dibuat.")->with('preview_batch_id', $batch->id);
        } catch (\Throwable $e) {
            $batch->update(['status'=>'failed','error_message'=>Str::limit($e->getMessage(), 1000)]);
            return back()->with('error', 'File gagal dibaca: '.$e->getMessage());
        }
    }

    public function generate(AmiQuestionImportBatch $batch): RedirectResponse
    {
        abort_unless(in_array($batch->status, ['ready', 'failed'], true), 422);
        $imported = 0;
        DB::transaction(function () use ($batch, &$imported) {
            foreach ($batch->rows()->where('status', 'valid')->cursor() as $row) {
                $standard = Standard::find($row->standard_id);
                $question = AmiChecklistQuestion::create(['standard_id'=>$standard->id,'audit_area_id'=>$row->audit_area_id,'standard_code'=>$this->normalizeCode($standard->code),'standard_name'=>$standard->name,'bidang'=>$row->bidang ?: '-','auditi'=>$row->auditi,'question_number'=>$row->question_number,'question_text'=>$row->question_text,'reference'=>$row->reference,'is_required'=>$row->is_required,'is_active'=>$row->is_active,'sort_order'=>$row->sort_order,'source_hash'=>$row->source_hash,'import_batch_id'=>$batch->id,'created_by'=>auth()->id()]);
                $question->prodis()->sync(Prodi::whereIn('code', $row->prodi_codes ?? [])->pluck('id'));
                $row->update(['status'=>'imported','generated_question_id'=>$question->id]); $imported++;
            }
            $batch->update(['status'=>'completed','imported_rows'=>$imported,'completed_at'=>now()]);
        });
        return back()->with('success', "{$imported} pertanyaan AMI berhasil dibuat.");
    }

    public function template(Request $request): BinaryFileResponse
    {
        $data = $request->validate(['standard_id' => ['required', 'exists:standards,id']]);
        $standard = Standard::findOrFail($data['standard_id']);
        $auditAreas = AuditArea::where('is_active', true)->orderBy('sort_order')->orderBy('code')->get();
        return Excel::download(new AmiQuestionTemplateExport($standard, $auditAreas), 'template-pertanyaan-ami-'.$standard->code.'.xlsx');
    }

    private function normalizeCode(string $code): string { return preg_match('/^ST([1-9])$/i', $code, $m) ? 'ST0'.$m[1] : strtoupper($code); }
    private function boolean(mixed $value): bool { return in_array(Str::lower(trim((string)$value)), ['1','ya','yes','true','aktif'], true); }

    private function importHeading(string $heading): string
    {
        $key = Str::snake(trim($heading));

        return [
            'kode_standar' => 'standard_code',
            'pertanyaan' => 'question_text',
            'pertanyaan_ami' => 'question_text',
            'pertanyaan_a_m_i' => 'question_text',
            'kode_bidang' => 'bidang',
            'ditujukan_kepada' => 'auditi',
            'program_studi' => 'prodi_codes',
            'kode_prodi' => 'prodi_codes',
            'referensi' => 'reference',
        ][$key] ?? $key;
    }
}
