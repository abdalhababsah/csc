<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_name',
        'body',
        'status',
    ];

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_subject', 'subject_id', 'student_id')
                    ->withPivot(['mark', 'mid_mark'])
                    ->withTimestamps();
    }

}
