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
use App\Models\SmtpDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\RoomChangeMail;
use Illuminate\Support\Facades\Crypt;





class AdminDashboardController extends Controller
{
    public function index(){
        $data['roles'] = Role::count();
        $data['permissions'] = Permission::count();
        $data['users'] = User::count();
        $data['students'] = Student::count();
        $data['rooms'] = Room::count();
        $data['hostels'] = Hostel::count();
        $data['courses'] = Course::count();
        $data['room_allocations'] = RoomAllocation::count();
        // dd($data);
        return view('dashboards.admin',$data);
        // return view('backend.layouts.mail',$data);
    }

    public function role_list(){
        $data['roles'] = Role::where('id', '!=', 1)->paginate(10);
        return view('backend.roles.list', $data);
    }

    public function add_role(){
        return view('backend.roles.add');
    }

    public function role_save(Request $request){
        // echo'<pre>';print_r($request->all());die;
        if($request->id) {
            $res = Role::where('id', $request->id)->update([
                        "name" => $request->name
                    ]);
        } else {
            $res = Role::create([
                    "name" => $request->name
            ]);
        }
       

        return redirect()->route('roles.list')->with('success', 'Saved role successfully.');
        
    }

        public function role_delete(Request $request)
        {
            $request->validate([
                'id' => 'required|integer|exists:roles,id',
            ]);

            $role = Role::find($request->id);

            if ($role) {
                $role->delete();
                return back()->with('success', 'Role deleted successfully.');
            }

            return back()->with('error', 'Role not found.');
        }



    public function role_permissions($id) {
        $data['role'] = Role::with('permissions')->findOrFail($id);
        $data['permissions'] = Permission::all();

        return view('backend.roles.permissions', $data);
    }

