<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'schedule_id',
        'class_date',
        'duration_minutes',
        'status',
        'notes',
        'teacher_notes',
    ];

    protected $casts = [
        'class_date' => 'date',
        'duration_minutes' => 'integer',
    ];

    // Relaciones
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments');
    }

    // Accesores para obtener información del horario
    public function getTeacherAttribute()
    {
        return $this->schedule?->teacher;
    }

    public function getBranchAttribute()
    {
        return $this->schedule?->branch;
    }

    public function getClassroomAttribute()
    {
        return $this->schedule?->classroom;
    }

    public function getInstrumentAttribute()
    {
        return $this->schedule?->instrument;
    }
}
