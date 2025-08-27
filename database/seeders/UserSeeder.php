<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user dekan
        User::create([
            'name' => 'dekan',
            'email' => 'dekan@gmail.com',
            'password' => Hash::make('dekan123'),
            'role' => 'dekanat'
        ]);

        // Buat user staff
        User::create([
            'name' => 'staff',
            'email' => 'staff@gmail.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff'
        ]);

        User::create([
            'name' => 'staff',
            'email' => 'michaeljovan07@gmail.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff'
        ]);
    }
}
