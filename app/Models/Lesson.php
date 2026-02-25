<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    protected $fillable = [
        'user_id',
        'volunteer_id',
        'title',
        'subject',
        'grade_min',
        'grade_max',
        'lesson_type',
        'delivery_mode',
        'youtube_url',
        'video_path',
        'document_path',
        'description',
        'learning_objectives',
        'language',
        'duration_minutes',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'published_at',
        'views_count',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
