<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerDocument extends Model
{
    protected $fillable = [
        'volunteer_id',
        'category',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }
}

