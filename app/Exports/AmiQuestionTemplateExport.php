<?php
namespace App\Exports;
use App\Models\Standard;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
class AmiQuestionTemplateExport implements WithMultipleSheets
{
    public function __construct(private readonly Standard $standard, private readonly Collection $auditAreas) {}

    public function sheets(): array
    {
        return [
            new AmiQuestionTemplateSheet($this->standard, $this->auditAreas),
            new AmiAuditAreaTemplateSheet($this->auditAreas),
        ];
    }
}
