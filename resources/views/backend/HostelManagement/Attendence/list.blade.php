@extends('backend.layouts.master')

@section('content')
@push('styles')
<style>
    
</style>
@endpush

<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Attendance Management</h5>

                    <div>
                        <!-- <a href="{{ route('attendance.dailyReport') }}" class="btn btn-sm btn-outline-primary me-2">Daily Attendance Report</a> -->
                        <a href="{{ route('attendance.monthlyReport') }}" class="btn btn-sm btn-outline-secondary">Monthly Attendance Report</a>
                    </div>
                </div>

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('attendence.list') }}" class="p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label>Search by Enrollment No</label>
                            <input type="text" name="search" class="form-control" placeholder="Enrollment No" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label>Filter by From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label>Filter by To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label>Hostel</label>
                            <select class="form-control" name="device_serial_no">
                                <option>Select Option</option>
                                @foreach($hostels as $hostel)
                                <option value="{{ $hostel->device_serial_no }}" {{ $device_serial_no == $hostel->device_serial_no ? 'selected' : '' }}>
                                    {{ $hostel->name }}
                                </option>
                                @endforeach
                            </select>
                           
                        </div>
                        <div class="col-md-2">
                            <label>Filter by Month</label>
                            <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Apply Filters</button>
                        </div>

                        <div class="all-student-count d-flex  justify-content-between pt-5">
                            <p>Total Record: {{@$totalAttendenceEntries}}</p>
                           <!--  <p>Total Students: {{ @$totalStudents }}
                            </p> -->
                            <p>Present Count: {{ @$presentCount }}
                            </p>
                            <p>Late Count: {{ @$lateCount }}</p>
                            <!-- <p>Absent Count: {{ @$absentCount }}</p> -->
                        </div>


                    </div>
                </form>

                <div class="card-body">
                    {{-- Attendance Table --}}
                    <table class="table table-striped table-hover align-middle mt-4">
                        <thead>
                            <tr class="fw-bold">
                                <th>#</th>
                                <th>Id</th> <!-- Device Enrollment Id--->
                                <th>Enrollment Id</th>
                                <th>Student Name</th>
                                <th>Hostel</th>
                                <th>Room</th>
                                <th>Log Date</th>
                                <th>Log Time</th>
                                <th>Entry Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student_attendance as $log)
                            <tr @class([
                                'green-main' => $log->entry_status == 'Present',
                                'orange-main' => $log->entry_status == 'Late Coming',
                                'red-main' => $log->entry_status == 'Absent',
                            ])>
                                <th scope="row">{{ $loop->iteration + ($student_attendance->currentPage() - 1) * $student_attendance->perPage() }}</th>
                                <td>{{ $log->enrollment_no ?? '-----' }}</td>
                                <td>{{ $log->student->enrollment_no ?? '-----' }}</td>
                                <td>{{ $log->student_name ?? '-----' }}</td>
                                <td>{{ $log->student->hostel->name ?? '-----' }}</td>
                                <td>{{ $log->student->room->room_number ?? '-----' }}</td>
                                <td>{{ $log->log_date }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->log_date_time)->format('H:i:s') }}</td>
                                <td>
                                    @if($log->entry_status == 'Present')
                                        <div class="circle green"></div>
                                    @elseif($log->entry_status == 'Late Coming')
                                        <div class="circle orange"></div>
                                    @else
                                        <div class="circle red"></div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">No attendance records found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end mt-4">
                        {{ $student_attendance->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection