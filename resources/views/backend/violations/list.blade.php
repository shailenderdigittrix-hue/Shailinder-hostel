@extends('backend.layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"> <i class="fas fa-clipboard-list me-2"></i> Disciplinary Violations </h4>
                    <a href="{{ route('violations.create') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-plus-circle me-1"></i> Record New Violation
                    </a>
                </div>

                <div class="card-body">
                    
                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th class="text-start">Student</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Fine Amount(&#x20B9;)</th>
                                    <th>Status</th>
                                    <th>Approved By</th>
                                    @if($role === 'Admin')
                                    <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($violations as $violation)
                                    <tr>
                                        <td>{{ $violation->id }}</td>
                                        <td class="text-start">
                                            {{ $violation->student->first_name .' '. $violation->student->last_name ?? 'N/A' }}<br>
                                            <small class="text-muted">Enrollment no: {{ $violation->student->enrollment_no ?? '-' }}</small>
                                        </td>
                                        <td>{{ $violation->violation_date ? $violation->violation_date->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $violation->violationType->name ?? '-' }}</td>
                                        <td>{{ $violation->fine_amount ?? '0.00' }}</td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $violation->status === 'approved' ? 'success' : 
                                                ($violation->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($violation->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $violation->reviewer->name ?? '-' }}</td>
                                        @if($role === 'Admin')
                                        <td>
                                            @if($violation->status === 'pending')
                                            <div class="mb-1">
                                                <a href="{{ route('violations.edit', $violation->id) }}" 
                                                class="btn btn-sm btn-outline-secondary w-100"
                                                data-bs-toggle="tooltip" title="Review Violation">
                                                    <i class="fas fa-eye"></i> Edit
                                                </a>
                                            </div>
                                            @endif

                                            <form action="{{ route('violations.destroy', $violation->id) }}" 
                                            method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete this violation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger w-100" 
                                                    data-bs-toggle="tooltip" title="Delete Violation">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                            </form>
                                            </td>

                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-muted">No violations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end mt-3">
                        {{ $violations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Enable tooltips --}}
@push('scripts')
<script>
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush
@endsection
