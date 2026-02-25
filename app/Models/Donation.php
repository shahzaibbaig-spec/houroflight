<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'user_id',
        'donor_name',
        'donor_email',
        'amount',
        'currency',
        'donation_type',
        'status',
        'verified_at',
        'verified_by',
        'receipt_number',
        'receipt_sent_at',
        'receipt_pdf_path',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'stripe_subscription_id',
        'receipt_url',
        'payment_proof_path',
        'impact_snapshot',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'impact_snapshot' => 'array',
        'verified_at' => 'datetime',
        'receipt_sent_at' => 'datetime',
    ];
}
