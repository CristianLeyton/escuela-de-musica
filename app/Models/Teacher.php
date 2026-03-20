<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'specialization',
        'experience_years',
        'bio',
        'is_active',
        'min_age',
        'max_age',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'experience_years' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instruments()
    {
        return $this->belongsToMany(Instrument::class, 'teacher_instrument');
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
