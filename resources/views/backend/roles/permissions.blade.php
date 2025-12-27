@extends('backend.layouts.master')
@section('content')
<div class="content-wrapper py-4">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Manage Permissions for Role: 
                    <strong class="text-primary">{{ ucfirst($role->name) }}</strong>
                </h5>
                <a href="{{ route('roles.list') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Permission Name</th>
                                <th>Assign</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($permissions as $permission)
                                <tr>
                                    <td class="text-start">
                                        {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                    </td>
                                    <td>
                                        <input 
                                            type="checkbox"
                                            class="permission-checkbox"
                                            data-role-id="{{ $role->id }}"
                                            data-permission="{{ $permission->name }}"
                                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="permission-alert" class="mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.permission-checkbox');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const roleId = this.dataset.roleId;
                const permission = this.dataset.permission;
                const checked = this.checked;

                fetch("{{ route('admin.role.permissions.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        role_id: roleId,
                        permission: permission,
                        assign: checked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Permission updated successfully!');
                    } else {
                        toastr.error('Failed to update permission!');
                    }
                })
                .catch(error => {
                    toastr.error('Server error occurred!');
                    console.error("Error:", error);
                });
            });
        });
    });
</script>
@endpush

