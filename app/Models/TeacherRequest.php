<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'grade_level',
        'notes',
    ];
}
