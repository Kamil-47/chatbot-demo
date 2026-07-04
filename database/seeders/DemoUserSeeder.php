<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Demo Admin',
            'email' => 'demo@demo.pl',
            'password' => Hash::make('demo123'),
            'is_admin' => true,
        ]);
    }
}
