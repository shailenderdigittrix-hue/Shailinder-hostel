@extends('backend.layouts.master')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Roles Management</h5>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" id="addNewRoleBtn">
                        Add New Role
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="usersTable"  class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 25%;">Role Name</th>
                                    <th style="width: 30%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                <tr>
                                    <td>{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</td>
                                    <td class="text-capitalize">{{ $role->name }}</td>
                                    <td>
                                        <div class="btn-group gap-1">
                                            <a href="{{ route('admin.role.permissions', ['id' => $role->id]) }}" 
                                                class="btn btn-success btn-sm" title="Manage Permissions">
                                                <i class="bi bi-shield-lock"></i> Permissions
                                            </a>

                                            <button 
                                                type="button" 
                                                class="btn btn-primary btn-sm edit-role-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#exampleModal"
                                                data-id="{{ $role->id }}"
                                                data-name="{{ $role->name }}"
                                                title="Edit Role">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>

                                            <form action="{{ route('role.destroy') }}" method="POST" onsubmit="return confirm('Delete this role?')">
    @csrf
    <input type="hidden" name="id" value="{{ $role->id }}">
    <button type="submit" class="btn btn-danger">Delete</button>
</form>

                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-muted text-center py-3">No roles found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($roles->hasPages())
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-center">
                        {{ $roles->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.roleStore') }}" id="roleForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="role_id">
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold" id="exampleModalLabel">Add Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role_name" class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="role_name" name="name" placeholder="Enter role name" required>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        // Add New Role
        $('#addNewRoleBtn').on('click', function () {
            $('#exampleModalLabel').text('Add Role');
            $('#roleForm').attr('action', "{{ route('admin.roleStore') }}");
            $('#formMethod').val('POST');
            $('#role_id').val('');
            $('#role_name').val('');
            $('#modalSubmitBtn').text('Add');
        });

        // Edit Role
        $('.edit-role-btn').on('click', function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#exampleModalLabel').text('Edit Role');
            $('#roleForm').attr('action', "{{ route('admin.roleStore') }}");
            $('#formMethod').val('POST');
            $('#role_id').val(id);
            $('#role_name').val(name);
            $('#modalSubmitBtn').text('Update');
        });
    });
</script>
@endpush

@endsection
