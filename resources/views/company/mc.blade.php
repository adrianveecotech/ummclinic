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
                    <th scope="col">MC Start Date</th>
                    <th scope="col">MC End Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($consultations as $consultation)
                    <tr>
                      <td>{{$consultation->employee_name}}</td>
                      <td>{{$consultation->employee_ic}}</td>
                      <td>{{$consultation->clinic_name}}</td>
                      <td>{{$consultation->mc_startdate}}</td>
                      <td>{{$consultation->mc_enddate}}</td>
                    </tr>                 
                  @endforeach
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