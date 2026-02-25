<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'school_name',
        'principal_name',
        'contact_email',
        'phone_number',
        'address',
        'city',
        'logo_path',
        'needs',
        'status',
    ];

    protected $casts = [
        'needs' => 'array',
    ];
}
