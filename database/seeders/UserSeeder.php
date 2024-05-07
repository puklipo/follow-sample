<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::factory()
            ->hasStatuses(10)
            ->create([
                'name' => 'Test User 1',
                'email' => 'test1@example.com',
            ]);

        $user2 = User::factory()
            ->hasStatuses(10)
            ->create([
                'name' => 'Test User 2',
                'email' => 'test2@example.com',
            ]);

        $user3 = User::factory()->create([
            'name' => 'Test User 3',
            'email' => 'test3@example.com',
        ]);

        $user1->followings()->toggle($user2);
    }
}
