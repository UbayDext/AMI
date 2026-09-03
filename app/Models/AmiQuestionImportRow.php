<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmiQuestionImportRow extends Model
{
    protected $fillable = ['import_batch_id', 'row_number', 'standard_code', 'standard_id', 'audit_area_id', 'question_number', 'question_text', 'reference', 'bidang', 'auditi', 'prodi_codes', 'is_required', 'is_active', 'sort_order', 'source_hash', 'status', 'validation_errors', 'generated_question_id'];
    protected $casts = ['prodi_codes' => 'array', 'validation_errors' => 'array', 'is_required' => 'boolean', 'is_active' => 'boolean'];
}
