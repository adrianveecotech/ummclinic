@extends('layouts.clinic')

@section('pageTitle', 'Clinic :: Payment')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">Payment</li>
@endsection

@section('styles')
<style>
  .badge {
    font-size: 0.9rem;
  }
  #details_table th, td{
        border-bottom: 1px solid #f5f5f5;
    }
    #search{
        width: 250px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
      <div class="card-body">
          <div class="float-left mb-3">
              <form action="{{ url('clinic/payment') }}" method="GET">
                  <input type="text" name="search" id="search" class="form-control" placeholder="Search Company Name">
              </form>
          </div>
        <table class="table table-bordered table-hover bg-white" id="payment_table">
          <thead>
            <tr>
              <th scope="col" style="width:2%" class="text-center">#</th>
              <th scope="col" style="width: 20%">Company Name</th>
              <th scope="col" style="width: 10%">Outstanding Amount</th>
              <th scope="col" style="width: 10%">Total Unsettled Patient</th>
              <th scope="col" style="width:5%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($payments as $index => $payment)
              <tr>
                <th scope="row" class="text-center">{{ $index+1 }}</th>
                <td>{{ $payment->company_name }}</td>
                <td>@if($payment->total_amount == 0) - @else RM {{ number_format((float)($payment->total_amount), 2) }}@endif</td>
                <td>{{ $payment->count_unsettled }}</td>
                <td class="text-center">
                    <a class="btn btn-sm btn-info text-white" href="/clinic/payment/outstanding-details?company={{ $payment->company_id }}">View Details</a>  
                </td>
              </tr>
              @empty
                    <br><br> No records found.
            @endforelse
          </tbody>
      </table>
      {{ $payments->appends(request()->input())->links() }}
      </div>
    </div>
</div>
@endsection