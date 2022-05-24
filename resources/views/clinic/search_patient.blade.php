@extends('layouts.clinic')

@section('pageTitle', 'Clinic :: Search Patient')

@section('styles')
<style>
#search_bar {
    display: block;
    border: 1px solid #aaa;
    font-size: 16px;
    padding: 10px 10px;
    width: 100%;
    background: #fff;
    padding-left: 40px;
    border-radius: 20px;
    outline: none !important;
    box-sizing: border-box;
} 
#details_table th, td{
  border-bottom: 1px solid #f5f5f5;
}
b{
  color: red;
}
.card{
    border-radius: 10px;
}
.card-title{
    font-size: 17px !important;
    font-weight: 500;
}
</style>
@endsection

@section('sub_header')
  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
  <li class="breadcrumb-item active">Patient</li>
@endsection

@section('content')
<div class="container">
  @if(Request::get('query') && $employees == null)
  <div class="alert alert-danger">
      Invalid Patient. No records found.
  </div>
  @endif  
  @if(Request::get('query') && $employees && $status == 'inactive')
  <div class="alert alert-danger">
      Inactive Patient.
  </div>
  @endif
  <form id="search" action="{{ url('clinic/search') }}" method="GET">
    <div class="search_employee text-center">
      <input type="text" id="search_bar" name="query" class="h-100 align-items-center" placeholder="Employee ID or IC Number">
      <button type="submit" class="btn btn-success mt-3 d-none">Submit</button>
  </div>
  </form>
  @if($employees != null)
    <div class="@if($employees == null || $status != 'active') d-none @endif">
      <div class="card mt-3">
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-auto">
              <a class="btn btn-outline-info" href="{{ url('/clinic/consult', $employees->ic) }}">Consult Now</a>
            </div>
            <div class="col-auto">
              <a class="btn btn-outline-info" href="{{ url('clinic/dependent/new') . '?employee='.$employees->id }}">Register Dependent</a>
            </div>
          </div>
          <div class="card">
            <div class="card-body">
            <div class="row mb-2">
              @if($daily_limit_exceeded == 'true')
              <div class="col-lg-12">
                  <div class="alert alert-danger" role="alert"> This employee has exceeded the daily limit. </div>
              </div>
              @endif
              @if($monthly_limit_exceeded == 'true')
              <div class="col-lg-12">
                  <div class="alert alert-danger" role="alert"> This employee has exceeded the monthly limit. </div>
              </div>
              @endif
              @if($overall_limit_exceeded == 'true')
              <div class="col-lg-12">
                  <div class="alert alert-danger" role="alert"> This employee has exceeded the overall limit. </div>
              </div>
            </div>
              @endif
              <h2 class="card-title">Employee</h2>
              <div class="row">
                <div class="col">
                   <p><strong>Patient Name :</strong><br> {{ $employees->name }}</p>
                </div>            
                <div class="col">
                   <p><strong>Patient IC :</strong><br> {{ $employees->ic }}</p>
                </div>            
                <div class="col">
                   <p><strong>Date of Birth :</strong><br> {{ \Carbon\Carbon::parse($employees->dob)->format('d-m-Y') }}</p>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col">
                  <p><strong>Patient Address :</strong><br> {{ $employees->address }}</p>
                </div>       
                <div class="col">
                  <p><strong>Company :</strong><br> {{ $employees->company_name }}</p>
                </div>                
                <div class="col">
                  <p><strong>Employee ID :</strong><br> {{ $employees->company_employee_id }}</p>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col">
                  <p><strong>Daily Limit :</strong><br><span style="@if($daily_limit_exceeded == 'true') color: red @endif"><strong>{{ $daily_spent }}</strong></span> / {{ $employees->daily_limit }}</p>
                </div>    
                            
                <div class="col">
                  <p><strong>Monthly Limit :</strong><br><span style="@if($monthly_limit_exceeded == 'true') color: red @endif"><strong>{{ $monthly_spent }}</strong></span> / {{ $employees->monthly_limit }} ( {{ $employees->monthly_limit_start_date }} - {{ $employees->monthly_limit_end_date }} )</p>
                </div>    
                <div class="col">
                  <p><strong>Overall Limit :</strong><br><span style="@if($overall_limit_exceeded == 'true') color: red @endif"><strong>{{ $overall_spent }}</strong></span> / {{ $employees->overall_limit }} ( {{ $employees->overall_limit_start_date }} - {{ $employees->overall_limit_end_date }} )</p>
                </div>
              </div>
              <div class="row mt-3">
                <div class="col">
                  <p><strong>Total MC taken :</strong><br> {{ $number_of_mc }}</p>
                </div> 
                <div class="col">
                  <p><strong>Status :</strong><br> {{ ucfirst($employees->status) }}</p>
                </div>  
                <div class="col">
                </div>                
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-body">
              <h2 class="card-title">Dependents</h2>
              @if(count($dependents_of_employee) == '0') No records found. @endif
              <table class="table table-bordered table-hover bg-white @if(count($dependents_of_employee) == '0') d-none @endif" id="dependents_table">
                  <thead>
                    <tr>
                      <th scope="col" style="width:5%" class="text-center">#</th>
                      <th scope="col" style="width: 15%">Name</th>
                      <th scope="col">IC</th>
                      <th scope="col" style="width: 10%">Contact</th>
                      <th scope="col" style="width: 10%">Category</th>
                      <th scope="col" style="width:10%" class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($dependents_of_employee as $index => $dependent)
                      <tr>
                        <th scope="row" class="text-center">{{ $index+1 }}</th>
                        <td>{{ $dependent['name'] }}</td>
                        <td>{{ $dependent['ic'] }}</td>
                        <td>{{ $dependent['contact'] }}</td>
                        <td>{{ ucfirst($dependent['category']) }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_dependent_modal{{ $index }}">View</button>  
                        </td>
                      </tr>                 
                    @endforeach
                  </tbody>
              </table>
              @foreach($dependents_of_employee as $index => $dependent)
              <div class="modal fade" id="view_dependent_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                          Employee Details
                        </div>
                        <div class="modal-body">
                          <table class="table table-bordered" id="details_table">
                            <tbody>
                              <tr>
                                <th class="table-active w-25">Dependent Name</th>
                                <td class="w-25">{{ $dependent['name'] }}</td>
                                <th class="table-active w-25">Created At</th>
                                <td class="w-25">{{ \Carbon\Carbon::parse($dependent['created_at'])->format('d-m-Y') }}</td>
                              </tr>                              
                              <tr>
                                <th class="table-active w-25">Category</th>
                                <td class="w-25">{{ ucfirst($dependent['category']) }}</td>
                                <th class="table-active w-25">IC</th>
                                <td class="w-25">{{ $dependent['ic'] }}</td>
                              </tr>  
                              <tr>
                                <th class="table-active w-25">Date of Birth</th>
                                <td class="w-25">{{ $dependent['dob'] }}</td>
                                <th class="table-active w-25">Contact</th>
                                <td class="w-25">{{ $dependent['contact'] }}</td>
                              </tr>      
                              <tr>
                                <th class="table-active w-25">Address</th>
                                <td class="w-25">{{ $dependent['address'] }}</td>
                                <th class="table-active w-25">Status</th>
                                <td class="w-25">{{ $dependent['status'] }}</td>
                              </tr>                                 
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
          <div class="card">
            <div class="card-body">
              <h2 class="card-title">Consultation History</h2>
              @if($consultations->count() == '0') No records found. @endif
              <table class="table table-bordered table-hover bg-white @if($consultations->count() == '0') d-none @endif" id="clinic_table">
                  <thead>
                    <tr>
                      <th scope="col" style="width:5%" class="text-center">#</th>
                      <th scope="col" style="width: 15%">Doctor</th>
                      <th scope="col">Diagnosis</th>
                      <th scope="col" style="width: 10%">Date</th>
                      <th scope="col" style="width:10%" class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($consultations as $index => $consultation)
                      <tr>
                        <th scope="row" class="text-center">{{ $index+1 }}</th>
                        <td>{{ $consultation->doctor_name }}</td>
                        <td>{{ $consultation->description }}</td>
                        <td>{{ \Carbon\Carbon::parse($consultation->created_at)->format('d-m-Y') }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_consultation_modal{{ $index }}">View</button>  
                        </td>
                      </tr>                 
                    @endforeach
                  </tbody>
              </table>
              {{ $consultations->appends(request()->input())->links() }}
              @foreach($consultation_details as $index => $consultation_detail)
              <div class="modal fade" id="view_consultation_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                          Consultation Details
                        </div>
                        <div class="modal-body">
                          <table class="table table-bordered" id="details_table">
                            <tbody>
                              <tr>
                                <th class="table-active w-25">Patient Name</th>
                                <td class="w-25">{{ $consultation_detail->employee_name }}</td>
                                <th class="table-active w-25">Consultation Date</th>
                                <td class="w-25">{{ \Carbon\Carbon::parse($consultation_detail->created_at)->format('d-m-Y') }}</td>
                              </tr>                              
                              <tr>
                                <th class="table-active w-25">Consultation Doctor</th>
                                <td class="w-25">{{ $consultation_detail->doctor_name }}</td>
                                <th class="table-active w-25">Consultation Price</th>
                                <td class="w-25">RM {{ number_format((float)($consultation_detail->price), 2) }}</td>
                              </tr>  
                              <tr>
                                <th class="table-active w-25">Clinic Name</th>
                                <td class="w-25">{{ $consultation_detail->clinic_name }}</td>
                                <th class="table-active w-25">Clinic Admin</th>
                                <td class="w-25">{{ $consultation_detail->clinic_admin }}</td>
                              </tr>      
                              <tr>
                                <th class="table-active w-25">MC</th>
                                <td class="w-25">
                                  @if($consultation_detail->mc_startdate == $consultation_detail->mc_enddate)
                                  {{ $consultation_detail->mc_startdate }}
                                  @else
                                  From {{ $consultation_detail->mc_startdate}} to {{$consultation_detail->mc_enddate}}
                                  @endif
                                </td>
                                <th class="table-active w-25">Medications</th>
                                <td class="w-25">
                                {{ $consultation_detail->medications_name }}
                                </td>
                              </tr>                               
                              <tr>
                                <td id="dash_description_id" colspan="4">
                                  {!! nl2br($consultation_detail->description) !!}
                              </tr>
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
    @endif
</div>
@endsection