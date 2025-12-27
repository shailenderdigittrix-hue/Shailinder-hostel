<?php

namespace App\Http\Controllers;

use App\Models\DisciplinaryViolation;
use App\Models\Student;
use App\Models\RoomAllocation;
use App\Models\ViolationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ViolationReviewedNotification;
use App\Notifications\ViolationStatusUpdated;
use App\Services\PusherService; 



class DisciplinaryViolationController extends Controller
{
    public function __construct() {
        // Optional: Add middleware for permissions (Spatie)
        // $this->middleware('permission:manage-violations');
    }

    public function index() {
        $authUser = auth()->user();
        // dd($authUser);
        $data['role'] = $authUser->getRoleNames()->first();
        $data['violations'] = DisciplinaryViolation::with(['student', 'reviewer', 'violationType'])
            ->latest()
            ->paginate(15);
        // dd($data);
        return view('backend.violations.list', $data);
    }

    public function create() {
        $data['students'] = Student::with('user')->get();
        $data['violationTypes'] = ViolationType::all();
        return view('backend.violations.add', $data);
    }
 
    public function store(Request $request, PusherService $pusher) {
        $pusher->send('event-channel', 'violation', [
            'message' => 'Disciplinary Violation',
        ]);
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_date' => 'required|date',
            'violation_type_id' => 'required|exists:violation_types,id',
            'details' => 'nullable|string|max:1000',
            'fine_amount' => 'nullable|numeric|min:0',
            'fine_reason' => 'nullable|string|max:255',
            'fine_issued_at' => 'nullable|date',
        ]);

        // Find violation type name by ID
        $violationType = ViolationType::find($validated['violation_type_id']);
        
        $authUser = auth()->user();
        $role = $authUser->getRoleNames()->first();
        if($role === "Admin"){
            $status = 'approved';
        } else {
            $status = 'pending';
        }
        
        DisciplinaryViolation::create([
            'student_id' => $validated['student_id'],
            'violation_date' => $validated['violation_date'],
            // 'type' => $violationType->name,  // store name string here
            'type' => $validated['violation_type_id'],
            'details' => $validated['details'] ?? null,
            'status' => $status,
            'fine_amount' => $validated['fine_amount'] ?? null,
            'fine_reason' => $validated['fine_reason'] ?? null,
            'fine_issued_at' => $validated['fine_issued_at'] ?? now(),
        ]);
        
        $pusher->send('event-channel', 'violation', [
            'message' => 'Disciplinary Violation',
        ]);

        return redirect()->route('violations.index')
            ->with('success', 'Violation recorded successfully.');
    }

    public function edit(DisciplinaryViolation $violation){
        // Only allow editing if pending
        if ($violation->status !== 'pending') {
            return redirect()->back()->with('error', 'This violation has already been reviewed.');
        }

        return view('backend.violations.edit', compact('violation'));
    }

    public function update(Request $request, DisciplinaryViolation $violation) {
        // dd($violation);
        // $student_id = $violation->student_id;
        // dd($student_id);
        
        $request->validate([
            'status'        => 'required|in:approved,rejected',
            'review_notes'  => 'nullable|string',
        ]);

        $violation->update([
            'status'        => $request->status,
            'review_notes'  => $request->review_notes,
            'reviewed_by'   => Auth::id(),
            'reviewed_at'   => now(),
        ]);

        // Notify the student
        $studentUser = $violation->student->user;
        if ($studentUser) {
            $studentUser->notify(new ViolationStatusUpdated($violation));
        }

        // Notify the guardian if exists
        $guardianUser = optional($violation->student->guardian)->user;
        if ($guardianUser) {
            $guardianUser->notify(new ViolationStatusUpdated($violation));
        }

        $authUser = auth()->user();

        $role = $authUser->getRoleNames()->first();

        if ($role === 'Admin') {
            $pusher->send('warden-channel', 'violation'.$authUser['id'], [
                'message' => 'A new disciplinary violation was added by Admin!',
                'user_id' => $authUser->id,
                'role' => $authRole,
            ]);
        } 
        elseif ($user->role === 'Hostel Warden') {
            $pusher->send('admin-channel', 'violation', [
                'message' => 'A new disciplinary violation was added by Warden!',
                'user_id' => 1,
                'role' => "Admin", 
            ]);
        }

        return redirect()->route('violations.index')
            ->with('success', 'Violation reviewed and notifications sent.');
    }

    public function destroy(DisciplinaryViolation $violation) {
        $violation->delete();

        return redirect()->route('violations.index')
            ->with('success', 'Violation deleted.');
    }




}
