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
              <th scope="col" style="width: 15%">Monthly Limit</th>
              <th scope="col" style="width: 15%">Overall Limit</th>
              <th scope="col" style="width: 15%">Daily Limit</th>
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
                    @if($employee->monthly_limit_exceeded == 'false') <span class="badge badge-success">Not Exceeded</span> @endif
                    @if($employee->monthly_limit_exceeded == 'true') <span class="badge badge-danger">Exceeded</span> @endif
                    @if($employee->monthly_limit_exceeded == 'Limit not set') <span class="badge badge-warning">Limit not set</span> @endif
                  </td>
                  <td class="text-center">
                    @if($employee->overall_limit_exceeded == 'false') <span class="badge badge-success">Not Exceeded</span> @endif
                    @if($employee->overall_limit_exceeded == 'true') <span class="badge badge-danger">Exceeded</span> @endif
                  </td>
                  <td class="text-center">
                    @if($employee->daily_limit_exceeded == 'false') <span class="badge badge-success">Not Exceeded</span> @endif
                    @if($employee->daily_limit_exceeded == 'true') <span class="badge badge-danger">Exceeded</span> @endif
                    @if($employee->daily_limit_exceeded == 'Limit not set') <span class="badge badge-warning">Limit not set</span> @endif
                  </td>
                  <td class="text-center">
                    @if($employee->status == 'active') <span class="badge badge-success">Active</span> @endif
                    @if($employee->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                  </td>
                  <td class="text-center">
                    <div class="dropdown">
                      <button <?php echo 'onclick="openDropDown('.$index.')"'; ?> class="dropbtn">Actions <i class="fa fa-angle-down"></i></button>
                      <div id="myDropdown[{{$index}}]" class="dropdown-content">
                        <a href="#" data-toggle="modal" data-toggle="modal" data-target="#edit_employee_modal{{ $index }}">Edit</a>
                        <a href="#" data-toggle="modal" data-toggle="modal" data-target="#delete_employee_modal{{ $index }}">Delete</a>
                        <a href="{{ url('company/dependent/new') . '?employee='.$employee->id }}">Register Dependent</a>
                      </div>
                    </div>
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
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="date_joined">Date of Joined</label>
                                    <input type="date" class="form-control" name="date_joined" value="{{ $employee->date_joined }}"></input>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="department">Department</label>
                                    <input type="text" class="form-control" name="department" value="{{ $employee->department }}"></input>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="monthly_limit">Monthly limit</label>
                                    <input type="text" class="form-control" name="monthly_limit" value="{{ $employee->monthly_limit }}"></input>
                                </div>
                                <div class="form-group col-md-3">
                                  <label for="monthly_limit_start_date">Start Date</label>
                                  <input type="date" class="form-control" name="monthly_limit_start_date" value="{{ $employee->monthly_limit_start_date }}" required>
                                </div>
                                <div class="form-group col-md-3">
                                  <label for="monthly_limit_end_date">End Date</label>
                                  <input type="date" class="form-control" name="monthly_limit_end_date" value="{{ $employee->monthly_limit_end_date }}" required>
                                </div>  
                            </div>
                            <div class="row">
                              <div class="form-group col-md-6">
                                <label for="yearly_limit">Overall limit</label>
                                <input type="text" class="form-control" name="overall_limit" value="{{ $employee->overall_limit }}"></input>
                              </div>
                              <div class="form-group col-md-3">
                                <label for="overall_limit_start_date">Start Date</label>
                                <input type="date" class="form-control" name="overall_limit_start_date" value="{{ $employee->overall_limit_start_date }}" required>
                              </div>
                              <div class="form-group col-md-3">
                                <label for="overall_limit_end_date">End Date</label>
                                <input type="date" class="form-control" name="overall_limit_end_date" value="{{ $employee->overall_limit_end_date }}" required>
                              </div>  
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="daily_limit">Daily Limit</label>
                                    <input type="text" class="form-control" name="daily_limit" value="{{ $employee->daily_limit }}"></input>
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

@section('scripts')
<script type="text/javascript">
  function openDropDown(index) {
      closeDropDown();
      document.getElementById("myDropdown["+ index +"]").classList.toggle("show");
      }

      window.onclick = function(event) {
      if (!event.target.matches('.dropbtn')) {
          closeDropDown();
      }
  }

  function closeDropDown(){
      var dropdowns = document.getElementsByClassName("dropdown-content");
          var i;
          for (i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.classList.contains('show')) {
              openDropdown.classList.remove('show');
          }
      }
  }
</script>
@endsection