    public function ajaxUpdatePermission(Request $request){
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission' => 'required|string|exists:permissions,name',
            'assign' => 'required|boolean',
        ]);

        $role = Role::findOrFail($request->role_id);

        if ($request->assign) {
            $role->givePermissionTo($request->permission);
        } else {
            $role->revokePermissionTo($request->permission);
        }

        return response()->json(['success' => true]);
    }

    public function alloted_rooms_list(Request $request)
    {
        // Base query: include relationships and exclude deallocated rooms
        $query = RoomAllocation::with(['student', 'hostel',  'room.hostel', 'room.building'])
            ->whereNull('deallocated_at');

        // Restrict by Hostel Warden’s assigned hostels (if applicable)
        if (auth()->user()->hasRole('Hostel Warden')) {
            $wardenId = auth()->id();
            $wardenHostelIds = Hostel::where('warden', $wardenId)->pluck('id');
            $query->whereIn('hostel_id', $wardenHostelIds);
        }

        // Apply filters (device serial number and allocated date)
        if ($request->filled('device_serial_no')) {
            $query->whereHas('room.hostel', function ($q) use ($request) {
                $q->where('device_serial_no', $request->device_serial_no);
            });
        }

        if ($request->filled('allocated_date')) {
            $query->whereDate('allocated_at', Carbon::parse($request->allocated_date)->format('Y-m-d'));
        }

        // Fetch filtered results (latest allocations first)
        $roomAllocations = $query->orderBy('allocated_at', 'desc')->get();

        // Fetch all allocations for reference (latest first)
        $roomAllo = RoomAllocation::with(['student', 'room.hostel', 'room.building'])
            ->whereNull('deallocated_at')
            ->when(auth()->user()->hasRole('Hostel Warden'), function ($q) {
                $wardenId = auth()->id();
                $wardenHostelIds = Hostel::where('warden', $wardenId)->pluck('id');
                $q->whereIn('hostel_id', $wardenHostelIds);
            })
            ->orderBy('allocated_at', 'desc')
            ->get();

        // Extract related IDs (only unique, non-null)
        $hostelIds   = $roomAllo->pluck('hostel_id')->filter()->unique();
        $buildingIds = $roomAllo->pluck('building_id')->filter()->unique();
        $roomIds     = $roomAllo->pluck('room_id')->filter()->unique();

        // Load related data
        $data = [
            'room_allocations' => $roomAllocations,
            'hostels' => Hostel::whereIn('id', $hostelIds)->get(),
            'buildings' => Building::whereIn('id', $buildingIds)->get(),
            'rooms' => Room::whereIn('id', $roomIds)->get(),
            'device_serial_no' => $request->device_serial_no,
            'allocated_date' => $request->allocated_date,
        ];

        return view('backend.HostelManagement.rooms.room_allocations', $data);
    }

    public function importBulkStudents(Request $request)
    {
        $file = $request->file('file');

        if (!$file) {
            return back()->withErrors(['excel' => 'No file uploaded']);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xls', 'xlsx', 'csv'])) {
            return back()->withErrors(['excel' => 'Unsupported file type']);
        }

        $path = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            foreach ($rows as $key => $row) {
                if($key > 0) {
                    $userData = User::where("email", $row[6])->first();
                    if(empty($userData)) {
                        $user = User::create([
                            'name' => $row[2].' '.$row[3],
                            'email' => $row[6] ?? null,
                            'password' => Hash::make('defaultPassword123')
                        ]);
                        Student::create([
                            'user_id' => $user->id,
                            'first_name' => $row[2] ?? null,
                            'last_name' => $row[3] ?? null,
                            'gender' => $row[4] ?? null,
                            'date_of_birth' => $row[5] ?? null,
                            'email' => $row[6] ?? null,
                            'phone' => $row[7],
                            'course' => $row[8] ?? null,
                            'year' => $row[9] ?? null,
                            'address' => $row[10] ?? null,
                            'admission_date' => $row[11] ?? null
                        ]);
                }
                    else {
                        $user = User::where("email", $row[6])->update([
                            'name' => $row[2].' '.$row[3],
                            // 'email' => $row[6] ?? null,
                            'password' => Hash::make('defaultPassword123')
                        ]);
                        Student::where("email", $row[6])->update([
                            'user_id' => $userData->id,
                            'first_name' => $row[2] ?? null,
                            'last_name' => $row[3] ?? null,
                            'gender' => $row[4] ?? null,
                            'date_of_birth' => $row[5] ?? null,
                            // 'email' => $row[6] ?? null,
                            'phone' => $row[7],
                            'course' => $row[8] ?? null,
                            'year' => $row[9] ?? null,
                            'address' => $row[10] ?? null,
                            'admission_date' => $row[11] ?? null
                        ]);
                    }

                }
            }
            return redirect()->route('dashboard')->with('success', 'Successfully Created Students');
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            return back()->withErrors(['excel' => 'Could not read the Excel file: ' . $e->getMessage()]);
        }
    }

    public function hostel_room_vacancy_status(){
        $hostels = Hostel::with(['rooms.activeAllocations'])->get();
        foreach ($hostels as $hostel) {
            foreach ($hostel->rooms as $room) {
                $room->occupied_beds = $room->activeAllocations->count();
                $room->vacant_beds = $room->capacity - $room->occupied_beds;
            }
        }
        $data['hostels'] = $hostels;
        return view('backend.HostelManagement.rooms.vacancy_status', $data);
    }

    // public function getRoomsByHostel($id){
    //     $hostel = Hostel::with(['rooms.activeAllocations', 'rooms.hostel'])->findOrFail($id);
    //     $data['rooms'] = $hostel->rooms;
    //     $html = view('backend.partials.rooms_table', $data)->render();

    //     // echo $html;
    //     return response()->json([
    //         'html' => $html
    //     ]);
    // }

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

        // dd($request->all());

        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'hostel_id'     => 'nullable|integer',
            'building_id'   => 'required|integer',
            'floor'         => 'required|integer',
            'room_id'       => 'required|integer',
        ]);

        $allocation = RoomAllocation::where('student_id', $request->student_id)->latest()->first();
        $previousHotelName = Hostel::where('id', $allocation->hostel_id)->value('name');
        $privousBuilding   = Building::where('id', $allocation->building_id)->value('name');
        $previousFloor     = $allocation->floor;
        $previousRoom      = Room::where('id', $allocation->room_id)->value('room_number');
        $currentHotelName  = Hostel::where('id', $request->hostel_id)->value('name');
        $currentBuilding   = Building::where('id', $request->building_id)->value('name');
        $currentFloor      = $request->floor;
        $currentRoom       = Room::where('id', $request->room_id)->value('room_number');
        
        $data = [
            'previousHotelName' => $previousHotelName,
            'previousBuilding'  => $privousBuilding,
            'previousFloor'     => $previousFloor,
            'previousRoom'      => $previousRoom,
            'currentHotelName'  => $currentHotelName,
            'currentBuilding'   => $currentBuilding,
            'currentFloor'      => $currentFloor,
            'currentRoom'       => $currentRoom,
        ];

        $checkStudent = Student::where('id', $request->student_id)->first();

        // Mail::to($checkStudent->email)->send(new RoomChangeMail(
        //     $previousHotelName,
        //     $privousBuilding,
        //     $previousFloor,
        //     $previousRoom,
        //     $currentBuilding,
        //     $currentFloor,
        //     $currentHotelName,
        //     $currentRoom
        // ));
        
        if ($allocation) {
            $allocation->update([
                'hostel_id'     => $request->hostel_id,
                'building_id'   => $request->building_id,
                'floor'         => $request->floor,
                'room_id'       => $request->room_id,
                'allocated_at'  => now()->toDateString(),
            ]);

            $this->sendRoomChangeEmail($request->student_id, $data);

            // return back()->with('success', 'Room re-allocated successfully.');
            return redirect()->route('admin.alloted_rooms_list')->with('success', 'Successfully Created Students');
        }
        return back()->with('error', 'Room allocation not found for the student.');
    }


    private function sendRoomChangeEmail($studentId, array $data){
        $student = Student::find($studentId);

        if (!$student) {
            // Log::warning("Email not sent — student not found (ID: {$studentId})");
            return;
        }

        // Validate email
        if (empty($student->email) || !filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
            // Log::warning("Email not sent — invalid or missing email for student ID: {$studentId}");
            return;
        }

        try {
            Mail::to($student->email)->send(new RoomChangeMail(
                $data['previousHotelName'],
                $data['previousBuilding'],
                $data['previousFloor'],
                $data['previousRoom'],
                $data['currentBuilding'],
                $data['currentFloor'],
                $data['currentHotelName'],
                $data['currentRoom']
            ));

            // Log::info("Room change email successfully sent to {$student->email}");

        } catch (\Throwable $e) {
            // Log::error("Room change email failed for {$student->email}: " . $e->getMessage());
        }
    }

    
     public function importExcel(Request $request)
        {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv,xls',
            ]);

            // Get the uploaded file path
            $path = $request->file('file')->getRealPath();

            // Read the Excel file
            $rows = SimpleExcelReader::create($path)->getRows();

            // Loop through rows and insert into DB
                echo'<pre>';print_r($rows);die;

            foreach ($rows as $row) {
                User::create([
                    'name'  => $row['name'] ?? null,
                    'email' => $row['email'] ?? null,
                    'phone' => $row['phone'] ?? null,
                ]);
            }

            return back()->with('success', 'Excel file imported successfully!');
        }




    public function edit_smtp(){
        $data['smtp'] = SmtpDetail::first(); 
        // dd($smtp);

        return view('backend.settings.SMTP.edit', $data);
    }

    public function update_smtp(Request $request, $id){
        $smtp = SmtpDetail::findOrFail($id);

        $smtp->update($request->only([
            'mailer', 'scheme', 'host', 'port', 'username', 'password',
            'from_address', 'from_name', 'encryption', 'status'
        ]));

        return redirect()->back()->with('success', 'SMTP settings updated successfully.');
    }

    // public function test_data(){
    //     // Define your values
    //     $DB_CONNECTION = '3500';
    //     $DB_HOST = '127.0.0.1';
    //     $DB_PORT = '3306';
    //     $DB_DATABASE = 'HostelCRM';
    //     $DB_USERNAME = 'root';
    //     $DB_PASSWORD = 'bPhtcmOSWE2#';

    //     // Encrypt each one separately
    //     $enc_connection = Crypt::encryptString($DB_CONNECTION);
    //     $enc_host = Crypt::encryptString($DB_HOST);
    //     $enc_port = Crypt::encryptString($DB_PORT);
    //     $enc_database = Crypt::encryptString($DB_DATABASE);
    //     $enc_username = Crypt::encryptString($DB_USERNAME);
    //     $enc_password = Crypt::encryptString($DB_PASSWORD);

    //     // Print results in browser
    //     return "
    //     <h3>Encrypted Database Credentials:</h3>
    //     <p><strong>DB_CONNECTION:</strong> {$enc_connection}</p>
    //     <p><strong>DB_HOST:</strong> {$enc_host}</p>
    //     <p><strong>DB_PORT:</strong> {$enc_port}</p>
    //     <p><strong>DB_DATABASE:</strong> {$enc_database}</p>
    //     <p><strong>DB_USERNAME:</strong> {$enc_username}</p>
    //     <p><strong>DB_PASSWORD:</strong> {$enc_password}</p>
    //     ";
    // }
}

