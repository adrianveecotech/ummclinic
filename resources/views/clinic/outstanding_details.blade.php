@extends('layouts.clinic')

@section('pageTitle', 'Clinic :: Outstanding Details')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item"><a href="/clinic/payment">Payment</a></li>
<li class="breadcrumb-item active">Outstanding Details</li>
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
          <div class="float-left">
              <form action="{{ url('clinic/payment/outstanding-details') }}" method="GET">
                  @if(Request::get('company'))
                    <input type="hidden" name="company" value="{{ Request::get('company') }}">
                  @endif
                  <input type="text" name="search" id="search" class="form-control" placeholder="Search IC or Company Name">
              </form>
          </div>
        <div class="float-right">
          <!-- <button type="button" class="btn btn-info text-white mb-3" data-toggle="modal" data-target="#billing_modal">Multiple Billing</button> -->
          <button type="button" class="btn btn-info text-white mb-3" data-toggle="modal" data-target="#filter_payment_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
        </div>
        <table class="table table-bordered table-hover bg-white" id="payment_table">
          <thead>
            <tr>
              <td class="text-center" colspan="7"><strong>{{ $company_name }}</strong></td>
            </tr>
            <tr @if(!$outstanding_amount[0]->total_amount) class="d-none" @endif>
              <td class="text-center" colspan="7"><strong>Total Outstanding: RM{{ number_format((float)($outstanding_amount[0]->total_amount), 2) }}</strong></td>
            </tr>
            <tr>
              <th scope="col" style="width:5%" class="text-center">#</th>
              <th scope="col">Patient Name</th>
              <th scope="col" style="width: 15%">IC</th>
              <th scope="col" style="width: 10%">Date</th>
              <th scope="col" style="width: 10%">Amount</th>
              <!-- <th scope="col" style="width:10%" class="text-center">Status</th> -->
              <th scope="col" style="width:15%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($payments as $index => $payment)
              <tr>
                  <th scope="row" class="text-center">{{ $index+1 }}</th>
                  <td>{{ $payment->patient_name }}</td>
                  <td>{{ $payment->ic }}</td>
                  <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y') }}</td>
                  <td>RM {{ number_format((float)($payment->amount), 2) }}</td>
                  <!-- <td class="text-center">
                    @if($payment->status == 'settled') <span class="badge badge-success">Settled</span> @endif
                    @if($payment->status == 'unsettled') <span class="badge badge-danger">Unsettled</span> @endif
                  </td> -->
                  <td class="text-center">
                      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_consultation_details_modal{{ $index }}">View</button>  
                      <!-- <button class="btn btn-sm btn-success @if($payment->status == 'settled') d-none @endif" data-toggle="modal" data-target="#billing_modal{{ $index }}">Billing</button>   -->
                      <!-- @if($payment->status == 'settled') <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#reference_modal{{ $index }}">Reference</button>@endif -->
                  </td>
              </tr>   
            @endforeach     
          </tbody>
      </table>
      {{ $payments->appends(request()->input())->links() }}
      @foreach($payments as $index => $payment)
      <div class="modal fade" id="view_consultation_details_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="viewconsultationdetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewconsultationdetailsModalLabel">Consultation Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="details_table">
                      <tbody>
                        <tr>
                          <th class="table-active w-25">Patient Name</th>
                          <td class="w-25">{{ $payment->patient_name }}</td>
                          <th class="table-active w-25">Consultation Date</th>
                          <td class="w-25">{{ \Carbon\Carbon::parse($payment->payment_created_at)->format('d-m-Y') }}</td>
                        </tr>                              
                        <tr>
                          <th class="table-active w-25">Consultation Doctor</th>
                          <td class="w-25">{{ $payment->doctor_name }}</td>
                          <th class="table-active w-25">Consultation Price</th>
                          <td class="w-25">RM {{ number_format((float)($payment->price), 2) }}</td>
                        </tr>                              
                      </tbody>
                    </table>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  </div>
            </div>
        </div>
    </div> 
    <!-- <div class="modal fade" id="billing_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header" style="font-size:17px !important;">
                <strong>Billing</strong>
              </div>
              <form action="{{ url('clinic/payment/billing', $payment->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="form-group">
                    <strong><label for="amount">Amount</label></strong>
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">RM</span>
                      </div>
                      <input type="number" class="form-control" name="amount" min="1" step="0.01" pattern="\$?(\d)+(\.\d\d)?" value="{{ number_format((float)($payment->amount), 2) }}">
                  </div>
                  </div>
                    <div class="form-group">
                      <strong><label for="reference">Billing Reference: </label></strong>
                      <textarea class="form-control" name="reference" rows="3" placeholder="Payment Details/Reference No.">{{ $payment->reference }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-success">Submit</button>
                </div>
              </form>
          </div>
      </div>
    </div>       -->
    <div class="modal fade" id="reference_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header" style="font-size:17px !important;">
                <strong>Payment Reference</strong>
              </div>
                <div class="modal-body">
                  <table class="table table-bordered" id="details_table">
                    <tbody>
                      <tr>
                        <th class="table-active w-25">Patient Name</th>
                        <td class="w-25">{{ $payment->patient_name }}</td>
                        <th class="table-active w-25">Status</th>
                        <td class="w-25">
                          @if($payment->status == 'settled') <span class="badge badge-success">Settled</span> @endif
                        </td>
                      </tr>                        
                      <tr>
                        <th class="table-active w-25">Payment Date</th>
                        <td class="w-25">{{ \Carbon\Carbon::parse($payment->settled_date)->format('d-m-Y') }}</td>
                        <th class="table-active w-25">Amount</th>
                        <td class="w-25">
                          RM {{ number_format((float)($payment->amount), 2) }}
                        </td>
                      </tr>                              
                      <tr>                             
                      <tr>
                        <td id="dash_description_id" colspan="4">
                          {{ nl2br($payment->reference) }}
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
          </div>
      </div>
    </div> 
    <div class="modal fade" id="billing_modal" tabindex="-1" role="dialog" aria-labelledby="billingModal" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="billingModal">Billing</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>          
              <form action="{{ url('clinic/multiple_billing', Request::get('company')) }}" method="POST">
                @csrf
                <div class="modal-body">
                  <div class="form-group">
                    <strong><label for="amount">Amount</label></strong>
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">RM</span>
                      </div>
                      <input type="number" class="form-control" name="amount" min="1" step="0.01" pattern="\$?(\d)+(\.\d\d)?" value="{{ number_format((float)($outstanding_amount[0]->total_amount), 2) }}">
                    </div>
                  </div>
                  <div class="form-group">
                    <strong><label for="reference">Billing Reference: </label></strong>
                    <textarea class="form-control" name="reference" rows="3" placeholder="Payment Details/Reference No.">{{ $payment->reference }}</textarea>
                  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
          </div>
      </div>
  </div>  
    <div class="modal fade" id="filter_payment_modal" tabindex="-1" role="dialog" aria-labelledby="paymentFilterModal" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="paymentFilterModal">Filter</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <form action="{{ url('clinic/payment/outstanding-details') }}" method="GET">
                  <div class="modal-body">
                    @if(Request::get('company'))
                      <input type="hidden" name="company" value="{{ Request::get('company') }}">
                    @endif
                      <div class="row">
                          <div class="col">
                              <label for="start_date">Start Date</label>
                              <input type="date" class="form-control" name="start_date">
                          </div>                                
                          <div class="col">
                              <label for="end_date">End Date</label>
                              <input type="date" class="form-control" name="end_date">
                          </div>
                      </div><br>
                      <label for="status">Status</label>
                      <select name="status" class="custom-select">
                        <option selected></option>
                          <option value="settled">Settled</option>
                          <option value="unsettled">Unsettled</option>
                      </select>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </form>
              </div>
          </div>
      </div>
  </div>     
      @endforeach
      </div>
    </div>
</div>
@endsection