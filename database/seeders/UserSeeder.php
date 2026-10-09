<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Keuangan',
            'email' => 'admin@noc.id',
            'password' => Hash::make('password'),
            'currency' => 'IDR',
            'phone' => '081234567890',
        ]);
    }
}
