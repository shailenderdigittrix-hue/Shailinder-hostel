@extends('backend.layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Review Disciplinary Violation
            </h4>
        </div>

        <div class="card-body">
            {{-- Violation Details --}}
            <div class="mb-4">
                <h5 class="text-muted mb-3">Violation Details</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Student:</strong> {{ $violation->student->user->name ?? 'Unknown' }}</li>
                    <li class="list-group-item"><strong>Date:</strong> {{ $violation->violation_date->format('d M Y') }}</li>
                    <li class="list-group-item"><strong>Type:</strong> {{ $violation->type }}</li>
                    <li class="list-group-item"><strong>Details:</strong><br>{{ $violation->details }}</li>
                </ul>
            </div>

            {{-- Review Form --}}
            <form action="{{ route('violations.update', $violation->id) }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="status" class="form-label">Decision <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="">-- Choose Action --</option>
                        <option value="approved">Approve</option>
                        <option value="rejected">Reject</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select a decision.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="review_notes" class="form-label">Review Notes (Optional)</label>
                    <textarea name="review_notes" id="review_notes" class="form-control" rows="4" placeholder="Any remarks or explanation..."></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('violations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Optional JS validation --}}
@push('scripts')
<script>
    // Bootstrap 5 client-side form validation
    (() => {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
@endpush
@endsection
