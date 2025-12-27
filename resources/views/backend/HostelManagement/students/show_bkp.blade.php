@extends('backend.layouts.master') {{-- Use your custom layout file --}}

@section('content')
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
                        </div>

                        <div class="col-lg-4">

                            <div class="attendence-details mb-3">
                                <h5 class="card-title fw-bold">Attendance Details</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <h6>Total Classes</h6>
                                            <p class="mb-0"> 0.0 </p>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <h6>Attended</h6>
                                            <p class="mb-0">108</p>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <h6>Percentage</h6>
                                            <p class="mb-0">90%</p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            
                            <div class="calendar">
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
                            </div>

                            <div class="attendence-details">
                                <h5 class="card-title fw-bold">Mess Details</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <h6>Mess Type</h6>
                                            <p class="mb-0">Vegetarian </p>
                                        </div>

                                    </div>
                                    <div class="col-md-6">
                                        <div class="inner-box">
                                            <h6>Mess Fees Paid</h6>
                                            <p class="mb-0">Yes </p>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <div class="calendar">
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
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
</div>
@endsection