@extends('backend.layouts.master')

@push('styles')
@endpush

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Edit Building</h4>
            <a href="{{ route('buildings.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
            <form action="{{ route('buildings.update', $building->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Hostel Dropdown --}}
                <div class="mb-3">
                    <label for="hostel_id" class="form-label">Hostel <span class="text-danger">*</span></label>
                    <select name="hostel_id" id="hostel_id" class="form-select @error('hostel_id') is-invalid @enderror" required>
                        <option value="">Select Hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ (old('hostel_id', $building->hostel_id) == $hostel->id) ? 'selected' : '' }}>
                                {{ $hostel->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('hostel_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Building Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Building Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $building->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Number of Floors --}}
                <div class="mb-3">
                    <label for="number_of_floors" class="form-label">Number of Floors</label>
                    <input type="number" name="number_of_floors" id="number_of_floors"
                        class="form-control @error('number_of_floors') is-invalid @enderror"
                        value="{{ old('number_of_floors', $building->number_of_floors) }}" min="0">
                    @error('number_of_floors') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Existing Image --}}
                @if($building->image)
                <div class="mb-3">
                    <label class="form-label d-block">Current Image:</label>
                    <img src="{{  $building->image }}" alt="Building Image" width="150">
                    <input type="hidden" value="{{  $building->image }}" name="set_image">
                </div>
                @endif

                {{-- Upload New Image --}}
                <div class="mb-3">
                    <label for="image" class="form-label">Change Image (optional)</label>
                    <input type="file" name="image" id="image"
                        class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-success">Update Building</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
