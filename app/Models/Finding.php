<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finding extends Model
{
    protected $fillable = ['assessment_id', 'question_id', 'standard_id', 'audit_area_ids', 'sequence', 'code', 'title', 'description', 'severity', 'condition_desc', 'root_cause', 'impact', 'recommendation', 'category', 'corrective_plan', 'due_date'];

    protected $casts = [
        'audit_area_ids' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function getNomorSuratAttribute(): ?string
    {
        $code = (string) ($this->code ?? '');

        // split by "/"
        $parts = array_values(array_filter(explode('/', $code)));

        // butuh minimal: [0]=PTK, [1]=002, ...
        if (count($parts) < 2) {
            return null;
        }

        // nomor surat adalah segment ke-2
        return $parts[1] ?? null;
    }

    /**
     * (Opsional) Ambil prefix surat: PTK/002/... => PTK
     */
    public function getSuratPrefixAttribute(): ?string
    {
        $code = (string) ($this->code ?? '');
        $parts = array_values(array_filter(explode('/', $code)));

        return $parts[0] ?? null;
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function getAuditAreaNamesAttribute()
    {
        if (empty($this->audit_area_ids)) return '-';
        return AuditArea::whereIn('id', $this->audit_area_ids)->pluck('name')->join(', ');
    }
}
