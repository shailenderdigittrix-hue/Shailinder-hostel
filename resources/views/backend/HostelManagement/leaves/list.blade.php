@extends('backend.layouts.master')

@section('content')
@php use Illuminate\Support\Str; @endphp

<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Leave Applications</h5>

                    <div class="d-flex align-items-center gap-2">
                        {{-- @role('Admin|Hostel Warden')
                        <a href="{{ route('student-leaves.export') }}" class="btn btn-success btn-sm mb-0">
                            Export to Excel
                        </a>
                        @endrole --}}

                        <a href="{{ route('student-leaves.create') }}" class="btn btn-secondary btn-sm mb-0">
                            Add New
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    @role('Admin|Hostel Warden')
                                        <th>Document</th>
                                        <th>Actions</th>
                                    @endrole
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaves as $leave)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $leave->student->user->name ?? '-' }}</td>
                                        <td>{{ $leave->from_date }}</td>
                                        <td>{{ $leave->to_date }}</td>
                                        <td>{{ $leave->reason }}</td>
                                        <td>
                                            @if($leave->status === 'Pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($leave->status === 'Approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>

                                        {{-- Remarks Column with Show More / Show Less --}}
                                        <td style="max-width: 300px; white-space: normal;">
                                            @php
                                                $remarks = $leave->remarks ?? '-';
                                                $shortRemarks = Str::limit($remarks, 80);
                                            @endphp

                                            <span class="short-text">{{ $shortRemarks }}</span>
                                            <span class="full-text d-none">{{ $remarks }}</span>

                                            @if(strlen($remarks) > 80)
                                                <br>
                                                <a href="javascript:void(0);" class="toggle-remarks text-primary text-decoration-underline">Show More</a>
                                            @endif
                                        </td>

                                        @hasanyrole('Admin|Hostel Warden')
                                            <td>
                                                @if($leave->document)
                                                    <a href="{{ $leave->document }}" target="_blank" class="btn btn-sm btn-outline-primary" download>
                                                        Download
                                                    </a>
                                                @else
                                                    <em>No File</em>
                                                @endif
                                            </td>
                                            <td>
                                                @if($leave->status === 'Pending')
                                                    <div class="d-flex gap-2">
                                                        <form action="{{ route('student-leaves.approve', $leave->id) }}" method="POST">
                                                            @csrf
                                                            <button class="btn btn-success btn-sm">Approve</button>
                                                        </form>

                                                        <form action="{{ route('student-leaves.reject', $leave->id) }}" method="POST">
                                                            @csrf
                                                            <button class="btn btn-danger btn-sm">Reject</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        @endhasanyrole
                                    </tr>
                                @empty
                                    <tr><td colspan="10">No leave records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> {{-- close .table-responsive --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $(document).on('click', '.toggle-remarks', function () {
            const $btn = $(this);
            const $td = $btn.closest('td');
            const $shortText = $td.find('.short-text');
            const $fullText = $td.find('.full-text');

            const isExpanded = !$shortText.hasClass('d-none');

            $shortText.toggleClass('d-none', isExpanded);
            $fullText.toggleClass('d-none', !isExpanded);
            $btn.text(isExpanded ? 'Show More' : 'Show Less');
        });
    });
</script>
@endpush
