@extends('layouts.company')

@section('pageTitle', 'Company :: Outstanding Details')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item"><a href="/company/payment"></a> Payment</li>
<li class="breadcrumb-item active">All Payment</li>
@endsection

@section('styles')
<style>
  #search{
        width: 250px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
              <table class="table table-bordered table-hover bg-white" id="consultation_table">
                <thead>
                  <tr>
                    <th scope="col" style="width: 20%">Employee Name</th>
                    <th scope="col" style="width:10%" class="text-center">Employee ID</th>
                    <th scope="col" style="width:10%" class="text-center">Clinic Name</th>
                    <th scope="col" style="width:10%" class="text-center">Amount</th>
                    <th scope="col" style="width:10%" class="text-center">Status</th>
                    <th scope="col" style="width:10%" class="text-center">Time</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($payments as $index => $payment)
                    <tr>
                      <td>{{ $payment->employee_name }}</td>
                      <td>{{ $payment->employee_id }}</td>
                      <td>{{ $payment->clinic_name }}</td>
                      <td>RM {{ number_format((float)($payment->amount), 2) }}</td>
                      <td>{{ $payment->status }}</td>
                      <td>{{ $payment->created_at }}</td>
                    </tr>                 
                  @endforeach
                </tbody>
            </table>
            {{ $payments->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection