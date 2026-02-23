<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finding extends Model
{
    protected $fillable = [
        'assessment_id',
        'question_id',
        'standard_id',
        'audit_area_ids',
        'sequence',
        'code',
        'title',
        'description',
        'severity',
        'condition_desc',
        'root_cause',
        'impact',
        'recommendation',
        'category',
        'corrective_plan',
        'due_date',
    ];

    protected $casts = [
        'audit_area_ids' => 'array',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function standard(): BelongsTo
    {
        return $this->belongsTo(Standard::class);
    }

    public function getAuditAreaNamesAttribute(): string
    {
        if (empty($this->audit_area_ids)) {
            return '-';
        }

        return AuditArea::whereIn('id', $this->audit_area_ids)
            ->pluck('name')
            ->join(', ');
    }

    public function getNomorSuratAttribute(): ?string
    {
        $parts = $this->parseCodeParts();

        return count($parts) >= 2 ? $parts[1] : null;
    }

    public function getSuratPrefixAttribute(): ?string
    {
        $parts = $this->parseCodeParts();

        return $parts[0] ?? null;
    }

    private function parseCodeParts(): array
    {
        return array_values(array_filter(explode('/', (string) ($this->code ?? ''))));
    }
}
