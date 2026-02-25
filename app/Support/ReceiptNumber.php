<?php

namespace App\Support;

use App\Models\Donation;

class ReceiptNumber
{
    public static function forDonation(Donation $donation): string
    {
        return 'HOL-'.now()->format('Y').'-'.str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT);
    }
}

