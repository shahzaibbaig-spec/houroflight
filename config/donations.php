<?php

return [
    'rates_to_usd' => [
        'USD' => 1.0,
        'PKR' => 0.0036,
        'GBP' => 1.27,
        'EUR' => 1.09,
    ],

    // Base value requested by project: 1 Chromebook ~= PKR 20,000.
    // Other currencies are derived through rates_to_usd normalization.
    'chromebook_cost_pkr' => 20000,
    'device_cost_usd' => 80,
    'training_hour_cost_usd' => 10,
    'classroom_kit_cost_usd' => 500,
];
