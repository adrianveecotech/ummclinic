@extends('layouts.clinic')

@section('pageTitle', 'Employee :: Register')

@section('sub_header')
  <li class="breadcrumb-item"><a href="/" class="text-dark">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="/company/employee" class="text-dark">Employee Details</a></li>
  <li class="breadcrumb-item active">Register Employee</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">  
            <form action="{{ url('/clinic/patient/store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="contact">Company Name</label>
                    <select name="company" class="custom-select">
                    <option selected></option>
                    @foreach($companies as $company)
                    <option value="{{$company->id}}">{{$company->name}}</option>
                    @endforeach
                    </select>
                </div>
                    <div class="form-group">
                        <label for="name">Employee Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>                    
                    <div class="form-group">
                        <label for="ic">Employee IC</label>
                        <input type="text" class="form-control" name="ic" required>
                    </div>                    
                    <div class="form-group">
                        <label for="company_employee_id">Company Employee ID</label>
                        <input type="text" class="form-control" name="company_employee_id" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Employee Address</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact">Employee Contact</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="dob">Employee Date of Birth</label>
                        <input type="date" class="form-control" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Date of Joined</label>
                        <input type="date" class="form-control" name="date_joined" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Department</label>
                        <input type="text" class="form-control" name="department" required>
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
                        </div>
                    </div>
                    <div class="form-group">
                         <div class="row">
                            <div class="col-2">
                                <label for="overall_limit">Overall Limit</label>
                                <input type="text" class="form-control" name="overall_limit" required>
                            </div>
                            <div class="col-2">
                                <label for="overall_limit_start_date">Start Date</label>
                                <input type="date" class="form-control" name="overall_limit_start_date" required>
                            </div>
                            <div class="col-2">
                                <label for="overall_limit_end_date">End Date</label>
                                <input type="date" class="form-control" name="overall_limit_end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="daily_limit">Daily Limit</label>
                        <input type="text" class="form-control col-2" name="daily_limit" required>
                    </div>
                <button type="submit" class="btn btn-success">Register</button>
            </form>          
        </div>
    </div>
</div>
@endsection