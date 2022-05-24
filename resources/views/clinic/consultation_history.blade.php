@extends('layouts.clinic')

@section('pageTitle', 'Clinic :: Consultation History')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">Consultation History</li>
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
                <form action="{{ url('clinic/consultation') }}" method="GET">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search IC or Company Name">
                </form>
            </div>
            <div class="float-right">
                <button type="button" class="btn btn-info text-white mb-3" data-toggle="modal" data-target="#filter_consultation_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
                  <div class="modal fade" id="filter_consultation_modal" tabindex="-1" role="dialog" aria-labelledby="consultationFilterModal" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="consultationFilterModal">Filter</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <form action="{{ url('clinic/consultation') }}" method="GET">
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
          <table class="table table-bordered table-hover bg-white" id="payment_table">
            <thead>
              <tr>
                <th scope="col" style="width:5%" class="text-center">#</th>
                <th scope="col">Employee Name</th>
                <th scope="col" style="width: 15%">IC</th>
                <th scope="col" style="width: 15%">Company Name</th>
                <th scope="col" style="width: 10%">Date</th>
                <th scope="col" style="width:10%" class="text-center">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($consultation_details as $index => $consultation_detail)
                <tr>
                    <th scope="row" class="text-center">{{ $index+1 }}</th>
                    <td>{{ $consultation_detail->employee_name }}</td>
                    <td>{{ $consultation_detail->employee_ic }}</td>
                    <td>{{ $consultation_detail->company_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($consultation_detail->created_at)->format('d-m-Y') }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_consultation_modal{{ $index }}">View</button>  
                    </td>
                </tr>        
                @empty
                    <br><br> No records found.
              @endforelse
            </tbody>
        </table>
        {{ $consultation_details->appends(request()->input())->links() }}
        @forelse($consultation_details as $index => $consultation_detail)
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
                            <th class="table-active w-25">MC</th>
                            <td class="w-25"> @if($consultation_detail->mc_startdate == $consultation_detail->mc_enddate)
                                  {{ $consultation_detail->mc_startdate }}
                                  @else
                                  From {{ $consultation_detail->mc_startdate}} to {{$consultation_detail->mc_enddate}}
                                  @endif</td>
                            <th class="table-active w-25">Medications</th>
                            <td class="w-25">{{ $consultation_detail->medications_name }}</td>
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
          @empty
            <div>No record found.</div>  
          @endforelse
        </div>
    </div>
</div>
@endsection