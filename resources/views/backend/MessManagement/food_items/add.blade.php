@extends('backend.layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>{{ isset($data) ? 'Edit' : 'Add' }} Food Item</h4>
        </div>

        <div class="card-body">
            <form action="{{ isset($data) ? route('fooditems.update', $data->id) : route('fooditems.store') }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($data))
                    @method('PUT')
                @endif

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $data->name ?? '') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                        <option value="">Select Category</option>
                        <option value="veg" {{ old('category', @$data->category ?? '') == 'veg' ? 'selected' : '' }}>Veg</option>
                        <option value="non-veg" {{ old('category', @$data->category ?? '') == 'non-veg' ? 'selected' : '' }}>Non Veg</option>
                        <option value="vegan" {{ old('category', @$data->category ?? '') == 'vegan' ? 'selected' : '' }}>Vegan</option>
                    </select>
                    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', @$data->description ?? '') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Calories -->
                <div class="mb-3">
                    <label for="calories" class="form-label">Calories <span class="text-danger">*</span></label>
                    <input type="number" name="calories" class="form-control @error('calories') is-invalid @enderror"
                        value="{{ old('calories', @$data->calories ?? '') }}" required>
                    @error('calories') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror"
                        value="{{ old('price', @$data->price ?? '') }}" required>
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Current Image -->
                @if(isset($data) && $data->image)
                    <div class="mb-3">
                        <label class="form-label">Current Image:</label><br>
                        <img src="{{ asset('public/' . $data->image) }}" alt="{{ $data->name }}" width="150">
                    </div>
                @endif
                <input type='hidden' name="old_image" value="<?php echo @$data->image;  ?>">

                <!-- Upload New Image -->
                <div class="mb-3">
                    <label for="image" class="form-label">Food Image</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Status -->
                <div class="mb-3 form-check">
                    <input type="checkbox" name="status" class="form-check-input" id="status"
                        {{ old('status', @$data->status ?? 'Active') == 'Active' ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active</label>
                </div>

                <button type="submit" class="btn btn-success">{{ isset($data) ? 'Update' : 'Save' }} Food Item</button>
            </form>
        </div>
    </div>
</div>
@endsection
