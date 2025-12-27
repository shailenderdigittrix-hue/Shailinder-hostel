@extends('backend.layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Add Permission</h5>
            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Permission Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" required value="{{ old('name') }}">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Create Permission</button>
                <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
