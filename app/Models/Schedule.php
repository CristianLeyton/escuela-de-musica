<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'teacher_id',
        'branch_id',
        'classroom_id',
        'instrument_id',
        'status',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relaciones
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }
}
