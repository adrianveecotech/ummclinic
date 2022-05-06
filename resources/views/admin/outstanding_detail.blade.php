@extends('layouts.admin')

@section('pageTitle', 'Admin :: Outstanding Detail')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{url('admin/payment-history')}}">Payment</a></li>
<li class="breadcrumb-item"><a href="{{url('admin/outstanding-monthly/'. $company->id)}}">{{$company->name}}</a></li>
<li class="breadcrumb-item active">{{$date}}</li>
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
        @if(session('success'))
            <div class="row mb-2">
                <div class="col-lg-12">
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                </div>
            </div>
        @elseif(session('fail'))
          <div class="row mb-2">
                <div class="col-lg-12">
                    <div class="alert alert-danger" role="alert">{{ session('fail') }}</div>
                </div>
            </div>
        @endif
        <p><strong>{{$company->name}}</strong></p>
        <p>{{$date}}</p>
        <div class="float-end">
        <form action="{{ url('admin/outstanding-monthly/settle')}}" method="POST">
          @csrf
          <input type="hidden" name="company_id" value="{{$company->id}}">
          <input type="hidden" name="date" value="{{$date}}">
        <button type="submit" class="btn btn-info">Mark as Settled</button>
        </form>
        </div>
        <br>    
        <br>    
        <table class="table table-bordered table-hover bg-white" id="payment_table">
                <thead>
                  <tr>
                    <th scope="col" style="width: 30%">Name</th>
                    <th scope="col" style="width: 15%">IC</th>
                    <th scope="col" style="width: 30%">Clinic</th>
                    <th scope="col" style="width: 7%">Amount</th>
                    <th scope="col" style="width: 15%">Date</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($payments as $index => $payment)
                    <tr>
                        <td>{{$payment->patient_name}}</td>
                        <td>{{$payment->ic}}</td>
                        <td>{{$payment->clinic_name}}</td>
                        <td>RM {{$payment->amount}}</td>
                        <td>{{$payment->created_at}}</td>
                    </tr>        
                    @empty
                    <td colspan="5">No records found.</td>
                  @endforelse
                </tbody>
            </table>    
            <div class="float-end mr-4">
              <strong>Total : RM{{$payments->getCollection()->sum('amount')}} </strong>
            </div>
        
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

</script>
@endsection