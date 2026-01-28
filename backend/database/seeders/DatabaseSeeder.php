<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'User One',
            'email' => 'user1@gmail.com',
            'password' => '12345678',
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'User Two',
            'email' => 'user2@gmail.com',
            'password' => '12345678',
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'User Three',
            'email' => 'user3@gmail.com',
            'password' => '12345678',
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => 'User Four',
            'email' => 'user4@gmail.com',
            'password' => '12345678',
            'role' => 'user',
        ]);
    }
}
