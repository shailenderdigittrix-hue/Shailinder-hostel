<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\Course;
use App\Models\Room;
use App\Models\Building;
use App\Models\RoomAllocation;
use App\Models\RoomChangeRequest;
use Carbon\Carbon;

class WardenDashboardController extends Controller
{
    public function index()
    {   
        $data['roles'] = Role::count();
        $data['permissions'] = Permission::count();
        $data['users'] = User::count();
        $data['students'] = Student::count();
        $data['rooms'] = Room::count();
        $data['hostels'] = Hostel::count();
        $data['courses'] = Course::count();
        $data['room_allocations'] = RoomAllocation::count();

        return view('dashboards.warden', $data);
    }

    // public function alloted_rooms_list(Request $request) {
    //     $query = RoomAllocation::with(['student', 'room.hostel', 'room.building'])
    //         ->whereNull('deallocated_at');

    //     $query2 = RoomAllocation::with(['student', 'room.hostel', 'room.building'])
    //     ->whereNull('deallocated_at');


    //     // If the user is a Hostel Warden, restrict by their hostels
    //     if (auth()->user()->hasRole('Hostel Warden')) {
    //         $wardenId = auth()->id();

    //         // Hostels assigned to the warden
    //         $wardenHostelIds = Hostel::where('warden', $wardenId)->pluck('id');
    //         // echo "<pre>";print_r($wardenHostelIds);die;
    //         // Filter room allocations by hostel
    //         $query->whereIn('hostel_id', $wardenHostelIds);
    //         $query2->whereIn('hostel_id', $wardenHostelIds);
    //     }

    //     if ($request->device_serial_no) {
    //         $query->whereHas('room.hostel', function ($q) use ($request) {
    //             $q->where('device_serial_no', $request->device_serial_no);
    //         });
    //     }

    //     if ($request->allocated_date) {
    //             // echo Carbon::parse($request->allocated_date)->format('Y-m-d');die;
    //             $query->where('allocated_at', Carbon::parse($request->allocated_date)->format('Y-m-d'));
    //     }         
        
        
    //     $roomAllocations = $query->>get();
    //     $roomAllocation = $query2->get();
        

        
    //     // Get only IDs from the allocations
    //     $hostelIds   = $roomAllocation->pluck('hostel_id')->filter()->unique();
    //     $buildingIds = $roomAllocation->pluck('building_id')->filter()->unique();
    //     $roomIds     = $roomAllocation->pluck('room_id')->filter()->unique();

    //     // Load related data
    //     $data['room_allocations'] = $roomAllocations;
    //     echo'<pre>';print_r( $data['room_allocations']);die;
    //     $data['hostels'] = Hostel::whereIn('id', $hostelIds)->get();
    //     $data['buildings'] = Building::whereIn('id', $buildingIds)->get();
    //     $data['rooms'] = Room::whereIn('id', $roomIds)->get();
    //     $data['device_serial_no'] = $request->device_serial_no;
    //     $data['allocated_date'] = $request->allocated_date;
       
    //     return view('backend.HostelManagement.rooms.room_allocations', $data);
    // }

