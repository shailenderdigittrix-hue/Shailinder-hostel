@extends('backend.layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>Hostel Details</h4>
            <a href="{{ route('hostels.edit', $hostel->id) }}" class="btn btn-primary btn-sm">Edit</a>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $hostel->name }}</p>
            <p><strong>Code:</strong> {{ $hostel->code }}</p>
            <p><strong>Gender:</strong> {{ ucfirst($hostel->gender) }}</p>
            <p><strong>Building:</strong> {{ $hostel->building }}</p>
            <p><strong>Total Capacity:</strong> {{ $hostel->total_capacity }}</p>
            <p><strong>Warden:</strong> {{ $hostel->warden }}</p>
            <p><strong>Contact:</strong> {{ $hostel->contact }}</p>
            <p><strong>Email:</strong> {{ $hostel->email }}</p>
            <p><strong>Address:</strong> {{ $hostel->address }}</p>
            <p><strong>Facilities:</strong> 
                @if ($hostel->facilities)
                    {{ implode(', ', json_decode($hostel->facilities)) }}
                @else
                    None
                @endif
            </p>
            <p><strong>Status:</strong> 
                <span class="badge bg-{{ $hostel->is_active ? 'success' : 'secondary' }}">
                    {{ $hostel->is_active ? 'Active' : 'Inactive' }}
                </span>
            </p>
        </div>
    </div>
</div>
@endsection
