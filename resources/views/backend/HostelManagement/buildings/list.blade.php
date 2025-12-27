@extends('backend.layouts.master')

@section('content')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
@endpush

<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Buildings</h5>
                    <a href="{{ route('buildings.create') }}" class="btn btn-secondary btn-sm">Add New Building</a>
                </div>
                <div class="card-body">
                    <table id="buildingsTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Building Name</th>
                                <th>Hostel</th>
                                <th>Floors</th>
                                <th>Created At</th>
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

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables + Buttons -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>

<!-- Export Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        let buildings = @json($buildings);
        const csrfToken = '{{ csrf_token() }}';

        let building_base_url = "{{ url('buildings') }}/";
        let tableData = [];

        buildings.forEach(building => {
            let editUrl = `${building_base_url}${building.id}/edit`;
            let deleteUrl = `${building_base_url}${building.id}`;
            let imageTag = building.image
                ? `<img src="{{ asset('storage') }}/${building.image}" alt="Building Image" width="60" height="60" class="rounded">`
                : '-';

            tableData.push([
                building.id || "-",
                building.name || "-",
                building.hostel?.name || "-",
                building.number_of_floors ?? "-",
                building.created_at ? new Date(building.created_at).toLocaleDateString() : "-",
                `<div class="action-buttons d-flex gap-2">
                    <a href="${editUrl}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="${deleteUrl}" method="POST" onsubmit="return confirm('Are you sure?')">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>`
            ]);
        });

        const table = $('#buildingsTable').DataTable({
            data: tableData,
            columns: [null, null, null, null, null,
                { title: "Action", orderable: false }
            ],
            lengthMenu: [10, 25, 50],
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: ['csv', 'excel', 'print']
        });
    });
</script>
@endpush

@endsection
