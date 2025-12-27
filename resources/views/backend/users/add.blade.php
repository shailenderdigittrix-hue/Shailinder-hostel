@extends('backend.layouts.master') {{-- Use your custom layout --}}

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">User Management</h5> 
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        Back
                    </a> 
                </div>
                <div class="card-body">
                    {{-- User Create Form --}}
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" required value="{{ old('name') }}">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control" required value="{{ old('email') }}">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="roles" class="form-label">Assign Role(s) <span class="text-danger">*</span></label>
                            <select name="roles[]" id="roles" class="form-select" multiple required>
                                @foreach ($roles as $role)
                                    @if($role->id !=1)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="manager_id" class="form-label">Manager (optional)</label>
                            <select name="manager_id" id="manager_id" class="form-select">
                                <option value="">-- None --</option>
                                @foreach ($managers as $manager)
                                    <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Create User</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
