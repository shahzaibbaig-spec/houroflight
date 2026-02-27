<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'requirements',
        'passing_score',
        'is_active',
    ];

    protected $casts = [
        'passing_score' => 'integer',
        'is_active' => 'boolean',
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(CourseLesson::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
