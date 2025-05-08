<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'tes',
            'email' => 'tes@gmail.com',
            'password' => Hash::make('tes123'), 
        ]);

        User::factory()->count(5)->create();
    }
}
