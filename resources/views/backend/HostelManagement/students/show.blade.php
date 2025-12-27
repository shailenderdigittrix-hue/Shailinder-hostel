@extends('backend.layouts.master') {{-- Use your custom layout file --}}

@section('content')
@push('styles')
<style>
.calendar-day {
    padding: 0.5rem;
    border-radius: 0.25rem;
    text-align: center;
    user-select: none;
    font-weight: 600;
    cursor: default;
}

.present {
    background-color: #d1e7dd; /* greenish */
    color: #0f5132;
}

.late {
    background-color: #fff3cd; /* yellow */
    color: #664d03;
}

.absent {
    background-color: #f8d7da; /* reddish */
    color: #842029;
}

.legend-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 6px;
    vertical-align: middle;
}

.present-dot {
    background-color: #0f5132;
}

.late-dot {
    background-color: #664d03;
}

.absent-dot {
    background-color: #842029;
}
</style>

@endpush
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between ah4gn-items-center">
                    <h5 class="card-title mb-0">Student Details</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Student Information -->
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">Student Information</div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> {{ $student->first_name .' '. $student->last_name }}</p>
                                    <p><strong>Enrollment No:</strong> {{ $student->enrollment_no }}</p>
                                    <p><strong>Email:</strong> {{ $student->email }}</p>
                                    <p><strong>Phone:</strong> {{ $student->phone }}</p>
                                </div>
                            </div>

                            <!-- Hostel Details -->
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">Hostel Details</div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered mb-0 text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Hostel Name</th>
                                                    <th>Building</th>
                                                    <th>Floor</th>
                                                    <th>Room Number</th>
                                                    <th>Allocated At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $student->hostel?->name ?? 'N/A' }}</td>
                                                    <td>{{ $student->roomAllocation?->room?->building?->name ?? 'N/A' }}</td>
                                                    <td>{{ $student->roomAllocation?->floor ?? 'N/A' }}</td>
                                                    <td>{{ $student->roomAllocation?->room?->room_number ?? 'N/A' }}</td>
                                                    <td>{{ $student->roomAllocation?->allocated_at ? \Carbon\Carbon::parse($student->roomAllocation->allocated_at)->format('d M Y') : 'N/A' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Mess Bills List -->
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">Mess Bills</div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        
                                            <table class="table table-striped table-bordered mb-0 text-center">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Month</th>
                                                        <th>Days</th>
                                                        <th>Amount (₹)</th>
                                                        <th>Status</th>
                                                        <th>Created At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($student->messBills->isNotEmpty())
                                                    @foreach ($student->messBills as $index => $messBill)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($messBill->month)->format('F Y') }}</td>
                                                            <td>{{ $messBill->days }}</td>
                                                            <td>{{ number_format($messBill->amount, 2) }}</td>
                                                            <td>
                                                                @if ($messBill->status === 'paid')
                                                                    <span class="badge bg-success">Paid</span>
                                                                @elseif ($messBill->status === 'unpaid')
                                                                    <span class="badge bg-danger">Unpaid</span>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ ucfirst($messBill->status) }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ \Carbon\Carbon::parse($messBill->created_at)->format('d M Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                    @else
                                                        <p class="text-center p-3 text-muted">No mess bills found.</p>
                                                    @endif
                                                </tbody>
                                            </table>
                                        
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark">Disciplinary Violations/Fines</div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        @if($student->violations->isNotEmpty())
                                            <table class="table table-striped table-bordered mb-0 text-center">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Date</th>
                                                        <th>Type</th>
                                                        <th>Status</th>
                                                        <th>Fine Amount</th>
                                                        <th>Review Notes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($student->violations as $index => $violation)
                                                    <tr>
                                                        <!-- violation columns -->
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($violation->violation_date)->format('d M Y') }}</td>
                                                        <td>{{ $violation->violationType->name }}</td>
                                                        <td>{{ ucfirst($violation->status) }}</td>
                                                        <td>{{ ucfirst($violation->fine_amount) }}</td>
                                                        <td>{{ ucfirst($violation->review_notes) }}</td>
                                                    </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-center p-3 text-muted">No disciplinary violations recorded.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="col-lg-4">
                            <!-- Attenddance Calander Start -->
                            <div class="attendence-details mb-3">
                                <h5 class="card-title fw-bold">Attendance Details</h5>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="inner-box">
                                            <h6>Present</h6>
                                            <p class="mb-0">{{ $attendanceCalendar['attendanceCount'] }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="inner-box">
                                            <h6>Leave</h6>
                                            <p class="mb-0">{{ $attendanceCalendar['leaveCount'] }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="inner-box">
                                            <h6>Absent</h6>
                                            <p class="mb-0">{{ $attendanceCalendar['absentCount'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                @include('backend.partials.student_attendance_calendar', [
                                    'calendarData' => $attendanceCalendar['calendarData'],  // <-- Only this sub-array
                                    'firstDayOfMonth' => $attendanceCalendar['firstDayOfMonth'],
                                    'year' =>  $attendanceCalendar['year'],
                                    'month' =>  $attendanceCalendar['month'],
                                ])
                            </div>
                            
                            



                            <!-- Attenddance Calander end -->

                            <div class="attendence-details">
                                <h5 class="card-title fw-bold">Mess Details</h5>
                                <div class="row g-4">
                                    <!-- <div class="col-md-6">
                                        <div class="inner-box">
                                            <h6>Mess Type</h6>
                                            <p class="mb-0">Vegetarian </p>
                                        </div>

                                    </div> -->
                                    @php
                                        $totalBills = $student->messBills->count();
                                        $paidBills = $student->messBills->where('status', 'paid')->count();
                                        $unpaidBills = $student->messBills->where('status', 'unpaid')->count();
                                    @endphp

                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <h6>Mess Fees Status</h6>
                                            <p class="mb-0">
                                                @if ($totalBills === 0)
                                                    No Bills
                                                @elseif ($paidBills === $totalBills)
                                                    Paid
                                                @elseif ($unpaidBills === $totalBills)
                                                    Unpaid
                                                @else
                                                    Partially Paid
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                </div>
                                @include('backend.partials.student_attendance_calendar', [
                                    'calendarData' => $attendanceCalendar['calendarData'],  // <-- Only this sub-array
                                    'firstDayOfMonth' => $attendanceCalendar['firstDayOfMonth'],
                                    'year' =>  $attendanceCalendar['year'],
                                    'month' =>  $attendanceCalendar['month'],
                                ])
                            </div>
                            <!-- <div class="calendar">
                                <div class="calendar-header">
                                    <span>Calendar View</span>
                                    <a href="#" class="text-primary fw-semibold text-decoration-none">Sep, 2025</a>
                                </div>

                                <div class="calendar-grid weeks fw-bold">
                                    <div>Sun</div>
                                    <div>Mon</div>
                                    <div>Tue</div>
                                    <div>Wed</div>
                                    <div>Thu</div>
                                    <div>Fri</div>
                                    <div>Sat</div>
                                </div>

                                <div class="calendar-grid">
                                    <div class="calendar-day">31</div>
                                    <div class="calendar-day present">1</div>
                                    <div class="calendar-day present">2</div>
                                    <div class="calendar-day present">3</div>
                                    <div class="calendar-day present">4</div>
                                    <div class="calendar-day present">5</div>
                                    <div class="calendar-day absent">6</div>

                                    <div class="calendar-day absent">7</div>
                                    <div class="calendar-day present">8</div>
                                    <div class="calendar-day present">9</div>
                                    <div class="calendar-day present">10</div>
                                    <div class="calendar-day present">11</div>
                                    <div class="calendar-day present">12</div>
                                    <div class="calendar-day absent">13</div>

                                    <div class="calendar-day absent">14</div>
                                    <div class="calendar-day present">15</div>
                                    <div class="calendar-day present">16</div>
                                    <div class="calendar-day present">17</div>
                                    <div class="calendar-day present">18</div>
                                    <div class="calendar-day present">19</div>
                                    <div class="calendar-day absent">20</div>

                                    <div class="calendar-day absent">21</div>
                                    <div class="calendar-day present">22</div>
                                    <div class="calendar-day present">23</div>
                                    <div class="calendar-day present">24</div>
                                    <div class="calendar-day present">25</div>
                                    <div class="calendar-day present">26</div>
                                    <div class="calendar-day absent">27</div>

                                    <div class="calendar-day absent">28</div>
                                    <div class="calendar-day present">29</div>
                                    <div class="calendar-day present">30</div>
                                    <div class="calendar-day">1</div>
                                    <div class="calendar-day">2</div>
                                    <div class="calendar-day">3</div>
                                    <div class="calendar-day">4</div>
                                </div>

                                <div class="legend border-top">
                                    <span><span class="legend-dot present-dot"></span> Present</span>
                                    <span><span class="legend-dot halfday-dot"></span> Late Absent</span>
                                    <span><span class="legend-dot absent-dot"></span> Absent</span>
                                </div>
                            </div> -->
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
</div>
@endsection