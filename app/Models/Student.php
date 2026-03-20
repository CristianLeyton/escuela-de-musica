<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'birth_date',
        'phone',
        'emergency_contact',
        'medical_notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date',
    ];

    protected $appends = ['age'];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'enrollments');
    }

    public function getAgeAttribute(): ?int
    {
        if (! $this->birth_date) {
            return null;
        }

        $date = $this->birth_date instanceof Carbon
            ? $this->birth_date
            : Carbon::parse($this->birth_date);

        return $date->age;
    }
}
