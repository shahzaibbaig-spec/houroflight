<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['name' => 'parizay'],
            [
                'email' => 'parizay@houroflight.com',
                'role' => 'admin',
                'password' => Hash::make('qazwsx09'),
            ]
        );
    }
}
