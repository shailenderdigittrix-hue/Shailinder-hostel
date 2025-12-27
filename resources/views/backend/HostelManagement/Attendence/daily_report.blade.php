@extends('backend.layouts.master') {{-- Use your custom layout file --}}

@section('content')
@push('styles')

@endpush
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between flex-wrap align-items-center">
                    <h5 class="card-title mb-0">Daily Attendance</h5>
                    {{-- Add buttons for Daily and Monthly Reports --}}
                    <div>
                        <div class="all-student-count d-flex flex-wrap gap-3">
                            <p>Total Students: {{ $totalStudents ?? '0' }}</p>
                            <p>Present Count: {{ $presentCount ?? '0' }}</p>
                            <p>Late Count: {{ $lateCount ?? '0' }}</p>
                            <p>Absent Count: {{ $absentCount ?? '0' }}</p>
                        </div>
                        <a href="{{ route('attendance.list') }}" class="btn btn-sm btn-outline-primary me-2">All Report</a>
                        <a href="{{ route('attendance.monthlyReport') }}" class="btn btn-sm btn-outline-secondary">Monthly Attendance Report</a>
                    </div>
                </div>

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('attendance.list') }}" class="p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label for="search">Search by Enrollment ID</label>
                            <input type="text" id="search" name="search" class="form-control" placeholder="Enrollment ID" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </div>
                </form>

                <div class="card-body">
                    {{-- Attendance Table --}}
                    <table id="usersTable" class="table table-striped table-hover align-middle mt-4">
                        <thead>
                            <tr class="fw-bold">
                                <th>#</th>
                                <th>Enrollment No</th>
                                <th>Employee Name</th>
                                <th>Log Date</th>
                                <th>Log Time</th>
                                <th>Device Name</th>
                                <th>Device No</th>
                                <th>Entry Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student_attendance as $user)
                            <tr @class([
                                'green-main' => $user->entry_status == 'Present',
                                'orange-main' => $user->entry_status == 'Late Coming',
                            ])>
                                <th scope="row">{{ $loop->iteration + ($student_attendance->currentPage() - 1) * $student_attendance->perPage() }}</th>
                                <td>{{ $user->enrollment_no }}</td>
                                <td>{{ $user->student_name }}</td>
                                <td>{{ $user->log_date }}</td>
                                <td>{{ \Carbon\Carbon::parse($user->log_date_time)->format('H:i:s') }}</td>
                                <td>{{ $user->device_name }}</td>
                                <td>{{ $user->device_no }}</td>
                                <td>
                                    @if($user->entry_status == 'Present')
                                    <div class="circle green"></div>
                                    @elseif($user->entry_status == 'Late Coming')
                                    <div class="circle orange"></div>
                                    @else
                                    <div class="circle red"></div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-3">No attendance found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end mt-4">
                        {{ $student_attendance->links('pagination::bootstrap-5') }}
                    </div>

                    {{-- Attendance Chart --}}
                    <!--
                    @if(isset($attendanceCounts) && $attendanceCounts->count())
                    <div class="mt-5">
                        <h6 class="text-center">Attendance Trends (Last 7 Days)</h6>
                        <canvas id="attendanceChart"></canvas>
                    </div>
                    @endif
                    -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(isset($attendanceCounts) && $attendanceCounts->count())
<!--
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($attendanceCounts->pluck('date')) !!},
            datasets: [{
                label: 'Total Attendance',
                data: {!! json_encode($attendanceCounts->pluck('total')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
-->
@endif
@endpush