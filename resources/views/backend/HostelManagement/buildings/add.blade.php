@extends('backend.layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Add New Building</h4>
            <a href="{{ route('buildings.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
            <form action="{{ route('buildings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Hostel Dropdown --}}
                <div class="mb-3">
                    <label for="hostel_id" class="form-label">Hostel <span class="text-danger">*</span></label>
                    <select name="hostel_id" id="hostel_id" class="form-select @error('hostel_id') is-invalid @enderror" required>
                        <option value="">Select Hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>
                                {{ $hostel->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('hostel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Building Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Building Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Number of Floors --}}
                <div class="mb-3">
                    <label for="number_of_floors" class="form-label">Number of Floors</label>
                    <input type="number" name="number_of_floors" id="number_of_floors"
                        class="form-control @error('number_of_floors') is-invalid @enderror"
                        value="{{ old('number_of_floors') }}" min="0">
                    @error('number_of_floors') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Building Image --}}
                <div class="mb-3">
                    <label for="image" class="form-label">Building Image</label>
                    <input type="file" name="image" id="image"
                        class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-success">Save Building</button>
            </form>
        </div>
    </div>
</div>
@endsection
