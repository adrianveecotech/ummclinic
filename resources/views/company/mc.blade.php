@extends('layouts.company')

@section('pageTitle', 'Company :: MC')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">MC</li>
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
              <form action="{{ url('company/mc') }}" method="GET" class="mb-3 row">
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
              </form>
              <table class="table table-bordered table-hover bg-white" id="consultation_table">
                <thead>
                  <tr>
                    <th scope="col">Employee Name</th>
                    <th scope="col">Employee IC</th>
                    <th scope="col">Clinic Name</th>
                    <th scope="col">Total MC Taken</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($consultations as $index => $consultation)
                    <tr>
                      <td>{{$consultation->employee_name}}</td>
                      <td>{{$consultation->employee_ic}}</td>
                      <td>{{$consultation->clinic_name}}</td>
                      <td>{{$consultation->total_mc}}</td>
                      <td class="text-center">
                      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_mc_modal{{ $index }}">View</button>  
                    </td>
                    </tr>                 
                  @endforeach
                </tbody>
            </table>
            {{ $consultations->appends(request()->input())->links() }}
            @foreach($consultations as $index => $consultation)
        <div class="modal fade" id="view_mc_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <strong>MC Details</strong>
                    </div>
                    <div class="modal-body">
                    <table class="table table-bordered table-hover bg-white" id="consultation_table">
                    <thead>
                      <tr>
                        <th scope="col">MC Duration</th>
                        <th scope="col">Clinic Name</th>
                        <th scope="col">Diagnosis</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($consultation_details as $consultation_detail)
                      @if($consultation_detail->ic == $consultation->ic)
                        <tr>
                          <td>{{$consultation_detail->mc_startdate}} - {{$consultation_detail->mc_enddate}}</td>
                          <td>{{$consultation_detail->clinic_name}}</td>
                          <td>{{$consultation_detail->diagnosis}}</td>
                        </tr>     
                      @endif            
                      @endforeach
                    </tbody>
                    </table>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
          </div>   
        @endforeach
        </div>
      </div>
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