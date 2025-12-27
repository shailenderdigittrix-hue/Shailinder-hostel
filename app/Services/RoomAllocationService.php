<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Room;
use App\Models\RoomAllocation;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoomAllocationService
{
    public function allocate(Student $student): ?Room
    {
        return DB::transaction(function () use ($student) {
            // Check if already allocated
            $existing = RoomAllocation::where('student_id', $student->id)
                ->whereNull('deallocated_at')
                ->first();

            if ($existing) {
                return null; // Already allocated
            }

            // Find available room
            $room = Room::where('gender', $student->gender)
                ->where('capacity', '>', DB::raw('occupied'))
                ->when($student->year, fn($q) => $q->where('year', $student->year))
                ->when($student->course, fn($q) => $q->where('course', $student->course))
                ->orderBy('occupied')
                ->lockForUpdate()
                ->first();

            if (!$room) {
                return null;
            }

            // Create allocation record
            RoomAllocation::create([
                'student_id' => $student->id,
                'room_id' => $room->id,
                'allocated_at' => Carbon::today(),
            ]);

            // Update room occupancy
            $room->occupied += 1;
            $room->save();

            return $room;
        });
    }
}
