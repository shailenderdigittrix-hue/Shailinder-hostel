<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomChangeRequestSeeder extends Seeder
{
    public function run()
    {
        $dummyRequests = [
            [
                'student_id' => 1,
                'current_room_id' => 1,
                'desired_room_id' => 4,
                'reason' => 'Needs quieter room.',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'current_room_id' => 3,
                'desired_room_id' => 6,
                'reason' => 'Wants to move closer to friends.',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 5,
                'current_room_id' => 10,
                'desired_room_id' => 13,
                'reason' => 'Room is too small.',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more dummy rows as needed
        ];

        DB::table('room_change_requests')->insert($dummyRequests);
    }
}
