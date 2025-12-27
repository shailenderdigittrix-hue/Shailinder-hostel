@extends('backend.layouts.master')
@section('content')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endpush
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Room Allocations</h5>
                </div>
                
                 {{-- Filter Form --}}
                <form 
                    method="GET" 
                    action="{{ auth()->user()->hasRole('Hostel Warden') 
                        ? route('warden.alloted_rooms_list') 
                        : route('admin.alloted_rooms_list') }}" 
                    class="p-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-2">
                            <label>Allocated Date</label>
                            <input type="date" name="allocated_date" class="form-control" value="{{ $allocated_date }}">
                        </div>
                        <div class="col-md-2">
                            <label>Hostel</label>
                            <select class="form-control" name="device_serial_no">
                                <option value="">Select Option</option>
                                @foreach($hostels as $hostel)
                                <option value="{{ $hostel->device_serial_no }}" {{ $device_serial_no == $hostel->device_serial_no ? 'selected' : '' }}>
                                    {{ $hostel->name }}
                                </option>
                                @endforeach
                            </select>
                           
                        </div>
                    
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Apply Filters</button>
                        </div>

                


                    </div>
                </form>

                <div class="card-body">
                    <table id="roomAllocationsTable" class="table table-striped table-hover">
                        <thead>
                            <tr class="fw-bold">
                                <th>ID</th>
                                <th>Student Name</th>
                                <th>Enrollment No</th>
                                <th>Hostel</th>
                                <th>Building</th>
                                <th>Floor</th>
                                <th>Room Number</th>
                                <th>Allocated Date</th>
                                <th>Room Change</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        const allocations = @json($room_allocations);
        let tableData = [];
        console.log('Allocations Data:', allocations); // Debugging line
        allocations.forEach(allocation => {
            const student = allocation.student || {};
            const room = allocation.room || {};
            const studentName = `${student.first_name ?? ''} ${student.last_name ?? ''}`.trim();
    
            const roomChangeBtn = `
            <button type="button" class="btn btn-primary open-request-modal"
                data-studentid="${student.id}"
                data-studentname="${studentName}"
                data-hostelid="${allocation.hostel_id}"
                data-currentroomid="${room.id}"
                data-currentroomnumber="${room.room_number}"
                data-bs-toggle="modal"
                data-bs-target="#roomChangeRequestModal">
                Add-request
            </button>`;
            tableData.push([
                allocation.id ?? '-',
                studentName,
                student.enrollment_no ?? '-',
                allocation.hostel?.name,
                room.building?.name ?? '-',
                allocation.floor ?? '-',
                room.room_number ?? '-',
                allocation.allocated_at ?? '-',
                roomChangeBtn
            ]);

        });
    
        $('#roomAllocationsTable').DataTable({
            data: tableData,
            columns: [null, null, null, null, null, null, null, null, 
                { title: "Room Change", orderable: false, searchable: false },
            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print',
                // {
                // text: 'Import',
                // className: 'btn btn-success',
                // action: function() {
                //     $('#importModal').modal('show');
                // }
                // }
            ],
            lengthMenu: [10, 25, 50, 100],
            pageLength: 25
        });
    });

    function applyWebFunction() {
    // Example: reload table data from server or apply filters
    console.log("Apply button clicked");
    // You can add any logic here — for example:
    // table.ajax.reload(); 
    // or
    // applyCustomFilters();
}
    
    // Room Change Request Modal open
    $(document).on('click', '.open-request-modal', function () {
        const studentId = $(this).data('studentid');
        const studentName = $(this).data('studentname');
        const currentRoomId = $(this).data('currentroomid');
        const currentRoomNumber = $(this).data('currentroomnumber');
    
        $('#student_id').val(studentId);
        $('#current_room_id').val(currentRoomId);
        $('#selectedStudentInfo').text(studentName);
        $('#selectedRoomInfo').text(currentRoomNumber);
        $('#desired_room_id').val('');
        $('#reason').val('');
    });
</script>
@endpush

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('import.roomAllocations') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Students from File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="file" name="file" class="form-control" required accept=".csv,.xls,.xlsx">
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Import</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Room Change Request Modal -->
<div class="modal fade" id="roomChangeRequestModal" tabindex="-1" aria-labelledby="roomChangeRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="roomChangeRequestForm" action="{{ route('admin.roomChangeRequests.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="roomChangeRequestModalLabel">Submit Room Change Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><b>Student</b></label>
                            <div class="form-control-plaintext" id="selectedStudentInfo">--</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><b>Current Room</b></label>
                            <div class="form-control-plaintext" id="selectedRoomInfo">--</div>
                        </div>
                    </div>
                    <input type="hidden" id="student_id" name="student_id">
                    <input type="hidden" id="current_room_id" name="current_room_id">
                    <!-- <div class="mb-3">
                        <label for="desired_room_id" class="form-label fw-semibold">Desired Room</label>
                        <select class="form-select" id="desired_room_id" name="desired_room_id" required>
                            <option value="">Select Desired Room</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->room_number }} ({{ $room->room_type }})</option>
                            @endforeach
                        </select>
                    </div> -->
                    <div class="mb-3">
                        <label for="reason" class="form-label fw-semibold">Reason for Change</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


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
    // $(document).on('click', '.open-room-re-allocate-modal', function () {
    //     const studentId = $(this).data('studentid');
    //     const studentName = $(this).data('studentname');
    //     const currentRoomId = $(this).data('currentroomid');
    //     const currentRoomNumber = $(this).data('currentroomnumber');
    //     const hostelID = $(this).data('hostelid');
    //     $('#reallocate_student_id').val(studentId);
    //     $('#reallocate_current_room_id').val(currentRoomId);
    //     $('#reallocateStudentInfo').text(studentName);
    //     $('#reallocateRoomInfo').text(currentRoomNumber);
       
    //     getHostelBuildings(hostelID);
    //     $('#floor').html('<option value="">Select Floor</option>').prop('disabled', true);
    //     $('#reallocate_new_room_id').html('<option value="">Select Room</option>').prop('disabled', true);
    //     $('#reallocate_note').val('');
    // });
    
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