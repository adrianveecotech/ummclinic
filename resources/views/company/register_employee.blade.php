@extends('layouts.company')

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
            <form action="{{ url('/company/employee/store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="contact">Company Name</label>
                    <input type="hidden" class="form-control" name="company_id" value="{{ Auth::user()->company_id }}" required>
                    <input type="text" class="form-control" name="company_name" value="{{ $company_name->name }}" disabled>
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
                <button type="submit" class="btn btn-success">Register</button>
            </form>          
        </div>
    </div>
</div>
@endsection