    public function alloted_rooms_list(Request $request)
{
    // Base query for allocations
    $query = RoomAllocation::with(['student', 'room.hostel', 'room.building'])
        ->whereNull('deallocated_at');

    $query2 = RoomAllocation::with(['student', 'room.hostel', 'room.building'])
        ->whereNull('deallocated_at');

    // 🔐 Restrict to warden's assigned hostels
    if (auth()->user()->hasRole('Hostel Warden')) {
        $wardenId = auth()->id();

        // Get hostel IDs assigned to this warden
        $wardenHostelIds = Hostel::where('warden', $wardenId)->pluck('id');

        // Apply the restriction through the `room.hostel` relationship
        $query->whereHas('room.hostel', function ($q) use ($wardenHostelIds) {
            $q->whereIn('id', $wardenHostelIds);
        });

        $query2->whereHas('room.hostel', function ($q) use ($wardenHostelIds) {
            $q->whereIn('id', $wardenHostelIds);
        });
    }

    // 🔍 Filter by device serial number
    if ($request->device_serial_no) {
        $query->whereHas('room.hostel', function ($q) use ($request) {
            $q->where('device_serial_no', $request->device_serial_no);
        });
    }

    // 📅 Filter by allocated date
    if ($request->allocated_date) {
        $query->whereDate('allocated_at', Carbon::parse($request->allocated_date)->format('Y-m-d'));
    }

    // 🔄 Execute queries
    $roomAllocations = $query->get();
    $roomAllocation = $query2->get();

    // echo'<pre>';print_r($roomAllocation);die;   
    // Extract IDs for filters
    $hostelIds   = $roomAllocation->pluck('hostel_id')->filter()->unique();
    // echo '<pre>';print_r($hostelIds);die;
    $buildingIds = $roomAllocation->pluck('building_id')->filter()->unique();
    $roomIds     = $roomAllocation->pluck('room_id')->filter()->unique();

    // Related data for dropdowns or filters
    $data['room_allocations'] = $roomAllocations;
    // echo '<pre>';print_r($wardenHostelIds);die;
    $data['hostels'] = Hostel::whereIn('id', $wardenHostelIds)->get();
    $data['buildings'] = Building::whereIn('id', $buildingIds)->get();
    $data['rooms'] = Room::whereIn('id', $roomIds)->get();
    $data['device_serial_no'] = $request->device_serial_no;
    $data['allocated_date'] = $request->allocated_date;

    return view('backend.HostelManagement.rooms.room_allocations', $data);
}

    public function hostel_room_vacancy_status() {
        $query = Hostel::with(['rooms.activeAllocations']);

        // Restrict to assigned hostels if user is a warden
        if (auth()->user()->hasRole('Hostel Warden')) {
            $wardenId = auth()->id();

            // Only hostels assigned to the logged-in warden
            $query->where('warden', $wardenId);
        }

        $hostels = $query->get();

        // Calculate bed status per room
        foreach ($hostels as $hostel) {
            foreach ($hostel->rooms as $room) {
                $room->occupied_beds = $room->activeAllocations->count();
                $room->vacant_beds = $room->capacity - $room->occupied_beds;
            }
        }

        return view('backend.HostelManagement.rooms.vacancy_status', [
            'hostels' => $hostels
        ]);
    }

    public function getRoomsByHostel($id) {
        $hostel = Hostel::with('buildings.rooms.activeAllocations')->findOrFail($id);

        $rooms = collect();

        foreach ($hostel->buildings as $building) {
            foreach ($building->rooms as $room) {
                // Add required display properties to each room object
                $room->hostel_name = $hostel->name;
                $room->building_name = $building->name;
                $rooms->push($room);
            }
        }

        // Pass only the processed, flat room list
        $html = view('backend.partials.rooms_table', [
            'rooms' => $rooms
        ])->render();

        return response()->json([
            'html' => $html
        ]);
    }

