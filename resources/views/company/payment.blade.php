@extends('layouts.company')

@section('pageTitle', 'Company :: Payment')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">Payment</li>
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
            <div class="float-left mb-3">
                <form action="{{ url('company/payment') }}" method="GET">
                  <input type="text" name="search" id="search" class="form-control" placeholder="Search Company Name">
                </form>
              </div>
              <table class="table table-bordered table-hover bg-white" id="consultation_table">
                <thead>
                  <tr>
                    <th scope="col" style="width:5%" class="text-center">#</th>
                    <th scope="col">Clinic Name</th>
                    <th scope="col" style="width: 20%">Total Outstanding Amount</th>
                    <th scope="col" style="width:10%" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($outstandings_amount as $index => $outstanding_amount)
                    <tr>
                      <th scope="row" class="text-center">{{ $index+1 }}</th>
                      <td>{{ $outstanding_amount->clinic_name }}</td>
                      <td>RM {{ number_format((float)($outstanding_amount->total_outstanding), 2) }}</td>
                      <td class="text-center">
                          <a class="btn btn-sm btn-info" href="/company/outstanding-details?clinic={{ $outstanding_amount->clinic_id }}">View</a>  
                      </td>
                    </tr>                 
                  @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection