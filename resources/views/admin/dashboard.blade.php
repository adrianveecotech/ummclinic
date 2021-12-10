@extends('layouts.admin')

@section('pageTitle', 'Dashboard')

@section('styles')
<style>
    #total{
        font-size: 35px !important;
    }
    #name{
        font-size: 17px !important;
    }
    small{
        font-size: 40% !important;
    }
    .badge {
        font-size: 0.9rem;
    }
    #amount_card{
        font-size: 16px !important;
        border: none;
        background-color: #e8e8e7;
    }
    .modal-header, .modal-title{
        font-size: 16px !important;
    }
    #header{
        font-size: 17px !important;
    }
</style>
@endsection

@section('sub_header')
  <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">   
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div id="total">
                        <div>
                            {{ $count_today_consultation }}                        
                            <!-- @if($today_percent > 0)
                                <small>
                                    <span class="badge bg-success"><i class="fa fa-arrow-up" aria-hidden="true"></i> {{ $today_percent }}%</span>
                                </small>
                            @endif
                            @if($today_percent < 0)
                            <small>
                                <span class="badge bg-danger"><i class="fa fa-arrow-down" aria-hidden="true"></i> {{ $today_percent }}%</span>
                            </small>
                        @endif -->
                        </div>
                    </div>
                    <div id="name">
                        <div class="font-weight-bold">Today's Consultation</div>
                    </div>
                </div>  
                <div class="card-footer bg-primary text-white text-center">
                    <a href="/admin/consultation?start_date={{ Carbon\Carbon::now()->toDateString() }}&end_date={{ Carbon\Carbon::now()->toDateString() }}" class="btn text-white"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> More Info</a>
                </div>
            </div>
        </div>    
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div id="total">
                        <div>{{ $count_total_consultation }}</div>
                    </div>
                    <div id="name">
                        <div class="font-weight-bold">Total Consultation</div>
                    </div>
                </div>
                <div class="card-footer bg-warning text-white text-center">
                    <a href="/admin/consultation" class="btn text-white"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> More Info</a>
                </div>  
            </div>
        </div>    
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div id="total">
                        <div>{{ $count_total_clinic }}</div>
                    </div>
                    <div id="name">
                        <div class="font-weight-bold">Total Active Clinic</div>
                    </div>
                </div>  
                <div class="card-footer bg-success text-white text-center">
                    <a href="/admin/clinic?status=active" class="btn text-white"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> More Info</a>
                </div>             
            </div>
        </div>    
        <div class="col-sm-6 col-lg-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div id="total">
                        <div>{{ $count_total_company }}</div>
                    </div>
                    <div id="name">
                        <div class="font-weight-bold">Total Active Companies</div>
                    </div>
                </div> 
                <div class="card-footer bg-info text-white text-center">
                    <a href="/admin/company?status=active" class="btn text-white"><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> More Info</a>
                </div>    
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover bg-white" id="consultation_table">
                <thead>
                    <tr>
                        <td class="text-center" id="header" colspan="5"><strong>Consultation History</strong></td>
                    </tr>
                    <tr>
                        <th scope="col" style="width:5%" class="text-center">#</th>
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
                                </td>
                                </tr> 
                                <tr>
                                <th class="table-active w-25">Clinic Admin Name</th>
                                <td class="w-25">{{ $consultation->clinic_admin }}</td>                            
                                <th class="table-active w-25">Created At</th>
                                <td class="w-25">{{ $consultation->created_at }}</td>
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