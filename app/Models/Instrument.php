<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instrument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_instrument');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassModel::class);
    }
}
