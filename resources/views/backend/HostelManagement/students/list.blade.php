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
                    <h5 class="card-title mb-0">Students</h5>
                    <!-- @if($student_count)
                    <a href="javascript:void(0);" 
                        onclick="alert('Student limit exceeded! Maximum allowed is 3500. Please contact digittrix support for further use');" 
                        class="btn btn-secondary btn-sm">
                        Add New Student
                        </a>   
                    @else
                      <a href="{{ route('students.create') }}" class="btn btn-secondary btn-sm">Add New Student</a>
                    @endif -->

                    <!-- <form action="#" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required>
                        <button class="btn btn-danger" type="submit">Import</button>
                    </form> -->

                </div>
                <div class="card-body">
                    <table id="studentsTable" class="table table-striped table-hover">
                        <thead>
                            <tr class="fw-bold">
                                <th>Enrollment No</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Phone</th>
                                <th>Admission Date</th>
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
        // let students = @json($students);
        let students = @json($students);

        // console.log('bgdjfgjsgfdsfgdsgfgjdsg', students);
        const csrfToken = '{{ csrf_token() }}';

        let student_base_url = "{{ url('students') }}/";
        
        let tableData = [];

        students.forEach(student => {
            let fullName = (student.first_name || student.last_name) 
                            ? `${student.first_name} ${student.last_name}`.trim() 
                            : student.user.name;

            let editUrl = `${student_base_url}${student.id}/edit`;
            let deleteUrl = `${student_base_url}${student.id}`;
            let showUrl = `${student_base_url}${student.id}`;
            console.log("tableData", tableData);
            
            tableData.push([
                student.enrollment_no || "-",
                fullName,
                student.gender || "-",
                student.email || "-",
                student.course?.name || "-",
                student.year || "-",
                student.phone || "-",
                student.admission_date || "-",
                `<div class="action-buttons d-flex ms-auto gap-2">
                    <a href="${showUrl}">View</a>
                    <a href="${editUrl}" class="btn btn-primary btn-sm">Edit</a>
                    <form action="${deleteUrl}" method="POST" style="display:inline;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>`
            ]);
        });

        const table = $('#studentsTable').DataTable({
            data: tableData,
            columns: [
                null,null, null, null, null, null, null, null,
                // -------------- Action buttons column --------------
                { "width": "120px", "orderable": false }
            ],
            lengthMenu: [10, 25, 50, 100],
            pageLength: 25,
            dom: 'Bfrtip',
            // buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            buttons: [
                'csv', 'excel', 'print',
                // {
                //     text: 'Import',
                //     className: 'btn btn-success',
                //     action: function () {
                //         $('#importModal').modal('show');
                //     }
                // }
            ]
        });

        // Optional global search (if you have a search input with id="globalSearch")
        $('#globalSearch').on('keyup change', function () {
            table.search(this.value).draw();
        });
    });
</script>
@endpush

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.importBulkStudents') }}" enctype="multipart/form-data" class="modal-content">
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


@endsection
