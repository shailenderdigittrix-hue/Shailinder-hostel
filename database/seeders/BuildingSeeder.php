<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example: You already have hostels with ID 1 and 2
        $buildings = [
            [
                'hostel_id' => 1, // Alpha Boys Hostel
                'name' => 'Block A',
                'number_of_floors' => 3,
            ],
            [
                'hostel_id' => 1,
                'name' => 'Block B',
                'number_of_floors' => 4,
            ],
            [
                'hostel_id' => 2, // Beta Girls Hostel
                'name' => 'Block C',
                'number_of_floors' => 2,
            ],
            [
                'hostel_id' => 2,
                'name' => 'Block D',
                'number_of_floors' => 3,
            ],
        ];

        foreach ($buildings as $building) {
            Building::create($building);
        }
    }
}
