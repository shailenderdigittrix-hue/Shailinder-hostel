@extends('backend.layouts.master')
@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Notifications</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr class="fw-bold">
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Is Read</th>
                                    <th>Date</th>
                                    <!-- <th>Actions</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $key => $notification)
                                    <tr>
                                        <td>{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $key + 1 }}</td>
                                        <td>{{ $notification->title }}</td>
                                        <td>{{ Str::limit($notification->message, 50) }}</td>
                                        <td>
                                            @if($notification->is_read)
                                                <span class="badge bg-success">Read</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Unread</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="text-nowrap">
                                            <div class="btn-group" role="group" aria-label="Actions">
                                                <!-- Action buttons (View/Edit/Delete) can go here -->
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No notifications found.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $notifications->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
