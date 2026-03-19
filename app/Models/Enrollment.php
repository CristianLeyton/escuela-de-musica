<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_model_id',
        'student_id',
        'enrollment_date',
        'status',
        'grade',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'grade' => 'decimal:2',
    ];

    // Relaciones
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
