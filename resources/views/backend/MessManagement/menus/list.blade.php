@extends('backend.layouts.master') {{-- Use your custom layout file --}}

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Mess Management</h5> 
                    <a class="btn btn-secondary" href="{{ route('mess.create') }}">Add Mess</a>
                </div>
                
                <div class="card-body">
                    <table id="usersTable" class="table table-striped table-hover align-middle">
                        <thead>
                            <tr class="fw-bold">
                                <th>#</th>
                                <th>Name</th>
                                <th>Hostel Name</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $user)
                                <tr>
                                    <th scope="row">{{ $loop->iteration + ($data->currentPage() - 1) * $data->perPage() }}</th>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->hostel->name }}</td>
                                      <td>
                                      <div class="d-flex "> 
                                        <a href="{{ route('mess.edit', $user->id) }}" class="btn btn-sm btn-warning me-1" title="Edit User">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a target="_blank" href="https://crmiitm.digittrix.com/public/{{@$user->menu_document_upload}}" class="btn btn-sm btn-danger me-1" title="Edit User">
                                           <i class="bi bi-eye"></i> View
                                        </a>
                                        <form action="{{ route('mess.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                             <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                       </div>

                                      <!--   <form action="{{ route('fooditems.destroy', $user->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this user?');" title="Delete User">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">
                                                <i class="bi bi-trash"></i>Delete
                                            </button>
                                        </form> -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No Mess found.</td>
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
