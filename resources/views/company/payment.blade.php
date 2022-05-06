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
              <form action="{{ url('company/payment') }}" method="GET" class="mb-3 row">
              <div class="col-2">
                    <label for="address">Clinic</label>
                    <select class="form-select" name="clinic" id="clinic">
                      <option value="">Select a clinic</option>
                      @forelse($clinics as $clinic)
                        @if(Request::get('clinic') && Request::get('clinic') == $clinic->id )
                        <option value="{{$clinic->id}}" selected>{{$clinic->name}}</option>
                        @else
                        <option value="{{$clinic->id}}" >{{$clinic->name}}</option>
                        @endif
                      @empty
                      <td colspan="10">No records found.</td> 
                      @endforelse
                    </select>
              </div>
              <div class="col-2">
                <label for="start">Month:</label>
                @if(Request::get('month'))
                <input type="month" id="month" name="month" class="form-control" value="{{Request::get('month')}}">
                @else
                <input type="month" id="month" name="month" class="form-control">
                @endif
              </div>
              <div class="col-1 mt-auto mb-1">
                <button type="submit" class="btn btn-success" id="btnFilter">Filter</a>
              </div>
              <div class="col-1 ml-auto mt-auto mb-1">
                <button type="button" class="btn btn-info" id="btnExport" onclick="exportReport();">Export</a>
              </div>
              </form>
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
                  @forelse($outstandings_amount as $index => $outstanding_amount)
                    <tr>
                      <th scope="row" class="text-center">{{ $index+1 }}</th>
                      <td>{{ $outstanding_amount->clinic_name }}</td>
                      <td>RM {{ number_format((float)($outstanding_amount->total_outstanding), 2) }}</td>
                      <td class="text-center">
                          <a class="btn btn-sm btn-info" href="/company/outstanding-details?clinic={{ $outstanding_amount->clinic_id }}&month={{ Request::get('month') }}">View</a>  
                      </td>
                    </tr>                 
                    @empty
                      <td colspan="10">No records found.</td> 
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
  function exportReport(){
      var clinic = document.getElementById("clinic").value;
      var yearmonth = document.getElementById("month").value;

      jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
      });

      jQuery.ajax({
          url: 'payment/export',
          method: 'post',
          data: {
              "_token": "{{ csrf_token() }}",
              clinic: clinic,
              yearmonth: yearmonth,
          },
          success: function(data) {            
            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Payment Report " + "\r\n\r\n";

            if (yearmonth != '' ) {
                csvContent += "Month : " + yearmonth + "\r\n\r\n";
            }

            if (clinic != '' ) {
                csvContent += "Clinic : " + data[0][0].clinic_name + "\r\n\r\n";
            }

            data[0].forEach(function(rowArray) {
                let row = rowArray.employee_name + ',' + rowArray.employee_ic + ',' +  "RM" + rowArray.amount + ',' + rowArray.created_at;
                csvContent += row + "\r\n";
            });
      
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "report.csv");
            document.body.appendChild(link);
            link.click()
              },
              error: function(data) {
                  console.log(data.responseJSON.message);
              }
          });
  }
</script>

@endsection