<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(
            [
                'email' => 'didik.ngr@gmail.com',
                'id_pegawai' => '1',
                'password'  => '$2y$10$mlvSl5JYkShr6FeizVQsTOFJ6mhy5GGZ5XqfmAbv4XbYZbwO9GLai'
            ],
        );
    }
}
