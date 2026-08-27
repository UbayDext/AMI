<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoskEvidence extends Model
{
    protected $table = 'fosk_evidences';

    protected $fillable = [
        'document_id',
        'title',
        'link_url',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(FoskDocument::class, 'document_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
