<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PronunciationStat extends Model
{
    protected $fillable = [
        'user_id',
        'total_practiced',
        'total_accuracy',
        'attempts',
        'streak',
    ];

    protected $casts = [
        'total_practiced' => 'integer',
        'total_accuracy' => 'integer',
        'attempts' => 'integer',
        'streak' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
