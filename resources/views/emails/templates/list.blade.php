@extends('backend.layouts.master')

@section('content')
@push('styles')
<style>
    .table-actions a { margin-right: 5px; }
</style>
@endpush

<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Email Templates</h5>
                    <a href="{{ route('email-templates.create') }}" class="btn btn-sm btn-outline-primary">Add New Template</a>
                </div>

                <div class="card-body">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr class="fw-bold">
                                <th>#</th>
                                <th>Template Name</th>
                                <th>Subject</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                                <tr>
                                    <th scope="row">{{ $loop->iteration + ($templates->currentPage() - 1) * $templates->perPage() }}</th>
                                    <td>{{ $template->name }}</td>
                                    <td>{{ $template->subject }}</td>
                                    <td>{{ $template->created_at->format('Y-m-d') }}</td>
                                    <td class="table-actions">
                                        <a href="{{ route('email-templates.show', $template->id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('email-templates.edit', $template->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('email-templates.destroy', $template->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No email templates found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end mt-4">
                        {{ $templates->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
