<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name' => 'Johor'],
            ['name' => 'Kedah'],
            ['name' => 'Kelantan'],
            ['name' => 'Malacca'],
            ['name' => 'Negeri Sembilan'],
            ['name' => 'Pahang'],
            ['name' => 'Penang'],
            ['name' => 'Perak'],
            ['name' => 'Perlis'],
            ['name' => 'Sabah'],
            ['name' => 'Sarawak'],
            ['name' => 'Selangor'],
            ['name' => 'Terengganu'],
            ['name' => 'Federal Territory of Kuala Lumpur'],
            ['name' => 'Federal Territory of Labuan'],
            ['name' => 'Federal Territory of Putrajaya'],
        ];

        DB::table('states')->insert($states);
    }
}
