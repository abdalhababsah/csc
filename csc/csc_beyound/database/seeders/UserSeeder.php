<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'teacher',
                'email' => 'teacher@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'student',
                'activated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'student',
                'email' => 'student@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'activated' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
