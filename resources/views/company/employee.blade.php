@extends('layouts.company')

@section('pageTitle', 'Employee')

@section('styles')
<style>
  .badge {
    font-size: 0.9rem;
  }
  #search{
        width: 250px !important;
    }
    .modal-header, .modal-title{
    font-size: 16px !important;
}
</style>
@endsection

@section('sub_header')
  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
  <li class="breadcrumb-item active">Employee Details</li>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="float-left">
          <form action="{{ url('company/employee') }}" method="GET">
            <input type="text" name="search" id="search" class="form-control" placeholder="Search Name, IC or Employee ID">
          </form>
        </div>
        <div class="float-right mb-3">
          <a href="/company/employee/new" class="btn btn-info text-white"><i class="fa fa-plus"></i> Register Employee</a>
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
                    <form action="{{ url('company/employee') }}" method="GET">
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
        <table class="table table-bordered table-hover bg-white" id="clinic_table">
          <thead>
            <tr>
              <th scope="col" style="width:5%" class="text-center">#</th>
              <th scope="col">Employee Name</th>
              <th scope="col" style="width: 15%">IC</th>
              <th scope="col" style="width: 15%">Employee ID</th>
              <th scope="col" style="width:10%" class="text-center">Status</th>
              <th scope="col" style="width:15%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($employees as $index => $employee)
              <tr>
                  <th scope="row" class="text-center">{{ $index+1 }}</th>
                  <td>{{ $employee->name }}</td>
                  <td>{{ $employee->ic }}</td>
                  <td>{{ $employee->company_employee_id }}</td>
                  <td class="text-center">
                    @if($employee->status == 'active') <span class="badge badge-success">Active</span> @endif
                    @if($employee->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                  </td>
                  <td class="text-center">
                      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#edit_employee_modal{{ $index }}">Edit</button>  
                      <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete_employee_modal{{ $index }}">Delete</button>  
                  </td>
              </tr>
              <div class="modal fade" id="edit_employee_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="editemployeeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editemployeeModalLabel"><strong>Employee Details</strong></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ url('company/employee/update', $employee->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                          <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">Employee Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $employee->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="ic">Employee IC</label>
                                    <input type="text" class="form-control" name="ic" value="{{ $employee->ic }}" readonly>
                                </div>
                            </div>                            
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="contact">Employee Contact</label>
                                    <input type="text" class="form-control" name="contact" value="{{ $employee->contact }}">
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="dob">Date of Birth</label>
                                  <input type="date" class="form-control" name="dob" value="{{ $employee->dob }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="address">Employee Address</label>
                                    <textarea type="text" class="form-control" name="address">{{ $employee->address }}</textarea>
                                </div>
                                <div class="col">
                                    <label for="address">Employee Status</label>
                                    <select class="form-select" name="status">
                                        <option value="active" @if($employee->status == 'active') selected @endif>Active</option>
                                        <option value="inactive" @if($employee->status == 'inactive') selected @endif>Inactive</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Save changes</button>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="delete_employee_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                        <strong>Delete Employee</strong>
                      </div>
                      <div class="modal-body">
                          Are you sure to delete this employee?
                      </div>
                      <form action="{{ url('company/employee/delete', $employee->id) }}" method="POST">
                        @csrf
                        @method('delete')
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                      </form>
                  </div>
              </div>
            </div>            
            @endforeach
          </tbody>
      </table>
      {{ $employees->appends(request()->input())->links() }}
      </div>
    </div>
</div>
@endsection