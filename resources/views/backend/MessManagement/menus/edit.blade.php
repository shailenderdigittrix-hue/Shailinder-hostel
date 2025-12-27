@extends('backend.layouts.master')

@push('styles')
@endpush

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Edit Mess</h4>
            <a href="{{ route('mess.list', $data->id) }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
            {{-- Important: method should be POST, with @method('PUT') --}}
            <form action="{{ route('mess.update', @$data->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Mess Name --}}
                <div class="mb-3">
                    <label for="mess_name" class="form-label">Mess Name <span class="text-danger">*</span></label>
                    <input type="text" name="mess_name" id="mess_name" 
                        class="form-control @error('mess_name') is-invalid @enderror"
                        value="{{ old('mess_name', @$data->name) }}" required>
                    @error('mess_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Hostel Dropdown --}}
                <div class="mb-3">
                    <label for="hostel_id" class="form-label">Hostel <span class="text-danger">*</span></label>
                    <select name="hostel_id" id="hostel_id" 
                        class="form-select @error('hostel_id') is-invalid @enderror" required>
                        <option value="">Select Hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ @$data->hostel_id == $hostel->id ? 'selected' : '' }}>
                                {{ $hostel->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('hostel_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mess Menu PDF Upload --}}
                <div class="mb-3">
                    <label for="menu_document" class="form-label">Menu Document <span class="text-danger">*</span></label>
                    <input type="hidden" name="old_menu_document" value="{{ @$data->menu_document_upload }}">
                    <input type="file" name="menu_document" id="menu_document"
                        class="form-control @error('menu_document') is-invalid @enderror"
                        accept="application/pdf">
                    @error('menu_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    {{-- Optional: Show current file if available --}}
                    @if(!empty($data->menu_document))
                        <p class="mt-2">
                            Current File: 
                            <a href="{{ asset('uploads/mess_documents/' . $data->old_menu_document) }}" target="_blank">
                                View PDF
                            </a>
                        </p>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-success">Save Mess</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
