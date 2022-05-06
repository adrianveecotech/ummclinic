@extends('layouts.clinic')

@section('pageTitle', 'Dependent :: Register')

@section('sub_header')
  <li class="breadcrumb-item"><a href="/" class="text-dark">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="/clinic/dependent" class="text-dark">Dependent Details</a></li>
  <li class="breadcrumb-item active">Register Dependent</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">  
            <form action="{{ url('/clinic/dependent/store') }}" method="POST">
                @csrf
                    <input type="hidden" name="employee" value="{{Request::get('employee')}}">
                    <div class="form-group">
                        <label for="contact">Employee Name</label>
                        <input type="text" class="form-control" name="employee_name" value="{{ $employee_name }}" disabled>
                    </div>    
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>                    
                    <div class="form-group">
                        <label for="ic">IC</label>
                        <input type="text" class="form-control" name="ic" required>
                    </div>                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact Number</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Category</label>
                        <select name="category" class="custom-select">
                          <option value="spouse">Spouse</option>
                          <option value="dependent">Dependent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-2">
                                <label for="monthly_limit">Monthly Limit</label>
                                <input type="text" class="form-control" name="monthly_limit" required>
                            </div>
                            <div class="col-2">
                                <label for="monthly_limit_start_date">Start Date</label>
                                <input type="date" class="form-control" name="monthly_limit_start_date" required>
                            </div>
                            <div class="col-2">
                                <label for="monthly_limit_end_date">End Date</label>
                                <input type="date" class="form-control" name="monthly_limit_end_date" required>
                            </div>
                            <!-- <div class="col-2">
                                <label for="monthly_limit_distributed">Monthly?</label>
                                <div>
                                    <input type="checkbox" name="monthly_limit_distributed" value="true" >
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dob">Daily Limit</label>
                        <input type="text" class="form-control" name="daily_limit" required>
                    </div>
                <button type="submit" class="btn btn-success">Register</button>
            </form>          
        </div>
    </div>
</div>
@endsection