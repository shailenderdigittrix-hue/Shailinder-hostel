@extends('backend.layouts.master') {{-- Use your custom layout file --}}

@section('content')
@push('styles')
<style>
    .pagination {
        justify-content: flex-end !important;
    }
</style>
@endpush
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Monthly Attendance</h5>
                    {{-- Add buttons for Daily and Monthly Reports --}}
                    <div>
                        <a href="{{ route('attendence.list') }}" class="btn btn-sm btn-outline-secondary me-2">Daily Report</a>
                        <!-- <a href="{{ route('attendance.dailyReport') }}" class="btn btn-sm btn-outline-primary me-2">Daily Attendance Report</a> -->
                    </div>  
                </div>

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('attendence.list') }}" class="p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label>Search by Enrollment id</label>
                            <input type="text" name="search" class="form-control" placeholder="Enrollment id" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Filter by Date</label>
                            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Apply Filters</button>
                        </div>
                    </div>
                </form>

                <div class="card-body">
                    {{-- Attendance Table --}}
                    <table id="usersTable" class="table table-striped table-hover align-middle mt-4">
                        <thead>
                            <tr class="fw-bold">
                                <th>#</th>
                                <th>Enrollment ID</th>
                                <th>Attendance Date</th>
                                <th>In Time</th>
                                <th>Status</th>
                                <!-- <th>Remarks</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student_attendance as $user)
                                <tr class="{{ $user->entry_status == 'Present' ? 'green-main' : ($user->entry_status == 'Late Coming' ? 'orange-main' : '') }}">
                                    <th scope="row">{{ $loop->iteration + ($student_attendance->currentPage() - 1) * $student_attendance->perPage() }}</th>
                                    <td>{{ $user->enrollment_no }}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->log_date)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($user->log_time)->format('H:i:s') }}</td>
                                    <td>{{ $user->entry_status ?? 'N/A' }}</td>
                                    <!-- <td>{{ $user->remarks ?? '-' }}</td> -->
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No attendance found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                        <!-- <tbody>
                            @forelse($student_attendance as $user)
                                <tr class="<?php echo $user->entry_status == 'Present' ? 'green-main' : ($user->entry_status == 'Late Coming' ? 'red-main' : ''); ?>">
                                    <th scope="row">{{ $loop->iteration + ($student_attendance->currentPage() - 1) * $student_attendance->perPage() }}</th>
                                    <td>{{ $user->enrollment_id }}</td>
                                    <td>{{ $user->attendance_date }}</td>
                                    <td>{{ $user->check_in }}</td>
                                    <td>{{ $user->check_out }}</td>
                                    <td>{{ $user->status }}</td>
                                    <td>{{ $user->remarks }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No attendance found.</td>
                                </tr>
                            @endforelse
                        </tbody> -->
                    </table>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end mt-4">
                        {{ $student_attendance->links('pagination::bootstrap-5') }}
                    </div>


                    {{-- Attendance Chart --}}
                    <!-- @if(isset($attendanceCounts) && $attendanceCounts->count())
                        <div class="mt-5">
                            <h6 class="text-center">Attendance Trends (Last 7 Days)</h6>
                            <canvas id="attendanceChart"></canvas>
                        </div>
                    @endif -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @if(isset($attendanceCounts) && $attendanceCounts->count())
        <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        </script> -->
    @endif
@endpush
