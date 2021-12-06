@extends('layouts.admin')

@section('pageTitle', 'Admin :: Patient Details')

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
  #amount_card{
    font-size: 16px !important;
    border: none;
    background-color: #e8e8e7;
  }
</style>
@endsection

@section('sub_header')
  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
  <li class="breadcrumb-item active">Patient Details</li>
@endsection

@section('content')
<div class="container-fluid">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="float-left">
          <form action="{{ url('admin/employee') }}" method="GET">
            <input type="text" name="search" id="search" class="form-control" placeholder="Search Name, IC or Employee ID">
          </form>
        </div>
        <div class="float-right mb-3">
          <a href="{{ url('admin/employee/new') }}" class="btn btn-info text-white"><i class="fa fa-plus"></i> Register Employee</a>
          <button type="button" class="btn btn-info text-white" data-toggle="modal" data-target="#filter_employee_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
          <div class="modal fade" id="filter_employee_modal" tabindex="-1" role="dialog" aria-labelledby="employeeFilterModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="employeeFilterModal"><strong>Filter</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('admin/employee') }}" method="GET">
                      <div class="modal-body">
                        <label for="status">Status</label>
                        <select name="status" class="custom-select">
                          <option selected></option>
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
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
        <table class="table table-bordered table-hover bg-white" id="patient_details_table">
          <thead>
            <tr>
              <th scope="col" style="width:5%" class="text-center">#</th>
              <th scope="col">Employee Name</th>
              <th scope="col" style="width: 15%">Company</th>
              <th scope="col" style="width: 10%">Monthly Limit Exceeded</th>
              <th scope="col" style="width: 10%">Yearly Limit Exceeded</th>
              <th scope="col" style="width:10%" class="text-center">Status</th>
              <th scope="col" style="width:15%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($employees as $index => $employee)
              <tr>
                  <th scope="row" class="text-center">{{ $index+1 }}</th>
                  <td>{{ $employee->name }}</td>
                  <td>{{ $employee->company_name }}</td>
                  <td class="text-center">
                    @if($employee->monthly_limit_exceeded == 'false') <span class="badge badge-success">Not Exceeded</span> @endif
                    @if($employee->monthly_limit_exceeded == 'true') <span class="badge badge-danger">Exceeded</span> @endif
                  </td>
                  <td class="text-center">
                    @if($employee->yearly_limit_exceeded == 'false') <span class="badge badge-success">Not Exceeded</span> @endif
                    @if($employee->yearly_limit_exceeded == 'true') <span class="badge badge-danger">Exceeded</span> @endif
                  </td>
                  <td class="text-center">
                    @if($employee->status == 'active') <span class="badge badge-success">Active</span> @endif
                    @if($employee->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                  </td>
                  <td class="text-center">
                      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_patient_modal{{ $index }}">Details</button>  
                  </td>
              </tr>
            @endforeach
          </tbody>
      </table>
      @foreach($employees as $index => $employee)
      <div class="modal fade" id="view_patient_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                  <strong>Patient Details</strong>
                </div>
                <div class="modal-body">
                  <table class="table table-bordered" id="details_table">
                    <tbody>
                      <tr>
                        <th class="table-active w-25">Employee Name</th>
                        <td class="w-25">{{ $employee->name }}</td>
                        <th class="table-active w-25">IC</th>
                        <td class="w-25">{{ $employee->ic }}</td>
                      </tr>                        
                      <tr>
                        <th class="table-active w-25">Company Name</th>
                        <td class="w-25">{{ $employee->company_name }}</td>
                        <th class="table-active w-25">Employee ID</th>
                        <td class="w-25">{{ $employee->company_employee_id }}</td>
                      </tr>                             
                      <tr>
                        <th class="table-active w-25">Registered Date</th>
                        <td class="w-25">{{ \Carbon\Carbon::parse($employee->created_at)->format('d-m-Y') }}</td>
                        <th class="table-active w-25">Status</th>
                        <td class="w-25">
                          @if($employee->status == 'active') <span class="badge badge-success">Active</span> @endif
                          @if($employee->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                        </td>
                      </tr>                             
                    </tbody>
                  </table>
                  <div class="card" id="amount_card">
                    <div class="card-body">
                        <strong>Total Consultation: </strong><strong>{{ count($consultations->where('ic', $employee->ic)) }}</strong><br>
                        <strong>Total Consultation Amount: RM </strong><strong>{{ number_format((float)($consultations->where('ic', $employee->ic)->sum('price')), 2) }}</strong>
                    </div>
                </div> 
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
      </div>
      @endforeach
      {{ $employees->appends(request()->input())->links() }}
      </div>
    </div>
</div>
</div>
@endsection