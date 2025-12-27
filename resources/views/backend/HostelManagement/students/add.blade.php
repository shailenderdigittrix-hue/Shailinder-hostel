@extends('backend.layouts.master')
@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>{{ isset($student) ? 'Edit Student' : 'Add New Student' }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ isset($student) ? route('students.update', $student->id) : route('students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($student))
                    @method('PUT')
                @endif
                
                <div class="row mb-3">
                    <label for="first_name" class="col-sm-2 col-form-label">First Name<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <input type="text" 
                               name="first_name" 
                               id="first_name" 
                               class="form-control @error('first_name') is-invalid @enderror" 
                               value="{{ old('first_name', $student->first_name ?? '') }}" 
                               placeholder="First Name" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="last_name" class="col-sm-2 col-form-label">Last Name <p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                               value="{{ old('last_name', $student->last_name ?? '') }}" placeholder="Last Name" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="enrollment_no" class="col-sm-2 col-form-label">Enrollment No <p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <input type="text" name="enrollment_no" id="enrollment_no" class="form-control @error('enrollment_no') is-invalid @enderror" 
                            value="{{ old('enrollment_no', $student->enrollment_no ?? '') }}" placeholder="Enrollment Number" required>
                        @error('enrollment_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="row mb-3">
                    <label for="gender" class="col-sm-2 col-form-label">Gender <p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="" disabled selected>Select Gender</option>
                            @foreach(['Male', 'Female', 'Other'] as $gender)
                                <option value="{{ $gender }}" 
                                    {{ old('gender', $student->gender ?? '') === $gender ? 'selected' : '' }}>
                                    {{ $gender }}
                                </option>
                            @endforeach
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="date_of_birth" class="col-sm-2 col-form-label">Date of Birth</label>
                    <div class="col-sm-10">
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" 
                               value="{{ old('date_of_birth', isset($student->date_of_birth) ? $student->date_of_birth->format('Y-m-d') : '') }}" >
                        @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $student->email ?? '') }}" placeholder="Email" >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="phone" class="col-sm-2 col-form-label">Phone</label>
                    <div class="col-sm-10">
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone', $student->phone ?? '') }}" placeholder="Phone" >
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="course_id" class="col-sm-2 col-form-label">Course<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                            <option value="" disabled {{ old('course_id', $student->course_id ?? '') ? '' : 'selected' }}>Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" 
                                    {{ old('course_id', $student->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="row mb-3">
                    <label for="year" class="col-sm-2 col-form-label">Year<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <input type="number" min="1" max="10" name="year" id="year" class="form-control @error('year') is-invalid @enderror" 
                               value="{{ old('year', $student->year ?? '') }}" placeholder="Year" required>
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="address" class="col-sm-2 col-form-label">Address<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Address" required>{{ old('address', $student->address ?? '') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="admission_date" class="col-sm-2 col-form-label">Admission Date<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <input type="date" name="admission_date" id="admission_date" class="form-control @error('admission_date') is-invalid @enderror" 
                               value="{{ old('admission_date', isset($student->admission_date) ? $student->admission_date->format('Y-m-d') : '') }}" required>
                        @error('admission_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Room Allocation Start - -->
                <!-- Hostel -->
                <div class="row mb-3">
                    <label for="hostel_id" class="col-sm-2 col-form-label">Hostel<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <select name="hostel_id" id="hostel_id" class="form-select" required>
                            <option value="">Select Hostel</option>
                            @foreach($hostels as $hostel)
                                <option value="{{ $hostel->id }}">{{ $hostel->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Building -->
                <div class="row mb-3">
                    <label for="building_id" class="col-sm-2 col-form-label">Building<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <select name="building_id" id="building_id" class="form-select" required>
                            <option value="">Select Building</option>
                        </select>
                    </div>
                </div>

                <!-- Floor -->
                <div class="row mb-3">
                    <label for="floor" class="col-sm-2 col-form-label">Floor<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <select name="floor" id="floor" class="form-select" required>
                            <option value="">Select Floor</option>
                        </select>
                    </div>
                </div>

                <!-- Room -->
                <div class="row mb-3">
                    <label for="room_id" class="col-sm-2 col-form-label">Room<p style="color: red">*</p></label>
                    <div class="col-sm-10">
                        <select name="room_id" id="room_id" class="form-select" required> 
                            <option value="">Select Room</option>
                        </select>
                    </div>
                </div>
                <!-- Room Allocation End --- -->

                <div class="row mb-3">
                    <label for="profile_image" class="col-sm-2 col-form-label">Profile Image</label>
                    <div class="col-sm-10">
                        <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*" onchange="previewImage(this)">
                        <img id="preview" style="max-height: 150px; margin-top: 10px; display: none;">
                    </div>
                    <script>
                        function previewImage(input) {
                            const preview = document.getElementById('preview');
                            const file = input.files[0];

                            if (file) {
                                preview.src = URL.createObjectURL(file);
                                preview.style.display = 'block';
                            } else {
                                preview.src = '';
                                preview.style.display = 'none';
                            }
                        }
                    </script>
                </div>

                <div class="row">
                    <div class="offset-sm-2 col-sm-10">
                        <button type="submit" class="btn btn-primary">{{ isset($student) ? 'Update' : 'Add Student' }}</button>
                        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        // Disable all selects except hostel initially
        $('#building_id, #floor, #room_id').prop('disabled', true);

        // When hostel is selected
        $('#hostel_id').change(function () {
            const hostelId = $(this).val();

            // Reset and disable other fields
            $('#building_id').html('<option value="">Loading...</option>').prop('disabled', true);
            $('#floor').html('<option value="">Select Floor</option>').prop('disabled', true);
            $('#room_id').html('<option value="">Select Room</option>').prop('disabled', true);

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

        // When building is selected
        $('#building_id').change(function () {
            const selectedOption = $('option:selected', this);
            const floors = selectedOption.data('floors');

            $('#floor').html('<option value="">Select Floor</option>').prop('disabled', true);
            $('#room_id').html('<option value="">Select Room</option>').prop('disabled', true);

            if (floors !== undefined) {
                let floorOptions = '<option value="">Select Floor</option>';
                for (let i = 0; i < floors; i++) {
                    floorOptions += `<option value="${i}">Floor ${i}</option>`;
                }
                $('#floor').html(floorOptions).prop('disabled', false);
            }
        });

        // When floor is selected
        $('#floor').change(function () {
            const buildingId = $('#building_id').val();
            const floor = $(this).val();

            $('#room_id').html('<option value="">Select Room</option>').prop('disabled', true);

            if (buildingId && floor !== '') {
                $('#room_id').html('<option value="">Loading...</option>');
                $.get(`/buildings/${buildingId}/rooms?floor=${floor}`, function (data) {
                    let options = '';
                    let hasAvailableRoom = false;
                    data.forEach(room => {
                        console.log('dsfjknfnf kdsnf kdnsfkdns fknsdf', room);

                        const available = room.capacity - room.current_occupancy;
                        const is_active = room.is_active;
                        if (available > 0 && is_active === 1) {
                            hasAvailableRoom = true;
                            options += `<option value="${room.id}">${room.room_number} (${available} slots available)</option>`;
                        }
                    });
                    if (hasAvailableRoom) {
                        options = '<option value="">Select Room</option>' + options;
                    } else {
                        options = '<option disabled selected>No rooms available</option>';
                    }
                    $('#room_id').html(options).prop('disabled', false);
                });
            }
        });
    });
</script>

@endpush

@endsection
