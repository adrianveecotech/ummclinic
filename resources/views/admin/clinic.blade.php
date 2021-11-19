@extends('layouts.admin')

@section('pageTitle', 'Admin :: Clinic')

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

@section('sub_header')
  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
  <li class="breadcrumb-item active">Clinic Details</li>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="float-left">
          <form action="{{ url('admin/clinic') }}" method="GET">
            <input type="text" name="search" id="search" class="form-control" placeholder="Search Clinic Name">
          </form>
        </div>
        <div class="float-right mb-3">
          <a href="/admin/clinic/new" class="btn btn-info text-white"><i class="fa fa-plus"></i> Register Clinic</a>
          <button type="button" class="btn btn-info text-white" data-toggle="modal" data-target="#filter_clinic_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
          <div class="modal fade" id="filter_clinic_modal" tabindex="-1" role="dialog" aria-labelledby="clinicFilterModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clinicFilterModal">Filter</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/admin/clinic" method="GET">
                      <div class="modal-body">
                        <strong><label for="status">Status</label></strong>
                        <select name="status" class="custom-select">
                          <option value="active">Active</option>
                          <option value="inactive">Inactive</option>
                        </select>
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
        <table class="table table-bordered table-hover bg-white" id="clinic_table">
          <thead>
            <tr>
              <th scope="col" style="width:5%" class="text-center">#</th>
              <th scope="col">Clinic Name</th>
              <th scope="col" style="width: 25%">Email</th>
              <th scope="col" style="width:10%" class="text-center">Status</th>
              <th scope="col" style="width:15%">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($clinics as $index => $clinic)
              <tr>
                  <th scope="row" class="text-center">{{ $index+1 }}</th>
                  <td>{{ $clinic->name }}</td>
                  <td>{{ $clinic->email }}</td>
                  <td class="text-center">
                    @if($clinic->status == 'active') <span class="badge badge-success">Active</span> @endif
                    @if($clinic->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                  </td>
                  <td>
                      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#edit_clinic_modal{{ $index }}">Edit</button>  
                      <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete_clinic_modal{{ $index }}">Delete</button>  
                  </td>
              </tr>
              <div class="modal fade" id="edit_clinic_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="editClinicModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editClinicModalLabel">Clinic Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ url('admin/clinic/update', $clinic->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                          <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">Clinic Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $clinic->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="contact">Clinic Contact</label>
                                    <input type="text" class="form-control" name="contact" pattern="(\+?6?01)[0-46-9]-*[0-9]{7,8}" value="{{ $clinic->contact }}">
                                </div>
                            </div>                            
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="address">Clinic Address</label>
                                    <textarea type="text" class="form-control" name="address">{{ $clinic->address }}</textarea>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="email">Clinic Email</label>
                                  <input type="email" class="form-control" name="email" pattern="[a-zA-Z0-9.-_]{1,}@[a-zA-Z.-]{2,}[.]{1}[a-zA-Z]{2,}" value="{{ $clinic->email }}">
                                </div>
                            </div>
                            <label for="status">Status</label>
                            <select class="custom-select" name="status">
                              <option value="active" @if($clinic->status == 'active') selected @endif>Active</option>
                              <option value="inactive" @if($clinic->status == 'inactive') selected @endif>Inactive</option>
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
            <div class="modal fade" id="delete_clinic_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                        Delete Clinic
                      </div>
                      <div class="modal-body">
                          Are you sure to delete this clinic?
                      </div>
                      <form action="{{ url('admin/clinic/delete', $clinic->id) }}" method="POST">
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
      {{ $clinics->appends(request()->input())->links() }}
      </div>
    </div>
</div>
@endsection