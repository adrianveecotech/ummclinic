@extends('layouts.clinic')

@section('pageTitle', 'Clinic :: Doctor')

@section('sub_header')
  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
  <li class="breadcrumb-item active">Staff</li>
@endsection

@section('styles')
<style>
  .badge {
    font-size: 0.9rem;
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
        <form action="{{ url('clinic/staff') }}" method="GET">
            <input type="text" name="search" id="search" class="form-control" placeholder="Search Doctor Name">
        </form>
      </div>
      <div class="float-right mb-3">
        <a href="/clinic/staff/new" class="btn btn-info text-white"><i class="fa fa-plus"></i> Register Doctor</a>
        <button type="button" class="btn btn-info text-white" data-toggle="modal" data-target="#filter_doctor_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
        <div class="modal fade" id="filter_doctor_modal" tabindex="-1" role="dialog" aria-labelledby="doctorFilterModal" aria-hidden="true">
          <div class="modal-dialog" role="document">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title" id="doctorFilterModal">Filter</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                  <form action="{{ url('clinic/staff') }}" method="GET">
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
            <th scope="col">Doctor Name</th>
            <th scope="col">Contact</th>
            <th scope="col" style="width: 10%" class="text-center">Status</th>
            <th scope="col" style="width:15%" class="text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($doctors as $index => $doctor)
            <tr>
                <th scope="row" class="text-center">{{ $index+1 }}</th>
                <td>{{ $doctor->name }}</td>
                <td>{{ $doctor->contact }}</td>
                <td class="text-center">
                  @if($doctor->status == 'active') <span class="badge badge-success">Active</span> @endif
                  @if($doctor->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#edit_doctor_modal{{ $index }}">Edit</button>  
                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete_doctor_modal{{ $index }}">Delete</button>  
                </td>
            </tr>
            <div class="modal fade" id="edit_doctor_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="editdoctorModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="editdoctorModalLabel">Doctor Details</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <form action="{{ url('clinic/staff/update', $doctor->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                        <div class="modal-body">
                          <div class="row">
                            <input type="hidden" class="form-control" name="clinic_id" value="{{ $doctor->clinic_id }}">
                              <div class="form-group col-md-6">
                                  <label for="name">Doctor Name</label>
                                  <input type="text" class="form-control" name="name" value="{{ $doctor->name }}">
                              </div>
                              <div class="form-group col-md-6">
                                  <label for="ic">IC</label>
                                  <input type="text" class="form-control" name="ic" value="{{ $doctor->ic }}" readonly>
                              </div>
                          </div>                            
                          <div class="row">
                              <div class="form-group col-md-6">
                                  <label for="contact">Contact</label>
                                  <input type="text" class="form-control" name="contact" value="{{ $doctor->contact }}">
                              </div>
                              <div class="form-group col-md-6">
                                <label for="dob">Date of Birth</label>
                                <input type="date" class="form-control" name="dob" value="{{ $doctor->dob }}">
                              </div>
                          </div>
                          <div class="row">
                              <div class="form-group col">
                                  <label for="address">Address</label>
                                  <textarea type="text" class="form-control" name="address">{{ $doctor->address }}</textarea>
                              </div>
                              <div class="col">
                                  <label for="address">Status</label>
                                  <select class="custom-select" name="status">
                                      <option value="active" @if($doctor->status == 'active') selected @endif>Active</option>
                                      <option value="inactive" @if($doctor->status == 'inactive') selected @endif>Inactive</option>
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
          <div class="modal fade" id="delete_doctor_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                      Delete Employee
                    </div>
                    <div class="modal-body">
                        Are you sure to delete this doctor?
                    </div>
                    <form action="{{ url('clinic/staff/delete', $doctor->id) }}" method="POST">
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
    {{ $doctors->appends(request()->input())->links() }}
    </div>
  </div>
</div>
@endsection