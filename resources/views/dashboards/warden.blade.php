@extends('backend.layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">

        <!-- Students -->
        <div class="col-md-4">
            <a href="{{ route('students.index') }}" class="text-decoration-none">
                <div class="card text-bg-primary shadow h-100 border-0 rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-white fw-semibold mb-2">Number of Students</h6>
                            <h2 class="text-white fw-bold">{{ $students }}</h2>
                        </div>
                        <i data-feather="users" class="feather-icon text-white" style="width: 48px; height: 48px;"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Rooms -->
        <div class="col-md-4">
            <a href="{{ route('rooms.index') }}" class="text-decoration-none">
                <div class="card text-bg-success shadow h-100 border-0 rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-white fw-semibold mb-2">Number of Rooms</h6>
                            <h2 class="text-white fw-bold">{{ $rooms }}</h2>
                        </div>
                        <i data-feather="home" class="feather-icon text-white" style="width: 48px; height: 48px;"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Room Allocations -->
        <div class="col-md-4">
            <a href="{{ route('warden.alloted_rooms_list') }}" class="text-decoration-none">
                <div class="card text-bg-danger shadow h-100 border-0 rounded-4">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-white fw-semibold mb-2">Room Allocations</h6>
                            <h2 class="text-white fw-bold">{{ $room_allocations }}</h2>
                        </div>
                        <i data-feather="grid" class="feather-icon text-white" style="width: 48px; height: 48px;"></i>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection
