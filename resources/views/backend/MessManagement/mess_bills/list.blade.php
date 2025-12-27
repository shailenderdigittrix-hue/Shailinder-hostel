@extends('backend.layouts.master')
@section('content')

@push('styles')

@endpush
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Mess Billing Records</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Month</th>
                                <th>Days</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Mark Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bills as $bill)
                            <tr>
                                <td>{{ $bill->student->first_name }} {{ $bill->student->last_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($bill->month)->format('F Y') }}</td>
                                <td>{{ $bill->days }}</td>
                                <td>₹{{ number_format($bill->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $bill->status == 'paid' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($bill->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($bill->status == 'unpaid')
                                        <form action="{{ route('mess.bills.markPaid', $bill->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button class="btn btn-sm btn-success">Mark as Paid</button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled>Paid</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $bills->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
