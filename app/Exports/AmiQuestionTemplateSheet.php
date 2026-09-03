<?php
namespace App\Exports;
use App\Models\Standard;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
class AmiQuestionTemplateSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly Standard $standard, private readonly Collection $auditAreas) {}

    public function title(): string { return 'Pertanyaan '.$this->standard->code; }

    public function headings(): array
    {
        return ['Pertanyaan AMI', 'Kode Bidang', 'Program Studi', 'Referensi'];
    }

    public function array(): array
    {
        return [[
            'Tulis pertanyaan untuk '.$this->standard->code.' — '.$this->standard->name,
            'WK1',
            'Semua',
            'Dokumen atau aturan yang menjadi rujukan',
        ]];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2'); $sheet->setAutoFilter('A1:D1');
        $sheet->getStyle('A1:D1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:D1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF2563EB');
        $sheet->getStyle('A1:D200')->getAlignment()->setVertical('top')->setWrapText(true);
        $sheet->getStyle('A2:D2')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFFFF7D6');
        $sheet->getColumnDimension('A')->setWidth(70); $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(24); $sheet->getColumnDimension('D')->setWidth(38);
        $lastAreaRow = max(2, $this->auditAreas->count() + 1);
        $validation = $sheet->getCell('B2')->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Kode bidang tidak valid');
        $validation->setError('Pilih kode yang tersedia pada dropdown atau sheet Bank Bidang.');
        $validation->setPromptTitle('Pilih Kode Bidang');
        $validation->setPrompt('Daftar ini berasal dari Bank Bidang.');
        $validation->setShowInputMessage(true);
        $validation->setFormula1("'Bank Bidang'!\$A\$2:\$A\$".$lastAreaRow);
        for ($row = 2; $row <= 200; $row++) {
            $sheet->getCell("B{$row}")->setDataValidation(clone $validation);
        }
        $sheet->getSheetView()->setZoomScale(90); return [];
    }
}
