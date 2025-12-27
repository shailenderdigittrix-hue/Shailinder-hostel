@extends('backend.layouts.master') {{-- Use your custom layout file --}}

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                
                <div class="card-header d-flex justify-content-between ah4gn-items-center">
                    <h5 class="card-title mb-0">Student Details</h5> 
                    <a href="{{ route('admin.users.create') }}" class="btn btn-secondary btn-sm">
                        Add New
                    </a> 
                </div>

                <div class="card-body">
                    <!-- Student Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">Student Information</div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $student->name }}</p>
                            <p><strong>Roll No:</strong> {{ $student->roll_no }}</p>
                            <p><strong>Email:</strong> {{ $student->email }}</p>
                            <p><strong>Phone:</strong> {{ $student->phone }}</p>
                        </div>
                    </div>

                    <!-- College Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">College Details</div>
                        <div class="card-body">
                            <p><strong>Course:</strong> {{ $student->course }}</p>
                            <p><strong>Department:</strong> {{ $student->department }}</p>
                            <p><strong>Year:</strong> {{ $student->year }}</p>
                        </div>
                    </div>

                    <!-- Hostel Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">Hostel Details</div>
                        <div class="card-body">
                            <p><strong>Hostel Name:</strong> {{ $student->hostel_name }}</p>
                            <p><strong>Room No:</strong> {{ $student->room_no }}</p>
                        </div>
                    </div>

                    <!-- Mess Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">Mess Details</div>
                        <div class="card-body">
                            <p><strong>Mess Type:</strong> {{ $student->mess_type }}</p>
                            <p><strong>Mess Fees Paid:</strong> {{ $student->mess_fees_paid ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>

                    <!-- Disciplinary / Fines -->
                    <div class="card mb-4">
                        <div class="card-header bg-danger text-white">Disciplinary / Fines</div>
                        <div class="card-body">
                            @if($student->fines->isEmpty())
                                <p>No fines or disciplinary actions.</p>
                            @else
                                <ul class="list-group">
                                    @foreach($student->fines as $fine)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $fine->description }}
                                            <span class="badge bg-danger">₹{{ $fine->amount }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- Attendance -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">Attendance Details</div>
                        <div class="card-body">
                            <p><strong>Total Classes:</strong> {{ $student->attendance_total }}</p>
                            <p><strong>Attended:</strong> {{ $student->attendance_present }}</p>
                            <p><strong>Percentage:</strong>
                                @php
                                    $percentage = $student->attendance_total > 0
                                        ? round(($student->attendance_present / $student->attendance_total) * 100, 2)
                                        : 0;
                                @endphp
                                {{ $percentage }}%
                            </p>
                        </div>
                    </div>
                    
                </div>

            </div>
        </div>
    </div>
</div>
 @endsection