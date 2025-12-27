@extends('backend.layouts.master')
@section('content')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<!-- Buttons CSS for Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endpush

<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Room Change Requests</h5>
                    
                </div>
                <div class="card-body">
                    <table id="roomChangeRequestsTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Current Room</th>
                                <!-- <th>Desired Room</th> -->
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Submitted At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @forelse($requests as $request)
                                @php
                                    $studentName = $request->student->first_name.' '.$request->student->last_name
                                @endphp
                                <tr>
                                    <td>{{ $studentName ?? 'N/A' }}</td>
                                    <td>{{ $request->currentRoom->room_number ?? 'N/A' }}</td>
                                    <!-- <td>{{ $request->desiredRoom->room_number ?? 'N/A' }}</td> -->
                                    <td>{{ $request->reason }}</td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($request->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($request->status === 'pending')
                                        <select class="form-select action-select" 
                                                data-request-id="{{ $request->id }}" 
                                                data-status="{{ $request->status }}">
                                            <option value="">Select Action</option>

                                            
                                                <option value="approved">Approve</option>
                                                <option value="rejected">Reject</option>
                                        </select>
                                        @else
                                            <button type="button" class="btn btn-primary open-room-re-allocate-modal"
                                                data-studentid="{{ $request->student->id }}"
                                                data-studentname="{{ $studentName ?? 'Student' }}"
                                                data-hostelid="{{ $request->currentRoom->hostel_id }}"
                                                data-currentroomid="{{ $request->currentRoom->id }}"
                                                data-currentroomnumber="{{ $request->currentRoom->room_number }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#roomReAllocateModal">
                                                Re-Allocate
                                            </button>
                                        @endif
                                        
                                    </td>

                                </tr>
                            @empty
                                <tr><td colspan="7">No room change requests found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Buttons Extension -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

<!-- Export Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Export Buttons -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


<script>
    $(document).ready(function () {
        $('.action-select').on('change', function () {
            const $this = $(this);
            const newStatus = $this.val();
            const requestId = $this.data('request-id');
            const currentStatus = $this.data('status');
            const itemType = 'RoomChangeRequest';

            if (!newStatus || newStatus === currentStatus) return;

            if (!['approved', 'rejected'].includes(newStatus)) {
                alert('Invalid status selected.');
                return;
            }

            if (!confirm(`Are you sure you want to ${newStatus} this request?`)) {
                $this.val(currentStatus);
                return;
            }
            
            // Call reusable AJAX function
            updateRequestStatus(requestId, newStatus, currentStatus, $this, itemType);
        });
    });

    function updateRequestStatus(requestId, newStatus, currentStatus, $selectElement, itemType) {
        $.ajax({
            url: '{{ route("admin.updateStatus") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: requestId,
                status: newStatus, // ✅ Add missing comma here
                itemType: itemType
            },
            success: function (response) {
                if (response.success) {
                    alert('Status updated successfully.');
                    location.reload(); // or use DOM update
                } else {
                    alert('Failed to update status.');
                    $selectElement.val(currentStatus);
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
                $selectElement.val(currentStatus);
            }
        });
    }
</script>

@endpush


<!-- Re-Allocate Modal -->
<div class="modal fade" id="roomReAllocateModal" tabindex="-1" aria-labelledby="roomReAllocateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('room.reAllocations.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Re-Allocate Student to New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><b>Student</b></label>
                        <div class="form-control-plaintext" id="reallocateStudentInfo">--</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><b>Current Room</b></label>
                        <div class="form-control-plaintext" id="reallocateRoomInfo">--</div>
                    </div>
                </div>
                <input type="hidden" id="reallocate_student_id" name="student_id">
                <input type="hidden" id="reallocate_current_room_id" name="current_room_id">
                <div class="row mb-3">
                   <label><strong>Select Hostel</strong></label>
                     <div class="col-sm-10">
                          <select name="hostel_id" id="hostel_id" class="form-select" onchange="getHostelId(this.value)" required>
                            <option value="">Select Hostel</option>
                          </select>
                          </div>
                </div>
                <!-- Building -->
                <div class="row mb-3">
                    <label for="building_id" class="col-sm-2 col-form-label">Building</label>
                    <div class="col-sm-10">
                        <select name="building_id" id="building_id" class="form-select" required>
                            <option value="">Select Building</option>
                        </select>
                    </div>
                </div>
                <!-- Floor -->
                <div class="row mb-3">
                    <label for="floor" class="col-sm-2 col-form-label">Floor</label>
                    <div class="col-sm-10">
                        <select name="floor" id="floor" class="form-select" required>
                            <option value="">Select Floor</option>
                        </select>
                    </div>
                </div>
                <!-- Room -->
                <div class="row mb-3">
                    <label for="room_id" class="col-sm-2 col-form-label">Room</label>
                    <div class="col-sm-10">
                        <select name="room_id" id="room_id" class="form-select" required>
                            <option value="">Select Room</option>
                        </select>
                    </div>
                </div>
                <!-- Room Allocation End --- -->
                <div class="mb-3">
                    <label for="reallocate_note" class="form-label fw-semibold">Note (optional)</label>
                    <textarea class="form-control" id="reallocate_note" name="note" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Confirm Re-Allocation</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Re-Allocate Modal open & reset fields
    function getHostelId (hostelId) {
        getHostelBuildings(hostelId);
        console.log('Selected Hostel ID:', hostelId);
    }
    $(document).on('click', '.open-room-re-allocate-modal', function () {
        const studentId = $(this).data('studentid');
        const studentName = $(this).data('studentname');
        const currentRoomId = $(this).data('currentroomid');
        const currentRoomNumber = $(this).data('currentroomnumber');
        const hostelID = $(this).data('hostelid');
        $('#reallocate_student_id').val(studentId);
        $('#reallocate_current_room_id').val(currentRoomId);
        $('#reallocateStudentInfo').text(studentName);
        $('#reallocateRoomInfo').text(currentRoomNumber);
       
        getHostelBuildings(hostelID);
        $('#floor').html('<option value="">Select Floor</option>').prop('disabled', true);
        $('#reallocate_new_room_id').html('<option value="">Select Room</option>').prop('disabled', true);
        $('#reallocate_note').val('');
    });

    function getHostel() {
        let hostelData = @json($hostelsData);
        let options = '<option value="">Select Hostel</option>';
        hostelData.forEach(hostel => {
            options += `<option value="${hostel.id}">${hostel.name}</option>`;
        });
        $('#hostel_id').html(options);
    }
    getHostel();
    
    // When hostel is selected
    function getHostelBuildings(hostelId){
        // Reset and disable other fields
        $('#building_id').html('<option value="">Loading...</option>').prop('disabled', true);
        $('#floor').html('<option value="">Select Floor</option>').prop('disabled', true);
        $('#room_id').html('<option value="">Select Room</option>').prop('disabled', true);

        $.get(`/hostels/${hostelId}/buildings`, function (data) {
            let options = '<option value="">Select Building</option>';
            data.forEach(building => {
                options += `<option value="${building.id}" data-floors="${building.number_of_floors}">${building.name}</option>`;
            });
            $('#building_id').html(options).prop('disabled', false);
        });
    }

    
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
</script>
@endpush

@endsection

