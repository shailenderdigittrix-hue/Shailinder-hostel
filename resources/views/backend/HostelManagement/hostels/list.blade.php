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
                    <h5 class="card-title mb-0">Hostels</h5> 
                    <a href="{{ route('hostels.create') }}" class="btn btn-secondary btn-sm">Add New</a> 
                </div>
                <div class="card-body">
                    <table id="hostelsTable" class="table table-striped table-hover">
                        <thead>
                            <tr class="fw-bold">
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Gender</th>
                                <th>Capacity</th>
                                <th>Contact</th>
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

<!-- Page Content End here -->

@push('scripts')

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
        let hostels = @json($hostels);
        const csrfToken = '{{ csrf_token() }}';
        console.log('sfhdgjhjdfgsdgfjdsgfdsfdgfj ', hostels);
        
        let hostel_base_url = "{{ url('hostels') }}/";
        let tableData = [];
        console.log("Table exists? ", $('#hostelsTable').length); 
         
        hostels.forEach(hostel => {
            let editUrl = `${hostel_base_url}${hostel.id}/edit`;
            let deleteUrl = `${hostel_base_url}${hostel.id}`;

            tableData.push([
                hostel.id || "-",
                hostel.name || "-",
                hostel.code || "-",
                hostel.gender || "-",
                hostel.total_capacity || "-",
                hostel.contact || "-",
                hostel.is_active ? 'Yes' : 'No',
                `<div class="action-buttons d-flex ms-auto gap-2">
                    <a href="${editUrl}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="${deleteUrl}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
                `
            ]);

        });

        let table = $('#hostelsTable').DataTable({
            data: tableData,
            columns: [
                // { "width": "8px", "orderable": false }, 
                null, 
                null, 
                null, 
                null, 
                null, 
                null,
                null,
                // -------------- Action buttons column --------------
                { "width": "120px", "orderable": false }
            ],
            lengthMenu: [10, 25, 50, 100],
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: ['csv', 'excel','print']
        });

        

        $('#globalSearch').on('keyup change', function () {
            table.search(this.value).draw();
        });
    });
</script>
@endpush


<!-- Master Layout End -->
@endsection
