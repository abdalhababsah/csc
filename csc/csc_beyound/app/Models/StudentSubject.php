<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Pivot
{
    use HasFactory;
    
    protected $table = 'student_subject';

    protected $fillable = [
        'student_id',
        'subject_id',
        'mark',
        'mid_mark',
    ];
}