    public function importRoomAllocations(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            return back()->withErrors(['file' => 'No file uploaded']);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xls', 'xlsx', 'csv'])) {
            return back()->withErrors(['file' => 'Unsupported file type']);
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            // dd($rows);

            foreach ($rows as $key => $row) {
                if ($key === 0) continue; // Skip header

                $enrollmentNo   = trim($row[1]);
                $hostelName     = trim($row[4]);
                $buildingName   = trim($row[5]);
                $floorRaw       = trim($row[6]);
                $roomNumber     = trim($row[7]);
                $validFrom      = trim($row[8]);
                $validTill      = trim($row[9]);

                // Normalize floor (e.g., "1st" → 1)
                $floor = is_numeric($floorRaw) ? (int)$floorRaw : intval(filter_var($floorRaw, FILTER_SANITIZE_NUMBER_INT));

                // Parse dates
                $allocated_at = $validFrom ? Carbon::parse($validFrom)->format('Y-m-d') : null;
                $deallocated_at = $validTill ? Carbon::parse($validTill)->format('Y-m-d') : null;

                // 1. Student
                $student = Student::where('enrollment_no', $enrollmentNo)->first();
                if (!$student || !$student->user) {
                    return back()->withErrors(['file' => "Enrollment number '{$enrollmentNo}' not found (Row " . ($key + 1) . ")."]);
                }

                // 2. Hostel
                $hostel = Hostel::where('name', $hostelName)->first();
                if (!$hostel) {
                    return back()->withErrors(['file' => "Hostel '{$hostelName}' not found (Row " . ($key + 1) . ")."]);
                }

                // 3. Building
                $building = Building::where('name', $buildingName)
                    ->where('hostel_id', $hostel->id)
                    ->first();
                if (!$building) {
                    return back()->withErrors(['file' => "Building '{$buildingName}' not found under hostel '{$hostelName}' (Row " . ($key + 1) . ")."]);
                }

                // 4. Room
                $room = Room::where('room_number', $roomNumber)
                    ->where('floor', $floor)
                    ->where('building_id', $building->id)
                    ->first();
                if (!$room) {
                    return back()->withErrors(['file' => "Room '{$roomNumber}' on floor '{$floor}' not found in building '{$buildingName}' (Row " . ($key + 1) . ")."]);
                }

                // Optional: check if allocation already exists
                $existing = RoomAllocation::where('student_id', $student->id)
                    ->where('room_id', $room->id)
                    ->whereDate('allocated_at', $allocated_at)
                    ->first();
                if ($existing) {
                    continue; // Skip duplicate
                }

                // 5. Allocate room
                RoomAllocation::create([
                    'student_id'     => $student->id,
                    'hostel_id'      => $hostel->id,
                    'building_id'    => $building->id,
                    'room_id'        => $room->id,
                    'floor'          => $floor,
                    'allocated_at'   => $allocated_at,
                    'deallocated_at' => $deallocated_at,
                ]);
            }

            return back()->with('success', 'Room allocations imported successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Failed to import file: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request){
        try {
            $validated = $request->validate([
                'id' => 'required|integer',
                'status' => 'required|in:approved,rejected',
                'itemType' => 'required|string',
            ]);

            // Define allowed model map
            $allowedModels = [
                'RoomChangeRequest' => RoomChangeRequest::class,
                // add other allowed models here if needed
            ];

            $itemType = $validated['itemType'];

            if (!array_key_exists($itemType, $allowedModels)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid item type.'
                ], 400);
            }

            $modelClass = $allowedModels[$itemType];

            $item = $modelClass::findOrFail($validated['id']);

            if ($item->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This request has already been processed.'
                ]);
            }

            $item->status = $validated['status'];
            $item->updated_at = now();
            $item->save();

            return response()->json([
                'success' => true,
                'message' => 'Request status updated successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Update status error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the request.'
            ], 500);
        }
    }

    public function room_re_allocate(Request $request) {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'hostel_id'     => 'nullable|integer',
            'building_id'   => 'required|integer',
            'floor'         => 'required|integer',
            'room_id'       => 'required|integer',
        ]);
        $allocation = RoomAllocation::where('student_id', $request->student_id)->latest()->first();
        if ($allocation) {
            $allocation->update([
                'hostel_id'     => $request->hostel_id,
                'building_id'   => $request->building_id,
                'floor'         => $request->floor,
                'room_id'       => $request->room_id,
                'allocated_at'  => now()->toDateString(),
            ]);
            
            // return back()->with('success', 'Room re-allocated successfully.');
            return redirect()->route('admin.alloted_rooms_list')->with('success', 'Successfully Created Students');
        }
        return back()->with('error', 'Room allocation not found for the student.');
    }




    
}
