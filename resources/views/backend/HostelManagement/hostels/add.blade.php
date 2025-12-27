@extends('backend.layouts.master')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Add New Hostel</h4>
            <a href="{{ route('hostels.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
            <form action="{{ route('hostels.store') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Hostel Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <!-- Code -->
                <div class="mb-3">
                    <label for="code" class="form-label">Hostel Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                        value="{{ old('code') }}" required>
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>


                <!-- Device Serial Number -->
              <!-- Device Serial Number -->
                <div class="mb-3">
                    <label for="device_serial_no" class="form-label">Device Serial Number <span class="text-danger">*</span></label>

                    <div class="devices-list">
                        @php
                            $oldDevices = old('device_serial_no', []);
                            if (empty($oldDevices)) {
                                $oldDevices = ['']; // one empty input by default
                            }
                        @endphp

                        @foreach($oldDevices as $idx => $val)
                            <div class="device-item d-flex align-items-center mt-2">
                                <input
                                    type="text"
                                    name="device_serial_no[]"
                                    class="form-control @error('device_serial_no.' . $idx) is-invalid @enderror"
                                    value="{{ $val }}"
                                    placeholder="Enter device serial number"
                                    required
                                >
                                <button type="button" class="btn btn-danger btn-sm ms-2 remove-device">Delete</button>

                                @error('device_serial_no.' . $idx)
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <button class="btn btn-primary mt-2" id="addDeviceBtn" type="button">Add Another Device</button>

                    @error('device_serial_no')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Gender -->
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="co-ed" {{ old('gender') == 'co-ed' ? 'selected' : '' }}>Co-ed</option>
                    </select>
                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Building -->
                <!-- <div class="mb-3">
                    <label for="building" class="form-label">Building</label>
rtre                        value="{{ old('building') }}">
                    @error('building') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div> -->

                <!-- Total Capacity -->
                <div class="mb-3">
                    <label for="total_capacity" class="form-label">Total Capacity <span class="text-danger">*</span></label>
                    <input type="number" name="total_capacity" class="form-control @error('total_capacity') is-invalid @enderror"
                        value="{{ old('total_capacity') }}" required>
                    @error('total_capacity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Warden -->
                <div class="mb-3">
                    <label for="warden" class="form-label">Warden Name</label>
                    <select name="warden" id="warden" class="form-select @error('warden') is-invalid @enderror">
                        <option value="">Select Warden</option>
                        @foreach($wardens as $warden)
                            <option value="{{ $warden->id }}" {{ old('warden') == $warden->id ? 'selected' : '' }}>
                                {{ $warden->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('warden')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Contact -->
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact</label>
                    <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror"
                        value="{{ old('contact') }}">
                    @error('contact') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Address -->
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                        rows="3">{{ old('address') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Facilities -->
                <div class="mb-3">
                    <label for="facilities" class="form-label">Facilities (Select multiple)</label>
                    <select name="facilities[]" class="form-select @error('facilities') is-invalid @enderror" multiple>
                        <option value="wifi" {{ collect(old('facilities'))->contains('wifi') ? 'selected' : '' }}>Wi-Fi</option>
                        <option value="laundry" {{ collect(old('facilities'))->contains('laundry') ? 'selected' : '' }}>Laundry</option>
                        <option value="gym" {{ collect(old('facilities'))->contains('gym') ? 'selected' : '' }}>Gym</option>
                        <option value="mess" {{ collect(old('facilities'))->contains('mess') ? 'selected' : '' }}>Mess</option>
                        <option value="tv" {{ collect(old('facilities'))->contains('tv') ? 'selected' : '' }}>TV</option>
                        <!-- Add more if needed -->
                    </select>
                    @error('facilities') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Is Active -->
                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_active" class="form-check-input"
                        id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Is Active</label>
                </div>

                <button type="submit" class="btn btn-success">Save Hostel</button>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const deviceList = document.querySelector('.devices-list');
    const addBtn = document.getElementById('addDeviceBtn');

    addBtn.addEventListener('click', function (e) {
        e.preventDefault();

        // wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'device-item d-flex align-items-center mt-2';

        // input
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'device_serial_no[]';
        input.className = 'form-control';
        input.placeholder = 'Enter device serial number';
        input.required = true;

        // delete button
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-danger btn-sm ms-2 remove-device';
        deleteBtn.textContent = 'Delete';

        // assemble
        wrapper.appendChild(input);
        wrapper.appendChild(deleteBtn);
        deviceList.appendChild(wrapper);
    });

    // event delegation for remove
    deviceList.addEventListener('click', function (e) {
        if (e.target && e.target.matches('.remove-device')) {
            const wrapper = e.target.closest('.device-item');
            if (wrapper) {
                wrapper.remove();
            }
        }
    });
});
</script>
@endsection
