@extends('backend.layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Add New Room</h4>
            <a href="{{ route('rooms.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf

                <!-- Hostel -->
                <div class="mb-3">
                    <label for="hostel_id" class="col-sm-2 col-form-label">Hostel</label>
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

                <!-- Building -->
                <div class="mb-3">
                    <label for="building_id" class="col-sm-2 col-form-label">Building</label>
                    <select name="building_id" id="building_id" class="form-select @error('building_id') is-invalid @enderror" required>
                        <option value="">Select Building</option>
                    </select>
                    @error('building_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Floor -->
                <div class="mb-3">
                    <label for="floor" class="col-sm-2 col-form-label">Floor<span class="text-danger">*</span></label>
                    <select name="floor" id="floor" class="form-select @error('floor') is-invalid @enderror" required>
                        <option value="">Select Floor</option>
                    </select>
                    @error('floor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Room Number -->
                <div class="mb-3">
                    <label for="room_number" class="form-label">Room Number <span class="text-danger">*</span></label>
                    <input type="text" name="room_number" id="room_number" class="form-control @error('room_number') is-invalid @enderror"
                        value="{{ old('room_number') }}" required>
                    @error('room_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Capacity -->
                <div class="mb-3">
                    <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="capacity" id="capacity" class="form-control @error('capacity') is-invalid @enderror"
                        value="{{ old('capacity') }}" required>
                    @error('capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Room Type -->
                <div class="mb-3">
                    <label for="room_type" class="form-label">Room Type <span class="text-danger">*</span></label>
                    <select name="room_type" id="room_type" class="form-select @error('room_type') is-invalid @enderror" required>
                        <option value="">Select Room Type</option>
                        @foreach(['Single', 'Double', 'Triple'] as $type)
                            <option value="{{ $type }}" {{ old('room_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Is Active -->
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-success">Save Room</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Make sure jQuery is loaded (can be placed in your master layout) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $('#building_id, #floor').prop('disabled', true);

        $('#hostel_id').change(function () {
            const hostelId = $(this).val();

            $('#building_id').html('<option value="">Loading...</option>').prop('disabled', true);
            $('#floor').html('<option value="">Select Floor</option>').prop('disabled', true);

            if (hostelId) {
                $.get(`/hostels/${hostelId}/buildings`, function (data) {
                    let options = '<option value="">Select Building</option>';
                    data.forEach(building => {
                        options += `<option value="${building.id}" data-floors="${building.number_of_floors}">${building.name}</option>`;
                    });
                    $('#building_id').html(options).prop('disabled', false);
                });
            }
        });

        $('#building_id').change(function () {
            const selectedOption = $('option:selected', this);
            const floors = selectedOption.data('floors');

            $('#floor').html('<option value="">Select Floor</option>').prop('disabled', true);

            if (floors !== undefined) {
                let floorOptions = '<option value="">Select Floor</option>';
                for (let i = 1; i <= floors; i++) {
                    floorOptions += `<option value="${i}">Floor ${i}</option>`;
                }
                $('#floor').html(floorOptions).prop('disabled', false);
            }
        });
    });
</script>
@endpush
