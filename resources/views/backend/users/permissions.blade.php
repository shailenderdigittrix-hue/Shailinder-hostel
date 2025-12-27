@extends('backend.layouts.master') {{-- Use your custom layout file --}}
@push('styles')

@endpush

@section('content')
<div class="container">
    <h3>Manage Permissions for {{ $user->name }}</h3>

    <form method="POST" action="{{ route('admin.users.permissions.update', $user->id) }}">
        @csrf
        @method('PUT')

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>View</th>
                    <th>Add</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedPermissions as $module => $permissions)
                    <tr>
                        <td>{{ ucfirst($module) }}</td>

                        @php
                            $actions = ['view', 'add', 'edit', 'delete'];
                        @endphp

                        @foreach ($actions as $action)
                            @php
                                $permName = $module . '.' . $action;
                                $permission = $permissions->firstWhere('name', $permName);
                            @endphp

                            <td>
                                @if ($permission)
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                        {{ in_array($permName, $userPermissions) ? 'checked' : '' }}>
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">Update Permissions</button>
    </form>

    @if (session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')

@endpush
