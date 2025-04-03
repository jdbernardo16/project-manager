<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Resource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Use a secure default password
            'role' => 'admin',
        ]);

        // 2. Project Manager User
        User::factory()->create([
            'name' => 'Project Manager',
            'email' => 'pm@example.com',
            'password' => Hash::make('password'),
            'role' => 'project_manager',
        ]);

        // 3. Resource Users (Create User and associated Resource profile)
        User::factory()->count(5)->create([
             'password' => Hash::make('password'),
             'role' => 'resource',
        ])->each(function ($user) {
            Resource::factory()->create([
                'user_id' => $user->id,
                // Factory can define default capacity, status, notes
            ]);
        });

        // Example of a specific resource user
         $specificResourceUser = User::factory()->create([
            'name' => 'Dev Resource 1',
            'email' => 'dev1@example.com',
            'password' => Hash::make('password'),
            'role' => 'resource',
        ]);
        Resource::factory()->create([
            'user_id' => $specificResourceUser->id,
            'capacity' => 8.0, // Specific capacity
            'availability_status' => 'available',
        ]);

    }
}
