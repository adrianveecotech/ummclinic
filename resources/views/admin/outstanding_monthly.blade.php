@extends('layouts.admin')

@section('pageTitle', 'Admin :: Outstanding Detail')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{url('admin/payment-history?group_by=company')}}">Payment</a></li>
<li class="breadcrumb-item active">Outstanding Detail</li>
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
        <br>    
        <table class="table table-bordered table-hover bg-white" id="payment_table">
                <thead>
                  <tr>
                    <th scope="col" style="width:20%">Month</th>
                    <th scope="col" style="width: 10%">Amount</th>
                    <th scope="col" style="width: 33%">File</th>
                    <th scope="col" style="width:15%" class="text-center">Action</th>    
                  </tr>
                </thead>
                <tbody>
                  @forelse($payments as $index => $payment)
                    <tr>
                        <td>{{ $payment->date }}</td>
                        <td>RM {{number_format((float)$payment->total_amount, 2, '.', '')}}</td>
                        <td>
                          <a href="{{url('files/invoice/'. $payment->file)}}">{{$payment->file ? $payment->file : '-'}}</a>
                        </td>
                        <td class="text-center">
                            <a class="btn btn-sm btn-info" href="/admin/outstanding-monthly/{{$payment->company_id}}/{{$payment->date}}">View</a>  
                        <a href="#" class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_attach_invoice_modal{{ $index }}">Attach Invoice</a>
                        </td>
                    </tr>        
                    @empty
                        <td colspan="4">No records found.</td>
                  @endforelse
                </tbody>
            </table>    
        
        </div>
    </div>
    @foreach($payments as $index => $payment)
    <div class="modal fade" id="view_attach_invoice_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="font-size:17px !important;">
                            <strong>Attach Invoice</strong>
                        </div>
                        <form action="{{ url('admin/outstanding-monthly/upload-invoice')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                              <div class="form-group">
                                  <strong><label for="amount">Invoice</label></strong>
                                  <div class="input-group mb-3">
                                  <input type="file" class="form-control" id="invoice" name="invoice" required>
                                  <input type="hidden" name="company_id" value="{{$company->id}}">
                                  <input type="hidden" name="date" value="{{$payment->date}}">
                              </div>
                              </div>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>  
    @endforeach
</div>
@endsection

@section('scripts')
<script type="text/javascript">

</script>
@endsection