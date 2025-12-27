<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomChangeRequest;
use App\Notifications\GeneralReminderNotification;
use App\Models\User;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\Course;
use App\Models\Room;
use Illuminate\Support\Facades\Mail;
use App\Models\Building;
use App\Models\RoomAllocation;
use Carbon\Carbon;

class RoomChangeRequestController extends Controller
{   
    

    public function room_change_requests_list(){
        $requests = RoomChangeRequest::with(['student', 'currentRoom', 'desiredRoom'])
            ->orderBy('id', 'desc')
            ->get();
        $hostelsData = Hostel::all();    
        return view('backend.HostelManagement.rooms.room_change_requests', compact('requests', 'hostelsData'));
    }

    // public function store(Request $request){
    //     // dd($request->all());
    //     try {
    //         $validated = $request->validate([
    //             'student_id' => 'required|exists:students,id',
    //             'current_room_id' => 'required|exists:rooms,id',
    //             // 'desired_room_id' => 'required|exists:rooms,id|different:current_room_id',
    //             'reason' => 'required|string|max:1000',
    //         ]);

    //         // Check for existing pending request
    //         $existingRequest = RoomChangeRequest::where('student_id', $validated['student_id'])
    //             ->where('status', 'pending')
    //             ->first();

    //         if ($existingRequest) {
    //             return redirect()->back()
    //                 ->withInput()
    //                 ->with('error', 'This student already has a pending room change request.');
    //         }

    //         $existingStudentRequest = RoomChangeRequest::where('student_id', $validated['student_id'])->first();
    //         if($existingStudentRequest){
    //             RoomChangeRequest::where('student_id', $validated['student_id'])->update([
    //             'current_room_id' => $validated['current_room_id'],
    //             'desired_room_id' => NULL,
    //             'reason' => $validated['reason'],
    //             'status' => 'pending',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //         }
    //         // Create new room change request
    //         RoomChangeRequest::create([
    //             'student_id' => $validated['student_id'],
    //             'current_room_id' => $validated['current_room_id'],
    //             'desired_room_id' => NULL,
    //             'reason' => $validated['reason'],
    //             'status' => 'pending',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         return redirect()->route('admin.roomChangeRequestsList')
    //             ->with('success', 'Room change request submitted successfully.');

    //     } catch (\Exception $e) {
    //         \Log::error('Room change request error: '.$e->getMessage());

    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'An error occurred while submitting the request. Please try again.');
    //     }
        
    // }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:students,id',
                'current_room_id' => 'required|exists:rooms,id',
                'reason' => 'required|string|max:1000',
            ]);

            // Check for existing pending request
            $pendingRequest = RoomChangeRequest::where('student_id', $validated['student_id'])
                ->where('status', 'pending')
                ->first();

            if ($pendingRequest) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This student already has a pending room change request.');
            }

            // Check for any existing request (approved/rejected), update it
            $existingRequest = RoomChangeRequest::where('student_id', $validated['student_id'])->first();

            if ($existingRequest) {
                $existingRequest->update([
                    'current_room_id' => $validated['current_room_id'],
                    'desired_room_id' => null,
                    'reason' => $validated['reason'],
                    'status' => 'pending',
                    'updated_at' => now(),
                ]);
            } else {
                // No existing request: create a new one
                RoomChangeRequest::create([
                    'student_id' => $validated['student_id'],
                    'current_room_id' => $validated['current_room_id'],
                    'desired_room_id' => null,
                    'reason' => $validated['reason'],
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->route('admin.roomChangeRequestsList')
                ->with('success', 'Room change request submitted successfully.');
                
        } catch (\Exception $e) {
            \Log::error('Room change request error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while submitting the request. Please try again.');
        }
    }

    public function room_re_allocate(Request $request) {
        // echo"<pre>";print_r($request->all());die;
        
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'hostel_id'     => 'nullable|integer',
            'building_id'   => 'required|integer',
            'floor'         => 'required|integer',
            'room_id'       => 'required|integer',
        ]);
        
        $allocation = RoomAllocation::where('student_id', $request->student_id)->latest()->first();
        
        $data = [
           "previousHotelName" => Hostel::where('id', $allocation->hostel_id)->value('name'),
            "privousBuilding"   => Building::where('id', $allocation->building_id)->value('name'),
            "previousFloor"     => $allocation->floor,
            "previousRoom"      => Room::where('id', $allocation->room_id)->value('room_number'),
            "currentHotelName"  => Hostel::where('id', $request->hostel_id)->value('name'),
            "currentBuilding"   => Building::where('id', $request->building_id)->value('name'),
            "currentFloor"      => $request->floor,
            "currentRoom"       => Room::where('id', $request->room_id)->value('room_number'),
        ];

        // $checkStudent = Student::where('id', $request->student_id)->first();
        
        // Mail::to($checkStudent->email)->send(new RoomChangeMail(
        //     $previousHotelName,
        //     $previousBuilding,
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
            
            // Send email to student via a separate method
            $this->sendRoomChangeEmail($request->student_id, $data);

            return back()->with('success', 'Room re-allocated successfully.');
        }
        
        return back()->with('error', 'Room allocation not found for the student.');
    }


    /**  * Send room change notification email to student  */
    private function sendRoomChangeEmail($studentId, array $data){
        $student = Student::find($studentId);

        if (!$student) {
            Log::warning("Email not sent — student not found (ID: {$studentId})");
            return;
        }

        // Validate email
        if (empty($student->email) || !filter_var($student->email, FILTER_VALIDATE_EMAIL)) {
            Log::warning("Email not sent — invalid or missing email for student ID: {$studentId}");
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

            Log::info("Room change email successfully sent to {$student->email}");

        } catch (\Throwable $e) {
            Log::error("Room change email failed for {$student->email}: " . $e->getMessage());
        }
    }


    // public function alloted_rooms_list() {
    //     $query = RoomAllocation::with(['student', 'room.hostel'])
    //                 ->whereNull('deallocated_at');

    //     // Default: all rooms and buildings (for admins or other roles)
    //     $rooms = Room::all();
    //     $buildings = Building::all();

    //     // Restrict based on role
    //     if (auth()->user()->hasRole('Hostel Warden')) {
    //         $wardenId = auth()->id();

            
    //         $hostelDeviceSerials = Hostel::where('warden', $wardenId)->pluck('device_serial_no');

            
    //         $query->whereHas('room.hostel', function ($q) use ($hostelDeviceSerials) {
    //             $q->whereIn('device_serial_no', $hostelDeviceSerials);
    //         });

    //         // Filter rooms by the same hostels
    //         $rooms = Room::whereHas('hostel', function ($q) use ($hostelDeviceSerials) {
    //             $q->whereIn('device_serial_no', $hostelDeviceSerials);
    //         })->get();

    //         // Filter buildings that contain those hostels
    //         $buildings = Building::whereHas('hostels', function ($q) use ($hostelDeviceSerials) {
    //             $q->whereIn('device_serial_no', $hostelDeviceSerials);
    //         })->get();
    //     }

    //     $data['room_allocations'] = $query->orderBy('allocated_at', 'desc')->get();
    //     $data['rooms'] = $rooms;
    //     $data['buildings'] = $buildings;

    //     return view('backend.HostelManagement.rooms.room_allocations', $data);
    // }






}
