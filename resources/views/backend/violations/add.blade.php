@extends('backend.layouts.master')
@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-pen-nib me-2"></i>
                Record Disciplinary Violation
            </h4>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please correct the errors below:
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <form action="{{ route('violations.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                    <select name="student_id" id="student_id" class="form-select select2" required>
                        <option value="">-- Select Student --</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->user->name ?? 'Unknown' }} ({{ $student->admission_number ?? 'ID:'.$student->id }})
                        </option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback">Please select a student.</div>
                </div>
                <div class="mb-3">
                    <label for="violation_date" class="form-label">Violation Date <span class="text-danger">*</span></label>
                    <input type="date" name="violation_date" id="violation_date" class="form-control" required>
                    <div class="invalid-feedback">Please provide a valid date.</div>
                </div>
                <div class="mb-3">
                    <label for="violation_type_id" class="form-label">Violation Type <span class="text-danger">*</span></label>
                    <div class="input-group mb-3">
                        <select name="violation_type_id" id="violation_type_id" class="form-select" required>
                            <option value="">-- Select Violation Type --</option>
                            @foreach($violationTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addViolationTypeModal">
                        + Add New Type
                        </button>
                    </div>
                    <div class="invalid-feedback">Please select a violation type.</div>
                </div>
                <div class="mb-3">
                    <label for="details" class="form-label">Details</label>
                    <textarea name="details" id="details" class="form-control" rows="4" placeholder="Describe the incident..."></textarea>
                </div>
                {{-- Fine Amount --}}
                <div class="mb-3">
                    <label for="fine_amount" class="form-label">Fine Amount</label>
                    <input type="number" name="fine_amount" id="fine_amount" step="0.01" class="form-control" placeholder="e.g., 100.00">
                </div>
                {{-- Fine Reason --}}
                <div class="mb-3">
                    <label for="fine_reason" class="form-label">Fine Reason</label>
                    <input type="text" name="fine_reason" id="fine_reason" class="form-control" placeholder="e.g., Misbehavior, Late Attendance">
                </div>
                {{-- Fine Issued Date --}}
                <div class="mb-3">
                    <label for="fine_issued_at" class="form-label">Fine Issued Date</label>
                    <input type="date" name="fine_issued_at" id="fine_issued_at" class="form-control" value="{{ \Carbon\Carbon::now()->toDateString() }}">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="{{ route('violations.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Record Violation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="addViolationTypeModal" tabindex="-1" aria-labelledby="addViolationTypeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addViolationTypeForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addViolationTypeLabel">Add New Violation Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_violation_type_name" class="form-label">Violation Type Name</label>
                        <input type="text" class="form-control" id="new_violation_type_name" name="name" required>
                        <div class="invalid-feedback">Please enter a violation type name.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Type</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        $('#student_id').select2({
            placeholder: "-- Select Student --",
            allowClear: true
        });
    });
    
    
    // Bootstrap 5 form validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    
    
    document.getElementById('addViolationTypeForm').addEventListener('submit', function(e) {
        e.preventDefault();
    
        let nameInput = document.getElementById('new_violation_type_name');
        let name = nameInput.value.trim();
    
        if(!name) {
            nameInput.classList.add('is-invalid');
            return;
        }
        nameInput.classList.remove('is-invalid');
    
        fetch('{{ route("violation-types.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if(data.id) {
                // Add new option to dropdown and select it
                let select = document.getElementById('violation_type_id');
                let option = new Option(data.name, data.id, true, true);
                select.appendChild(option);
    
                // Close modal
                let modal = bootstrap.Modal.getInstance(document.getElementById('addViolationTypeModal'));
                modal.hide();
    
                // Clear input
                nameInput.value = '';
            } else if(data.errors) {
                alert('Error: ' + JSON.stringify(data.errors));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add violation type.');
        });
    });
    
</script>
@endpush
@endsection