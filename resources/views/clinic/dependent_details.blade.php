@extends('layouts.clinic')

@section('pageTitle', 'Clinic :: Dependent Details')

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
  <li class="breadcrumb-item active">Dependent Details</li>
@endsection

@section('content')
<div class="container-fluid">
  <div class="container-fluid">
    <div class="card">
      <div class="card-body">
        <div class="float-left">
          <form action="{{ url('clinic/dependent') }}" method="GET">
            <input type="text" name="search" id="search" class="form-control" placeholder="Search Name, IC or Dependent ID">
          </form>
        </div>
        <div class="float-right mb-3">
          <button type="button" class="btn btn-info text-white" data-toggle="modal" data-target="#filter_dependent_modal"><i class="fa fa-filter" aria-hidden="true"></i></button>
          <div class="modal fade" id="filter_dependent_modal" tabindex="-1" role="dialog" aria-labelledby="dependentFilterModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dependentFilterModal"><strong>Filter</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ url('clinic/dependent') }}" method="GET">
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
        <table class="table table-bordered table-hover bg-white" id="dependent_details_table">
          <thead>
            <tr>
              <th scope="col" style="width:5%" class="text-center">#</th>
              <th scope="col">Dependent Name</th>
              <th scope="col">Employee Name</th>
              <th scope="col" style="width:10%" class="text-center">Category</th>
              <th scope="col" style="width:8%" class="text-center">Status</th>
              <th scope="col" style="width:7%" class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dependents as $index => $dependent)
              <tr>
                  <th scope="row" class="text-center">{{ $index+1 }}</th>
                  <td>{{ $dependent->name }}</td>
                  <td>{{ $dependent->employee_name }}</td>
                  <td>{{ ucfirst($dependent->category) }}</td>
                  <td class="text-center">
                    @if($dependent->status == 'active') <span class="badge badge-success">Active</span> @endif
                    @if($dependent->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                  </td>
                  <td class="text-center">
                      <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#view_dependent_modal{{ $index }}">Details</button>  
                  </td>
              </tr>
            @endforeach
          </tbody>
      </table>
      @foreach($dependents as $index => $dependent)
      <div class="modal fade" id="view_dependent_modal{{ $index }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                  <strong>Dependent Details</strong>
                </div>
                <div class="modal-body">
                  <table class="table table-bordered" id="details_table">
                    <tbody>
                      <tr>
                        <th class="table-active w-25">Dependent Name</th>
                        <td class="w-25">{{ $dependent->name }}</td>
                        <th class="table-active w-25">IC</th>
                        <td class="w-25">{{ $dependent->ic }}</td>
                      </tr>    
                      <tr>
                        <th class="table-active w-25">Address</th>
                        <td class="w-25">{{ $dependent->address }}</td>
                        <th class="table-active w-25">Contact</th>
                        <td class="w-25">{{ $dependent->contact }}</td>
                      </tr>                                               
                      <tr>
                        <th class="table-active w-25">Registered Date</th>
                        <td class="w-25">{{ \Carbon\Carbon::parse($dependent->created_at)->format('d-m-Y') }}</td>
                        <th class="table-active w-25">Status</th>
                        <td class="w-25">
                          @if($dependent->status == 'active') <span class="badge badge-success">Active</span> @endif
                          @if($dependent->status == 'inactive') <span class="badge badge-danger">Inactive</span> @endif
                        </td>
                      </tr>                             
                    </tbody>
                  </table>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
      </div>
      @endforeach
      {{ $dependents->appends(request()->input())->links() }}
      </div>
    </div>
</div>
</div>
@endsection