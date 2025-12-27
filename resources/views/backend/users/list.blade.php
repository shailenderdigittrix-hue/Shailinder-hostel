@extends('backend.layouts.master') {{-- Use your custom layout file --}}

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">User Management</h5> 
                    <a href="{{ route('admin.users.create') }}" class="btn btn-secondary btn-sm">
                        Add New
                    </a> 
                </div>
                <div class="card-body">
                    <table id="usersTable" class="table table-striped table-hover align-middle">
                        <thead>
                            <tr class="fw-bold">
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Manager</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <th scope="row">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-info text-dark me-1">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $user->manager?->name ?? '-' }}</td>
                                    <td>
                                        <!-- <a href="{{ route('admin.users.permissions.edit', $user->id) }}" class="btn btn-sm btn-info me-1" title="Edit Permissions">
                                            <i class="bi bi-lock"></i> Edit Permissions
                                        </a> -->
                                        <!-- @if($user->permissions->count() > 0)
                                            <a href="{{ route('admin.users.permissions.edit', $user->id) }}" class="btn btn-sm btn-info me-1" title="Edit Permissions">
                                                <i class="bi bi-lock"></i> Edit Permissions
                                            </a>
                                        @else
                                            <span class="btn btn-sm btn-secondary me-1 disabled" aria-disabled="true" title="No permission">
                                                No permission
                                            </span>
                                        @endif -->

                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning me-1" title="Edit User">
                                            <i class="bi bi-pencil-square"></i>Edit
                                        </a>

                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this user?');" title="Delete User">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">
                                                <i class="bi bi-trash"></i>Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
