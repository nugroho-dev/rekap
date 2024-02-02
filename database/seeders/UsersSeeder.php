<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'didik.ngr@gmail.com',
                'password'  => '$2y$10$mlvSl5JYkShr6FeizVQsTOFJ6mhy5GGZ5XqfmAbv4XbYZbwO9GLai'
            ],
            ['email' => 'didik.ngr@gmail.com',
            'password'  => '$2y$10$mlvSl5JYkShr6FeizVQsTOFJ6mhy5GGZ5XqfmAbv4XbYZbwO9GLai',]
        );
    }
}
