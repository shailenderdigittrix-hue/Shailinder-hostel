@extends('backend.layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Permissions</h5>
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-secondary btn-sm">Add Permission</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="fw-bold">
                    <tr>
                        <th>#</th>
                        <th>Permission Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr>
                            <td>{{ $loop->iteration + ($permissions->currentPage() - 1) * $permissions->perPage() }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>
                                <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this permission?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No permissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-4">
                {{ $permissions->links('pagination::bootstrap-5') }}
            </div>


        </div>
    </div>
</div>
@endsection
