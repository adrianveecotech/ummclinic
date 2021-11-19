@extends('layouts.clinic')

@section('pageTitle', 'Clinic :: Register Doctor')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item"><a href="/clinic/staff">Staff</a></li>
<li class="breadcrumb-item active">Register Staff</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><strong>Register Staff</strong></div>
        <div class="card-body">
            <form action="{{ url('/clinic/staff/store') }}" method="POST">
                @csrf
                <input type="hidden" class="form-control" name="clinic_id" value="{{ Auth::user()->clinic_id }}" required>
                    <div class="form-group">
                        <label for="name">Doctor Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>                    
                    <div class="form-group">
                        <label for="ic">IC</label>
                        <input type="text" class="form-control" name="ic" required>
                    </div>                    
                    <div class="form-group">
                        <label for="contact">Contact</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" name="address" rows="2" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" name="dob" required>
                    </div>
                <button type="submit" class="btn btn-success">Register</button>
            </form>          
        </div>
    </div>
</div>
@endsection