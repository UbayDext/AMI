<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AmiAuditAreaTemplateSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly Collection $auditAreas) {}

    public function title(): string { return 'Bank Bidang'; }

    public function headings(): array { return ['Kode Bidang', 'Area Audit']; }

    public function array(): array
    {
        return $this->auditAreas->map(fn ($area) => [$area->code, $area->name])->all();
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:B'.max(2, $this->auditAreas->count() + 1));
        $sheet->getStyle('A1:B1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:B1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF2563EB');
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(65);

        return [];
    }
}
