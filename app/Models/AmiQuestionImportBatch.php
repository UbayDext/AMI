<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AmiQuestionImportBatch extends Model
{
    protected $fillable = ['standard_id', 'original_filename', 'file_hash', 'status', 'total_rows', 'valid_rows', 'invalid_rows', 'duplicate_rows', 'imported_rows', 'created_by', 'error_message', 'completed_at'];
    protected $casts = ['completed_at' => 'datetime'];
    public function rows(): HasMany { return $this->hasMany(AmiQuestionImportRow::class, 'import_batch_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function standard(): BelongsTo { return $this->belongsTo(Standard::class); }
}
