@extends('layouts.company')

@section('pageTitle', 'Consultation Report')

@section('styles')
<style>
  .badge {
    font-size: 0.9rem;
  }
  #search{
        width: 250px !important;
    }
#amount_card{
    font-size: 16px !important;
    border: none;
    background-color: #e8e8e7;
}
.modal-header, .modal-title{
    font-size: 16px !important;
}
</style>
@endsection

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">Consultation Report</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
          <div class="float-left">
            <form action="{{ url('company/consultation') }}" method="GET">
              <input type="text" name="search" id="search" class="form-control" placeholder="Search Name, IC or Clinic Name">
            </form>
          </div>
          <div class="float-right mb-3">
            <button type="button" class="btn btn-info mr-1" id="btnExport" onclick="exportReport();">Export</a>
            <button type="button" class="btn btn-info text-white" data-toggle="modal" data-target="#filter_employee_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
            <div class="modal fade" id="filter_employee_modal" tabindex="-1" role="dialog" aria-labelledby="employeeFilterModal" aria-hidden="true">
              <div class="modal-dialog" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="employeeFilterModal">Filter</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <form action="{{ url('company/consultation') }}" method="GET">
                        <div class="modal-body">
                          <div class="row mb-3">
                            <div class="col">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" name="start_date">
                            </div>                                
                            <div class="col">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                          </div>
                          <label for="status">Status</label>
                          <select name="status" class="custom-select">
                            <option selected></option>
                            <option value="settled">Settled</option>
                            <option value="unsettled">Unsettled</option>
                            <option value="processing">Processing</option>
                          </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                      </form>
                  </div>
              </div>
            </div>
          </div>
          <table class="table table-bordered table-hover bg-white" id="consultation_table">
            <thead>
              <tr>
                <th scope="col" style="width:5%" class="text-center">#</th>
                <th scope="col">Employee Name</th>
                <th scope="col" style="width: 20%">IC</th>
                <th scope="col" style="width: 20%">Clinic Name</th>
                <th scope="col" style="width: 10%">Date</th>
                <th scope="col" style="width:10%" class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($consultations as $index => $consultation)
                <tr>
                  <th scope="row" class="text-center">{{ $index+1 }}</th>
                  <td>{{ $consultation->employee_name }}</td>
                  <td>{{ $consultation->ic }}</td>
                  <td>{{ $consultation->clinic_name }}</td>
                  <td>{{ \Carbon\Carbon::parse($consultation->created_at)->format('d-m-Y') }}</td>
                  <td class="text-center">
                      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_consultation_modal{{ $index }}">View</button>  
                  </td>
                </tr>                 
              @endforeach
            </tbody>
        </table>
        {{ $consultations->appends(request()->input())->links() }}
        @foreach($consultations as $index => $consultation)
        <div class="modal fade" id="view_consultation_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <strong>Consultation Details</strong>
                    </div>
                    <div class="modal-body">
                      <table class="table table-bordered" id="details_table">
                        <tbody>
                          <tr>
                            <th class="table-active w-25">Patient Name</th>
                            <td class="w-25">{{ $consultation->employee_name }}</td>                            
                            <th class="table-active w-25">Patient IC</th>
                            <td class="w-25">{{ $consultation->ic }}</td>
                          </tr>    
                          <tr>
                            <th class="table-active w-25">Clinic Name</th>
                            <td class="w-25">{{ $consultation->clinic_name }}</td>
                            <th class="table-active w-25">Consultation Doctor</th>
                            <td class="w-25">{{ $consultation->doctor_name }}</td>
                          </tr>
                          <tr>
                            <th class="table-active w-25">Consultation Date</th>
                            <td class="w-25">{{ \Carbon\Carbon::parse($consultation->created_at)->format('d-m-Y') }}</td>
                            <th class="table-active w-25">Payment Status</th>
                            <td class="w-25">
                                @if($consultation->payment_status == 'settled') <span class="badge badge-success">Settled</span>@endif
                                @if($consultation->payment_status == 'unsettled') <span class="badge badge-danger">Unsettled</span>@endif
                                @if($consultation->payment_status == 'processing') <span class="badge badge-info">Processing</span>@endif
                            </td>
                          </tr>   
                          <tr>
                            <th class="table-active w-25">MC</th>
                            <td class="w-25"> @if($consultation->mc_startdate == $consultation->mc_enddate)
                                  {{ $consultation->mc_startdate }}
                                  @else
                                  From {{ $consultation->mc_startdate}} to {{$consultation->mc_enddate}}
                                  @endif</td>
                              <th class="table-active w-25">Medications</th>
                            <td class="w-25">{{ $consultation->medications_name }}</td>
                          </tr>                      
                          <tr>
                            <td id="dash_description_id" colspan="4">
                              {!! nl2br($consultation->description) !!}
                          </tr>      
                        </tbody>
                      </table>
                      <div class="card" id="amount_card">
                        <div class="card-body">
                            <strong>Total Amount: RM </strong><strong>{{ number_format((float)($consultation->price), 2) }}</strong>
                        </div>
                    </div>  
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
@endsection
@section('scripts')
<script type="text/javascript">
    var consultations = <?php echo $allConsultations ?>;

    function exportReport(){
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Consultation History \r\n";

        csvContent += "Patient Name, IC, Clinic Name, Created At, Doctor, Payment Status, MC, Medications, Diagnosis, Price \r\n";
        consultations.forEach(consultation => {
          if(consultation.mc_startdate == null)
          csvContent += consultation.employee_name + "," +  consultation.ic + "," + consultation.clinic_name + "," + consultation.created_at + "," + consultation.doctor_name + "," + consultation.payment_status + "," + '-' + "," + consultation.medications_name.replace(",", ";") + "," + consultation.description.replace(",", ";") + "," + consultation.price + "\r\n";
          else
            csvContent += consultation.employee_name + "," +  consultation.ic + "," + consultation.clinic_name + "," + consultation.created_at + "," + consultation.doctor_name + "," + consultation.payment_status + "," + consultation.mc_startdate + '-' + consultation.mc_enddate + "," + consultation.medications_name.replace(",", ";") + "," + consultation.description.replace(",", ";") + "," + consultation.price + "\r\n";
        });
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "consultation_history.csv");
        document.body.appendChild(link);
        link.click()
      }
</script>
@endsection