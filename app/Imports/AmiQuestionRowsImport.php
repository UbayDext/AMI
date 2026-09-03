<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AmiQuestionRowsImport implements ToCollection
{
    public function collection(Collection $collection): void
    {
        // Parsing and validation are handled by AmiQuestionController so every
        // source row can be preserved in the staging table for admin preview.
    }
}
