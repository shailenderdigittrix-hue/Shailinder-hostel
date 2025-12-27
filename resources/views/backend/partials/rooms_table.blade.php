<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<table id="roomsTable" class="table table-bordered table-striped table-hover w-100">
    <thead>
        <tr>
            <th>Hostel</th>
            <th>Building</th>
            <th>Floor</th>
            <th>Room Number</th>
            <th>Capacity</th>
            <th>Occupied</th>
            <th>Vacant</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>



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
        // Parse rooms data from Laravel
        let rooms = @json($rooms);

        // Transform rooms data into an array for DataTables
        let tableData = rooms.map(room => {
            const hostelName   = room.hostel_name || '-';
            const buildingName = room.building_name || '-';
            const floor        = room.floor !== null ? `Floor ${room.floor}` : '-';
            const roomNumber   = room.room_number || '-';
            const capacity     = room.capacity || 0;
            const allocations  = room.active_allocations?.length || 0;
            const vacant       = capacity - allocations;

            let status = 'Vacant';
            if (vacant === 0) status = 'Full';
            else if (vacant < capacity) status = 'Partial';

            return [
                hostelName,
                buildingName,
                floor,
                roomNumber,
                capacity,
                allocations,
                vacant,
                status
            ];
        });

        // Initialize DataTable with data and columns
        const table = $('#roomsTable').DataTable({
            data: tableData,
            columns: [null, null, null, null, null, null, null, null],
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],

            // Optional: add colors to status column
            createdRow: function (row, data) {
                const status = data[7];
                const cell = $('td', row).eq(7);

                let badgeClass = '';

                if (status === 'Full') {
                    badgeClass = 'text-bg-danger';
                } else if (status === 'Partial') {
                    badgeClass = 'text-bg-warning';
                } else if (status === 'Vacant') {
                    badgeClass = 'text-bg-success';
                } else {
                    badgeClass = 'text-bg-secondary';
                }

                // Wrap the existing status text with a badge span
                cell.html(`<span class="badge ${badgeClass}">${status}</span>`);
            }


        });

        // Optional: global search input listener
        $('#globalSearch').on('keyup change', function () {
            table.search(this.value).draw();
        });
    });
</script>

