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
                    <h5 class="card-title mb-0">Rooms</h5>
                    <a href="{{ route('rooms.create') }}" class="btn btn-secondary btn-sm">Add New Room</a>
                </div>
                <div class="card-body">
                    <table id="roomsTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Room ID</th>
                                <th>Room Number</th>
                                <th>Hostel</th>
                                <th>Capacity</th>
                                <th>Room Type</th>
                                <th>Active</th>
                                <th>Action</th>
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
        let rooms = @json($rooms);
        const csrfToken = '{{ csrf_token() }}';

        let room_base_url = "{{ url('rooms') }}/";
        let tableData = [];

        rooms.forEach(room => {
            let editUrl = `${room_base_url}${room.id}/edit`;
            let deleteUrl = `${room_base_url}${room.id}`;

            tableData.push([
                room.id || "-",
                room.room_number || "-",
                room.hostel?.name || "-",  // Assuming 'hostel' relationship is loaded
                room.capacity || "-",
                room.room_type || "-",
                room.is_active ? 'Yes' : 'No',
                `<div class="action-buttons d-flex ms-auto gap-2">
                    <a href="${editUrl}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="${deleteUrl}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>`
            ]);
        });

        const table = $('#roomsTable').DataTable({
            data: tableData,
            columns: [
                { title: "Room ID" },
                { title: "Room Number" },
                { title: "Hostel" },
                { title: "Capacity" },
                { title: "Room Type" },
                { title: "Active" },
                { title: "Action", width: "120px", orderable: false }
            ],
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: ['csv', 'excel', 'print']

            // buttons: [
            //     'copy', 'csv', 'excel', 'pdf', 'print',
            //     {
            //         text: 'Import',
            //         className: 'btn btn-success',
            //         action: function () {
            //             $('#importModal').modal('show');
            //         }
            //     }
            // ]
        });

        $('#globalSearch').on('keyup change', function () {
            table.search(this.value).draw();
        });
    });
</script>
@endpush

@endsection

