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
            ['name' => 'Draft'],
            ['name' => 'Pending'],
            ['name' => 'Published'],
           
        ];

        DB::table('states')->insert($states);
    }
}
