<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\StudentLeaveSubmitted;
use App\Notifications\StudentLeaveStatusChanged;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentLeavesExport;
use App\Models\User;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\Course;
use App\Models\Room;
use App\Models\Building;
use App\Models\RoomAllocation;
use App\Models\RoomChangeRequest;
use Carbon\Carbon;
use App\Models\StudentLeave;



class StudentLeaveController extends Controller
{
    /** Display a listing of the resource. */
    // public function index() {
    //     $user = auth()->user();
    //     if ($user->hasRole('Student')) {
    //         $studentId = optional($user->student)->id;
    //         $leaves = StudentLeave::where('student_id', $studentId)->latest()->get();
    //     } else {
    //         $leaves = StudentLeave::with('student.user')->latest()->get();
    //     }
    //     return view('backend.HostelManagement.leaves.list', compact('leaves'));
    // }
    public function index() {
        $user = auth()->user();

        // Case 1: Student - only their own leaves
        if ($user->hasRole('Student')) {
            $studentId = optional($user->student)->id;

            $leaves = StudentLeave::where('student_id', $studentId)
                ->latest()
                ->get();
        }

        // Case 2: Hostel Warden - leaves from students in hostels they manage
        elseif ($user->hasRole('Hostel Warden')) {
            $wardenId = $user->id;

            // Get device_serial_no of hostels assigned to the warden
            $hostelDeviceSerials = Hostel::where('warden', $wardenId)
                ->pluck('device_serial_no');

            // Get student IDs from those hostels
            $studentIds = Student::whereIn('device_serial_no', $hostelDeviceSerials)
                ->pluck('id');

            // Get leaves of those students
            $leaves = StudentLeave::with('student.user')
                ->whereIn('student_id', $studentIds)
                ->latest()
                ->get();
        }

        // Case 3: Other roles (Admin, Super Admin, etc.) - all leaves
        else {
            $leaves = StudentLeave::with('student.user')
                ->latest()
                ->get();
        }
        // dd($leaves );
        return view('backend.HostelManagement.leaves.list', compact('leaves'));
    }

    /** Show the form for creating a new resource. */
    public function create() {
        $user = auth()->user();
        if ($user->hasRole('Student')) {
            $student = $user->student;

            if (!$student) {
                return redirect()->back()->with('error', 'Student profile not found.');
            }

            return view('backend.HostelManagement.leaves.add', compact('student'));
        }

        if ($user->hasRole('Hostel Warden')) {
            // Get hostel(s) assigned to the warden
            $wardenHostels = Hostel::where('warden', $user->id)->pluck('id');

            // Get students only from those hostels
            $students = Student::whereIn('device_serial_no', function ($query) use ($wardenHostels) {
                $query->select('device_serial_no')
                    ->from('hostels')
                    ->whereIn('id', $wardenHostels);
            })->with('user')->get();

            return view('backend.HostelManagement.leaves.add', compact('students'));
        }

        if ($user->hasRole('Admin')) {
            $students = Student::with('user')->get();
            return view('backend.HostelManagement.leaves.add', compact('students'));
        }

        
        abort(403, 'Unauthorized');
    }

    /**  Store a newly created resource in storage. */
    public function store(Request $request) {
        // dd($request->all());

        $data = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $document = '';
        if ($request->hasFile('document')) {
                $file = $request->file('document'); 
                $name = time() . '' . str_replace(' ', '', $file->getClientOriginalName());
                $file->move(public_path('/uploads/leave_documents/'), $name);
                $document = url('/public/uploads/leave_documents/' . $name);
            }

        $studentLeave = StudentLeave::create([
            'student_id' => $request->student_id,
            'from_date' => $data['from_date'],
            'to_date' => $data['to_date'],
            'reason' => $data['reason'],
            'remarks' => $data['remarks'] ?? null,
            'document' => $document,
        ]);

        if($studentLeave){
            $student = Student::find($request->student_id);
            // dd($student);
            $user = User::find($student->user_id);
            // dd($user);
            // dd($user->email);
            if ($user) {
                $user->notify(new StudentLeaveSubmitted($studentLeave));
            }

            // auth()->user()->notify(new StudentLeaveSubmitted($studentLeave));
        }
        // $leave = StudentLeave::create($data);
        

        return redirect()->route('student-leaves.index')->with('success', 'Leave application submitted.');
    }

    public function show(string $id) {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    /**  Remove the specified resource from storage. */
    public function destroy(string $id)
    {
        //
    }

    public function approve($id) {
        $user = auth()->user();
        if (!$user->hasRole('Hostel Warden') && !$user->hasRole('Admin')) {
            abort(403, 'Unauthorized action.');
        }

        $leave = StudentLeave::findOrFail($id);
        $leave->status = 'Approved';
        $leave->remarks = 'Approved by ' . $user->name;
        $leave->save();

        $leave->student->user->notify(new StudentLeaveStatusChanged($leave));

        return redirect()->back()->with('success', 'Leave approved.');
    }

    public function reject($id) {
        $leave = StudentLeave::findOrFail($id);
        $leave->status = 'Rejected';
        $leave->remarks = 'Rejected by ' . auth()->user()->name;
        $leave->save();

        // Notify the student
        $leave->student->user->notify(new StudentLeaveStatusChanged($leave));

        return redirect()->back()->with('success', 'Leave rejected.');
    }

    public function exportExcel() {
        return redirect()->back()->with('success', 'Leave approved.');
    }





}
