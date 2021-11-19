@extends('layouts.admin')

@section('pageTitle', 'Admin :: Company')

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
  <li class="breadcrumb-item active">Company Details</li>
@endsection

@section('content')
<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="float-left">
          <form action="{{ url('admin/company') }}" method="GET">
            <input type="text" name="search" id="search" class="form-control" placeholder="Search Company Name">
          </form>
        </div>
        <div class="float-right mb-3">
          <a href="/admin/company/new" class="btn btn-info text-white"><i class="fa fa-plus"></i> Register Company</a>
          <button type="button" class="btn btn-info text-white" data-toggle="modal" data-target="#filter_company_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
          <div class="modal fade" id="filter_company_modal" tabindex="-1" role="dialog" aria-labelledby="filter_company_modal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filter_company_modal">Filter</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="/admin/company" method="GET">
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
        <table class="table table-bordered table-hover bg-white" id="company_table">
          <thead>
            <tr>
              <th scope="col" style="width:5%" class="text-center">#</th>
              <th scope="col">Company Name</th>
              <th scope="col" style="width: 25%">Email</th>
              <th scope="col" style="width:10%" class="text-center">Status</th>
              <th scope="col" style="width:15%">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($companies as $index => $company)
              <tr>
                  <th scope="row" class="text-center">{{ $index+1 }}</th>
                  <td>{{ $company->name }}</td>
                  <td>{{ $company->email }}</td>
                  <td class="text-center">
                    @if($company->status == 'active') <span class="badge badge-success">Active</span> @endif
                    @if($company->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                  </td>
                  <td>
                      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#edit_company_modal{{ $index }}">Edit</button>  
                      <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete_company_modal{{ $index }}">Delete</button>  
                  </td>          
              </tr>
              <div class="modal fade" id="edit_company_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="editCompanyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCompanyModalLabel">Company Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ url('admin/company/update', $company->id) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <div class="modal-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="name">Company Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $company->name }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="contact">Company Contact</label>
                                    <input type="text" class="form-control" name="contact" pattern="(\+?6?01)[0-46-9]-*[0-9]{7,8}" value="{{ $company->contact }}">
                                </div>
                            </div>                            
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="address">Company Address</label>
                                    <textarea type="text" class="form-control" name="address">{{ $company->address }}</textarea>
                                </div>
                                <div class="form-group col-md-6">
                                  <label for="email">Company Email</label>
                                  <input type="email" class="form-control" name="email" pattern="[a-zA-Z0-9.-_]{1,}@[a-zA-Z.-]{2,}[.]{1}[a-zA-Z]{2,}" value="{{ $company->email }}">
                                </div>
                            </div>
                              <label for="status">Status</label>
                              <select class="custom-select" name="status">
                                <option value="active" @if($company->status == 'active') selected @endif>Active</option>
                                <option value="inactive" @if($company->status == 'inactive') selected @endif>Inactive</option>
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
            <div class="modal fade" id="delete_company_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="deleteCompanyModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                        Delete Company
                      </div>
                      <div class="modal-body">
                          Are you sure to delete this company?
                      </div>
                      <form action="{{ url('admin/company/delete', $company->id) }}" method="POST">
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
      {{ $companies->appends(request()->input())->links() }}
      </div>
    </div>
</div>
@endsection