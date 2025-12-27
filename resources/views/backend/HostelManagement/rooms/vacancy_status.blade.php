@extends('backend.layouts.master')

@section('content')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Buttons CSS for Bootstrap 5 -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css"> -->
@endpush
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0 rounded">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Room Vacancy Status</h5>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('student-leaves.index') }}" class="btn btn-secondary btn-sm mb-0">
                            List
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="p-3">
                        <!-- Hostel Tabs -->
                        <ul class="nav nav-tabs mb-3" id="hostelTabs" role="tablist">
                            @foreach($hostels as $index => $hostel)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if($index === 0) active @endif"
                                        id="tab-{{ $hostel->id }}"
                                        data-hostel-id="{{ $hostel->id }}"
                                        type="button"
                                        role="tab"
                                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                        {{ $hostel->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content mt-3" id="hostelTabContent">
                            <div id="hostel-room-table-container">
                                <p>Select a hostel to view room data.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
    <!-- jQuery (required if not already included) -->
    <script>
    $(document).ready(function () {

        function loadHostelRooms(hostelId) {
            // PHP sets the base URL based on role, and we pass it as a string into JS
            let baseUrl = @json(auth()->user()->hasRole('Hostel Warden') 
                                ? route('warden.hostel.rooms', ['id' => 'HOSTEL_ID_PLACEHOLDER']) 
                                : route('hostel.rooms', ['id' => 'HOSTEL_ID_PLACEHOLDER']));

            // Replace placeholder with actual hostel ID
            let url = baseUrl.replace('HOSTEL_ID_PLACEHOLDER', hostelId);

            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function () {
                    $('#hostel-room-table-container').html('<p>Loading...</p>');
                },
                success: function (response) {
                    console.log('Table response ..........................', response);
                    $('#hostel-room-table-container').html(response.html);
                },
                error: function () {
                    $('#hostel-room-table-container').html('<p class="text-danger">Failed to load data.</p>');
                }
            });
        }

        // --------------- Handle tab click ---------------
        $('#hostelTabs').on('click', 'button', function () {
            $('#hostelTabs .nav-link').removeClass('active');
            $(this).addClass('active');

            const hostelId = $(this).data('hostel-id');
            loadHostelRooms(hostelId);
        });

        // --------------  Auto-load first tab's data ---------------
        $('#hostelTabs button.active').trigger('click');
    });
</script>

@endpush

@endsection
