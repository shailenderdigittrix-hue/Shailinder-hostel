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
                    <h5 class="card-title mb-0">Couple Apartment</h5> 
                    <a href="{{ route('admin.couple-apartment.create') }}" class="btn btn-secondary btn-sm">Add New</a> 
                </div>
                <div class="card-body">
                    <table id="hostelsTable" class="table table-striped table-hover">
                        <thead>
                            <tr class="fw-bold">
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Floor Number</th>
                                <th>Total Floor</th>
                                <th>bedrooms</th>
                                <th>bathrooms</th>
                                <th>Number Of Persons</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $x = 1; ?>
                            @if($data)
                            @foreach(@$data as $row)
                                <tr>
                                   <td>{{ $x++ }}</td>
                                   <td>{{ $row->name }}</td>
                                   <td>{{ $row->type }}</td>    
                                   <td>{{ substr($row->description, 0, 150) }}</td>    
                                   <td>{{ $row->floor_number }}</td>    
                                   <td>{{ $row->total_floors }}</td>     
                                   <td>{{ $row->bedrooms }}</td>    
                                   <td>{{ $row->bathrooms }}</td>    
                                   <td>{{ $row->number_of_members }}</td>    
                                   <td>
                                       <a href="{{ route('admin.couple-apartment.edit', $row->id) }}" class="btn btn-primary">Edit</a>

                                  <a href="javascript:void(0);" 
                                        onclick="if(confirm('Are you sure you want to delete this apartment?')) { 
                                            document.getElementById('delete-form-{{ $row->id }}').submit(); 
                                        }" 
                                        class="btn btn-danger">
                                        Delete
                                        </a>

                                        <form id="delete-form-{{ $row->id }}" 
                                            action="{{ route('admin.couple-apartment.edit', $row->id) }}" 
                                            method="POST" 
                                            style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                        </form>

                                </td>    
                                </tr>
                            @endforeach
                            @endif
                        </tbody>
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
    @endpush

<!-- Master Layout End -->
@endsection
