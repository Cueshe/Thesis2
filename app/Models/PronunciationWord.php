<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PronunciationWord extends Model
{
    protected $fillable = [
        'text',
        'phonetic',
        'tips',
        'language',
        'mode',
        'created_by',
        'is_active',
        'order',
    ];

    protected $casts = [
        'tips' => 'array',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByMode($query, $mode)
    {
        return $query->where('mode', $mode);
    }
}
