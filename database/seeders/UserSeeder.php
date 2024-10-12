<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password'),
            'gender' => 'Male',
            'date_of_birth' => '1990-01-01',
            'mailing_address' => '123 Test St, Test City',
            'country' => 'USA',
            'current_team_id' => null,
            'profile_photo_path' => null,
        ]);

        // User::factory(1)->create();
    }
}
