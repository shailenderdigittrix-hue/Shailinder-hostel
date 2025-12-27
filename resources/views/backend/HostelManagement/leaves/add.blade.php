@extends('backend.layouts.master')
@section('content')

@push('styles')

@endpush
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Apply for Leave</h5>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('student-leaves.index') }}" class="btn btn-secondary btn-sm mb-0">
                            List
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="card-body">
                        <form action="{{ route('student-leaves.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="student_id" class="form-label">Select Student</label>
                                <select name="student_id" id="student_id" class="form-select" required>
                                    <option value="" disabled selected>-- Choose a student --</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->user->name ?? $student->first_name . ' ' . $student->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">Please select a student.</div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="from_date" class="form-label">From Date</label>
                                    <input type="date" id="from_date" name="from_date" class="form-control" required value="{{ old('from_date', @$leave->from_date) }}">
                                    <div class="invalid-feedback">Please select a valid from date.</div>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="to_date" class="form-label">To Date</label>
                                    <input type="date" id="to_date" name="to_date" class="form-control" required value="{{ old('to_date', @$leave->to_date) }}">
                                    <div class="invalid-feedback">Please select a valid to date.</div>
                                </div>
                            </div>
                                
                            <!-- <div class="mb-3">
                                <label for="from_date" class="form-label">From Date</label>
                                <input type="date" id="from_date" name="from_date" class="form-control" required value="{{ old('from_date') }}">
                                <div class="invalid-feedback">Please select a valid from date.</div>
                            </div>

                            <div class="mb-3">
                                <label for="to_date" class="form-label">To Date</label>
                                <input type="date" id="to_date" name="to_date" class="form-control" required value="{{ old('to_date') }}">
                                <div class="invalid-feedback">Please select a valid to date.</div>
                            </div> -->

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason</label>
                                <input type="text" id="reason" name="reason" class="form-control" required value="{{ old('reason') }}">
                                <div class="invalid-feedback">Please enter a reason for leave.</div>
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks (optional)</label>
                                <textarea id="remarks" name="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="document" class="form-label">Upload Document (optional - PDF/JPG/PNG)</label>
                                <input type="file" id="document" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')

@endpush

@endsection
