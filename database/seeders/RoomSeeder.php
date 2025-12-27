<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $roomTypes = ['Single', 'Double', 'Triple'];

        $buildings = Building::with('hostel')->get();

        if ($buildings->isEmpty()) {
            $this->command->warn('No buildings found. Please seed buildings first.');
            return;
        }

        foreach ($buildings as $building) {
            $numberOfFloors = $building->number_of_floors ?? 1;

            // Loop over floors
            for ($floorNum = 1; $floorNum <= $numberOfFloors; $floorNum++) {
                // $floorLabel = $this->formatFloor($floorNum);
                $floorLabel = $floorNum;
                // Create 5 rooms per floor (customize as needed)
                for ($i = 1; $i <= 5; $i++) {
                    Room::create([
                        'hostel_id'   => $building->hostel_id,
                        'building_id' => $building->id,
                        'room_number' => strtoupper($building->name) . '-' . $floorNum . str_pad($i, 2, '0', STR_PAD_LEFT), // e.g., B15-101
                        'capacity'    => rand(1, 3),
                        'room_type'   => $roomTypes[array_rand($roomTypes)],
                        'floor'       => $floorLabel,
                        'is_active'   => 1,
                    ]);
                }
            }
        }
    }

    private function formatFloor($number)
    {
        return match ($number) {
            1 => '1st',
            2 => '2nd',
            3 => '3rd',
            default => $number . 'th',
        };
    }
}
