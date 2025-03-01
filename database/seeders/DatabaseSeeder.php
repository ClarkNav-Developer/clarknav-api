<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $superuserEmail = env('SUPERUSER_EMAIL', 'ClarkNav2024@gmail.com');
        $superuserPassword = env('SUPERUSER_PASSWORD', 'SuperClarkNav@2024!');

        // Check if the superuser already exists
        $superuser = User::where('email', $superuserEmail)->first();

        if (!$superuser) {
            // Create the superuser
            User::factory()->create([
                'first_name' => 'Super',
                'last_name' => 'User',
                'email' => $superuserEmail,
                'password' => Hash::make($superuserPassword),
                'isAdmin' => true,
                'isUser' => false,
            ]);
        }
    }
}
