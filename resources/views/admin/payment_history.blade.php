@extends('layouts.admin')

@section('pageTitle', 'Admin :: Payment History')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">Payment History</li>
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
            @if(!Request::get('group_by'))
            <div class="float-left">
                <form action="{{ url('admin/payment-history') }}" method="GET">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search IC or Clinic/Company Name">
                </form>
            </div>
            @elseif(Request::get('group_by') == 'company')
            <div class="col-4 float-left">
                <form action="{{ url('admin/payment-history') }}" method="GET" class="mb-3 row">
                    <input type="hidden" name="group_by" value="company">
                    <div class="row">
                        <label>Month:</label>
                        @if(Request::get('month'))
                        <input type="month" id="month" name="month" class="form-control" value="{{Request::get('month')}}">
                        @else
                        <input type="month" id="month" name="month" class="form-control" placeholder="month">
                        @endif
                    </div>
                    <div class="col mt-auto ">
                        <button type="submit" class="btn btn-success" id="btnFilter">Filter</a>
                    </div>
                </form>   
            </div>
            @endif

            @if(Request::get('group_by') == 'company')
            <div class="float-right ml-1">
            <button type="button" class="btn btn-success text-white" onclick="exportCsvCompany()"><i class="fa fa fa-file-excel-o" aria-hidden="true"></i></button>
            <a href="{{ url('admin/payment-history') }}" class="btn btn-info text-white">Group By All</a>
            </div>
            @else
            <div class="float-right ml-1">
                <form action="{{ url('admin/payment-history') }}" method="GET">
                    <input type="hidden" name="group_by" value="company">
                    <button type="submit" class="btn btn-info text-white">Group By Company</button>
                </form>
            </div>            
            @endif
          <div class="float-right">
            @if(!Request::get('group_by'))
            <a href="{{url('admin/payment-history')}}"><button type="button" class="btn btn-danger text-white mb-3"><i class="fa fa-close" aria-hidden="true"></i></button></a>
            <button type="button" class="btn btn-info text-white mb-3" data-toggle="modal" data-target="#filter_payment_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
            <button type="button" class="btn btn-success text-white mb-3" onclick="exportCsvAll()"><i class="fa fa fa-file-excel-o"  aria-hidden="true"></i></button>
              <div class="modal fade" id="filter_payment_modal" tabindex="-1" role="dialog" aria-labelledby="paymentFilterModal" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="paymentFilterModal">Filter</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                          <form action="{{ url('admin/payment-history') }}" method="GET">
                          <div class="modal-body">
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
                              <br>
                              <label for="company">Company</label>
                            <select name="company" class="custom-select">
                                <option selected></option>
                                @foreach($companies as $company)
                                <option value="{{$company->id}}">{{$company->name}}</option>
                                @endforeach
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
            @endif
          </div>
          @if(!Request::get('group_by'))
            <table class="table table-bordered table-hover bg-white" id="payment_table">
                <thead>
                  <tr>
                    <th scope="col" style="width:5%" class="text-center">#</th>
                    <th scope="col" style="width: 15%">IC</th>
                    <th scope="col" style="width: 15%">Clinic Name</th>
                    <th scope="col" style="width: 10%">Date</th>
                    <th scope="col" style="width: 7%">Amount</th>
                    <th scope="col" style="width:5%" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($payments as $index => $payment)
                    <tr>
                        <th scope="row" class="text-center">{{ $index+1 }}</th>
                        <td>{{ $payment->ic }}</td>
                        <td>{{ $payment->clinic_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y') }}</td>
                        <td>RM {{ number_format((float)($payment->amount), 2) }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_consultation_details_modal{{ $index }}">View</button>  
                        </td>
                    </tr>        
                    @empty
                        <br><br> No records found.
                  @endforelse
                </tbody>
            </table>
            {{ $payments->appends(request()->input())->links() }}
            @foreach($payments as $index => $payment)
            <div class="modal fade" id="view_consultation_details_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="viewconsultationdetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewconsultationdetailsModalLabel">Payment Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered" id="details_table">
                              <tbody>
                                  <tr>
                                    <th class="table-active w-25">Company Name</th>
                                    <td class="w-25" colspan="3">{{ $payment->company_name }}</td> 
                                  </tr>
                                <tr>
                                  <th class="table-active w-25">Patient Name</th>
                                  <td class="w-25">{{ $payment->employee_name }}</td>                                  
                                  <th class="table-active w-25">Patient IC</th>
                                  <td class="w-25">{{ $payment->ic }}</td>
                                </tr>                              
                                <tr>
                                    <th class="table-active w-25">Consultation Clinic</th>
                                    <td class="w-25">{{ $payment->clinic_name }}</td>
                                    <th class="table-active w-25">Consultation Date</th>
                                    <td class="w-25">{{ \Carbon\Carbon::parse($payment->created_at)->format('d-m-Y') }}</td>
                                </tr> 
                                <tr>
                                    <th class="table-active w-25">Consultation Price</th>
                                    <td class="w-25">RM {{ number_format((float)($payment->amount), 2) }}</td>    
                                    <th class="table-active w-25">Payment Status</th>
                                    <td class="w-25">
                                        @if($payment->status == 'settled') <span class="badge badge-success">Settled</span> @endif
                                        @if($payment->status == 'unsettled') <span class="badge badge-danger">Unsettled</span> @endif
                                    </td>
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
            @endforeach
            @elseif(Request::get('group_by') == 'company')
            <table class="table table-bordered table-hover bg-white" id="payment_table">
                <thead>
                  <tr>
                    <th scope="col" style="width:5%" class="text-center">#</th>
                    <th scope="col" style="width: 15%">Company</th>
                    <th scope="col" style="width: 10%">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($payments as $index => $payment)
                    <tr>
                        <th scope="row" class="text-center">{{ $index+1 }}</th>
                        <td>{{ $payment->company_name }}</td>
                        <td>RM {{number_format((float)$payment->total_amount, 2, '.', '')}}</td>
                    </tr>        
                    @empty
                        <br><br><br><br> No records found.
                  @endforelse
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var allPayments = <?php echo $allPayments ?>;
    function exportCsvAll(){
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Payment History \r\n";

        csvContent += "IC,Clinic Name, Date, Amount\r\n";
        allPayments.forEach(payment => {
            csvContent += payment.ic + "," +  payment.clinic_name + "," + payment.created_at + "," + payment.amount + "\r\n";
        });
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "payment_history.csv");
        document.body.appendChild(link);
        link.click()
    }

    function exportCsvCompany(){
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Company Outstanding Amount \r\n";
        url_string = window.location.href;
        var url = new URL(url_string);
        var c = url.searchParams.get("month");
        if(c)
            csvContent += "Month : " + c + "\r\n\r\n";
        else    
            csvContent += "\r\n";
        csvContent += "Company Name, Amount\r\n";
        allPayments.forEach(payment => {
            csvContent += payment.company_name + "," + "RM " + payment.total_amount + "\r\n";
        });
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "company_oustanding_amount.csv");
        document.body.appendChild(link);
        link.click()
    }
</script>
@endsection