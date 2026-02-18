<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quest extends Model
{
    protected $fillable = [
        'classroom_id',
        'teacher_id',
        'title',
        'description',
        'type',
        'difficulty',
        'content',
        'estimated_time',
        'reward_points',
        'status',
        'available_until',
    ];

    protected $casts = [
        'content' => 'array',
        'available_until' => 'datetime',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
