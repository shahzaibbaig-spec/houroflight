<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volunteer extends Model
{
    protected $fillable = [
        'user_id',
        'expertise_subjects',
        'grade_levels',
        'availability',
        'lesson_format',
        'years_experience',
        'short_bio',
        'profile_photo_path',
        'show_photo_on_website',
        'show_on_website',
        'degree_details',
        'degree_document_path',
        'certificates_document_path',
        'awards',
        'teaching_profile_notes',
        'status',
    ];

    protected $casts = [
        'show_photo_on_website' => 'boolean',
        'show_on_website' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VolunteerDocument::class);
    }
}
