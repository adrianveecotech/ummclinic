@extends('layouts.admin')

@section('pageTitle', 'Company :: Register')

@section('sub_header')
  <li class="breadcrumb-item"><a href="/" class="text-dark">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="/admin/company" class="text-dark">Company Details</a></li>
  <li class="breadcrumb-item active">Register Company</li>
@endsection

@section('styles')
<style>
    #header{
        font-size: 17px !important;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div id="header">Register Company</div>
        </div>
        <div class="card-body">
            <form action="{{ url('/admin/company/store') }}" method="POST">
                @csrf
                    <div class="form-group">
                        <label for="name">Company Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Company Email</label>
                        <input type="email" class="form-control" name="email" pattern="[a-zA-Z0-9.-_]{1,}@[a-zA-Z.-]{2,}[.]{1}[a-zA-Z]{2,}" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Company Address</label>
                        <textarea class="form-control" name="address" rows="2" value="{{ old('address') }}" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact">Company Contact</label>
                        <input type="text" class="form-control" name="contact" pattern="(\+?6?01)[0-46-9]-*[0-9]{7,8}" value="{{ old('contact') }}" required>
                    </div>
                <button type="submit" class="btn btn-success">Register</button>
            </form>          
        </div>
    </div>
</div>
@endsection