@extends('backend.layouts.master') {{-- Use your custom layout file --}}

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Food Item Management</h5> 
                    <a href="{{ route('fooditems.create') }}" class="btn btn-secondary btn-sm">
                        Add New
                    </a> 
                </div>
                <div class="card-body">
                    <table id="usersTable" class="table table-striped table-hover align-middle">
                        <thead>
                            <tr class="fw-bold">
                                <th>#</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Calories</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $user)
                                <tr>
                                    <th scope="row">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->category }}</td>
                                    <td>{{ $user->description }}</td>
                                    <td>{{ $user->calories }}</td>
                                    <td>{{ $user->status }}</td>
                                    <td>
                                        <a href="{{ route('fooditems.edit', $user->id) }}" class="btn btn-sm btn-warning me-1" title="Edit User">
                                            <i class="bi bi-pencil-square"></i>Edit
                                        </a>

                                        <form action="{{ route('fooditems.destroy', $user->id) }}" method="POST" class="d-inline"
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
                        {{ $data->links('pagination::bootstrap-5') }}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
