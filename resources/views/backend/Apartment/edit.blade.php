
@extends('backend.layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Edit Apartment</h4>
            <a href="{{ route('admin.couple-apartment.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.couple-apartment.update', $id) }}" method="POST" id="coupleApartmentForm">
                @csrf
                   @method('PUT')

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ $apartment->name }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Type -->
                <div class="mb-3">
                    <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                    <input type="text" name="type" class="form-control @error('type') is-invalid @enderror"
                        value="{{ @$apartment->type }}" required>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" required>{{ $apartment->description }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Apartment Number -->
                <div class="mb-3">
                    <label for="apartment_number" class="form-label">Apartment Number <span class="text-danger">*</span></label>
                    <input type="text" name="apartment_number" class="form-control @error('apartment_number') is-invalid @enderror"
                        value="{{ $apartment->apartment_number }}" required>
                    @error('apartment_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Floor Number -->
                <div class="mb-3">
                    <label for="floor_number" class="form-label">Floor Number <span class="text-danger">*</span></label>
                    <input type="number" name="floor_number" class="form-control @error('floor_number') is-invalid @enderror"
                        value="{{ $apartment->floor_number }}" required>
                    @error('floor_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Total Floors -->
                <div class="mb-3">
                    <label for="total_floors" class="form-label">Total Floors <span class="text-danger">*</span></label>
                    <input type="number" name="total_floors" class="form-control @error('total_floors') is-invalid @enderror"
                        value="{{ $apartment->total_floors }}" required>
                    @error('total_floors') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            
                <!-- Furnished Status -->
                <div class="mb-3">
                    <label for="furnished_status" class="form-label">Furnished Status <span class="text-danger">*</span></label>
                    <select name="furnished_status" class="form-select @error('furnished_status') is-invalid @enderror" required>
                        <option value="">Select Status</option>
                        <option value="Furnished" {{ $apartment->furnished_status == 'Furnished' ? 'selected' : '' }}>Furnished</option>
                        <option value="Semi-Furnished" {{ $apartment->furnished_status == 'Semi-Furnished' ? 'selected' : '' }}>Semi-Furnished</option>
                        <option value="Unfurnished" {{ $apartment->furnished_status == 'Unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                    </select>
                    @error('furnished_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Mess List -->
               <!--  <div class="mb-3">
                    <label for="mess_id" class="form-label">Mess List <span class="text-danger">*</span></label>
                    <select name="mess_id" class="form-select @error('mess_id') is-invalid @enderror" required>
                        <option value="">Select Mess</option>
                        @foreach ($messList as $mess)
                            <option value="{{ $mess->id }}" {{ $apartment->mess_id == $mess->id ? 'selected' : '' }}>{{ $mess->name }}</option>
                        @endforeach
                    </select>
                    @error('mess_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div> -->

                <!-- Add People -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Add People <span class="text-danger">*</span></label>
                        <button type="button" class="btn btn-sm btn-primary" id="addMemberBtn">+ Add Member</button>
                    </div>
                    <div id="memberList"></div>
                    <div class="text-danger small" id="memberError" style="display:none;">At least one member is required.</div>
                </div>

                <button type="submit" class="btn btn-success">Save Hostel</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addMemberBtn = document.getElementById('addMemberBtn');
    const memberList = document.getElementById('memberList');
    const memberError = document.getElementById('memberError');
    let memberIndex = 0;

    const memberDetails = @json($apartment->apartmentMember);
    console.log('memberDetails', memberDetails);

    // ✅ Load existing members if editing
    if (memberDetails && memberDetails.length > 0) {
        editCaseAppendMembers();
        memberIndex = memberDetails.length;
    } else {
        // If no members, start with one empty field
        addMember();
    }

    addMemberBtn.addEventListener('click', function() {
        addMember();
    });

    // ✅ Function to append members from DB
    function editCaseAppendMembers() {
        memberList.innerHTML = ''; // clear old members if any

        memberDetails.forEach((item, index) => {
            const fetchDiv = document.createElement('div');
            fetchDiv.classList.add('row', 'g-2', 'mb-2', 'align-items-end');

            fetchDiv.innerHTML = `
                <div class="col-md-4">
                    <label class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="members[${index}][name]" class="form-control" value="${item.name}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Relation <span class="text-danger">*</span></label>
                    <input type="text" name="members[${index}][relation]" class="form-control" value="${item.relation}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Age <span class="text-danger">*</span></label>
                    <input type="number" name="members[${index}][age]" class="form-control" min="1" value="${item.age}" required>
                </div>
                <div class="col-md-1 text-end">
                    ${index > 0 ? `<button type="button" class="btn btn-danger btn-sm remove-member">X</button>` : ''}
                </div>
            `;

            memberList.appendChild(fetchDiv);

            // Enable remove button
            fetchDiv.querySelector('.remove-member')?.addEventListener('click', () => {
                fetchDiv.remove();
            });
        });
    }

    // ✅ Function to add new blank member row
    function addMember() {
        const div = document.createElement('div');
        div.classList.add('row', 'g-2', 'mb-2', 'align-items-end');
        div.innerHTML = `
            <div class="col-md-4">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="members[${memberIndex}][name]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Relation <span class="text-danger">*</span></label>
                <input type="text" name="members[${memberIndex}][relation]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Age <span class="text-danger">*</span></label>
                <input type="number" name="members[${memberIndex}][age]" class="form-control" min="1" required>
            </div>
            <div class="col-md-1 text-end">
                ${memberIndex > 0 ? `<button type="button" class="btn btn-danger btn-sm remove-member">X</button>` : ''}
            </div>
        `;

        memberList.appendChild(div);
        memberIndex++;

        div.querySelector('.remove-member')?.addEventListener('click', () => {
            div.remove();
        });
    }

    // ✅ Validate before submit
    document.getElementById('coupleApartmentForm').addEventListener('submit', function(e) {
        if (memberList.children.length === 0) {
            e.preventDefault();
            memberError.style.display = 'block';
        } else {
            memberError.style.display = 'none';
        }
    });
});
</script>

@endsection
