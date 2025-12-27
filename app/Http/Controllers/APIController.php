<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Hostel;
use App\Models\Building;
use App\Models\Room;
use Illuminate\Support\Facades\Hash;



class APIController extends Controller
{
    public function getBuildings($hostelId)
    {
        return Building::where('hostel_id', $hostelId)->get(['id', 'name', 'number_of_floors']);
    }

    // Get distinct floors for a building
    public function getFloors($buildingId)
    {
        return Room::where('building_id', $buildingId)
                   ->select('floor')
                   ->distinct()
                   ->orderBy('floor')
                   ->get();
    }

    // Get available rooms by building and floor
    public function getRooms($buildingId, Request $request)
    {
        $floor = $request->query('floor');

        $query = Room::where('building_id', $buildingId);

        if ($floor !== null) {
            $query->where('floor', $floor);
        }

        // Fetch rooms
        $rooms = $query->get([
            'id',
            'room_number',
            'capacity',
            'is_active'
        ]);

        // Append current_occupancy dynamically
        $rooms->each(function ($room) {
            $room->current_occupancy = $room->activeAllocations()->count();
        });

        return $rooms;
    }

}
