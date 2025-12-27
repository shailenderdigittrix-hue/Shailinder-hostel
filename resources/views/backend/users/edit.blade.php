@extends('backend.layouts.master') {{-- Use your custom layout --}}

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Edit User</h5> 
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                        Back
                    </a> 
                </div>

                <div class="card-body">
                    {{-- Edit User Form --}}
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Name <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="form-control" 
                                value="{{ old('name', $user->name) }}" 
                                required>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                class="form-control" 
                                value="{{ old('email', $user->email) }}" 
                                required>
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password 
                                <small class="text-muted">(leave blank to keep current)</small>
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control">
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Confirm Password
                            </label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                class="form-control">
                        </div>

                        {{-- Roles --}}
                        <div class="mb-3">
                            <label for="roles" class="form-label">
                                Assign Role(s) <span class="text-danger">*</span>
                            </label>
                            <select 
                                name="roles[]" 
                                id="roles" 
                                class="form-select"  
                                required>
                                @foreach ($roles as $role)
                                    @continue($role->id == 1) {{-- Skip Super Admin --}}
                                    <option 
                                        value="{{ $role->name }}" 
                                        {{ in_array($role->name, $user->roles->pluck('name')->toArray()) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Manager --}}
                        <div class="mb-3">
                            <label for="manager_id" class="form-label">
                                Manager (optional)
                            </label>
                            <select 
                                name="manager_id" 
                                id="manager_id" 
                                class="form-select">
                                <option value="">-- None --</option>
                                @foreach ($managers as $manager)
                                    <option 
                                        value="{{ $manager->id }}" 
                                        {{ $user->manager_id == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }} ({{ $manager->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                Update User
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
        </div> <!-- /.col-lg-12 -->
    </div> <!-- /.row -->
</div> <!-- /.content-wrapper -->
@endsection
