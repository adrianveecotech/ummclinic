@extends('layouts.admin')

@section('pageTitle', 'Clinic :: Register')

@section('sub_header')
  <li class="breadcrumb-item"><a href="/" class="text-dark">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="/admin/clinic" class="text-dark">Clinic Details</a></li>
  <li class="breadcrumb-item active">Register Clinic</li>
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
            <div id="header">Register Clinic</div>
        </div>
        <div class="card-body">
            <form action="{{ url('/admin/clinic/store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Clinic Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="email">Clinic Email</label>
                    <input type="email" class="form-control" name="email" pattern="[a-zA-Z0-9.-_]{1,}@[a-zA-Z.-]{2,}[.]{1}[a-zA-Z]{2,}" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="address">Clinic Address</label>
                    <textarea class="form-control" name="address" rows="2" value="{{ old('address') }}" required></textarea>
                </div>
                <div class="form-group">
                    <label for="contact">Clinic Contact</label>
                    <input type="text" class="form-control" name="contact" pattern="(\+?6?01)[0-46-9]-*[0-9]{7,8}" value="{{ old('contact') }}" required>
                </div>
                <div class="form-group">
                    <label for="contact">Branch</label>
                    <select name="branch" class="custom-select">
                        <option value="0">No branch</option>
                        @foreach($clinics as $clinic)
                          <option value="{{$clinic->id}}">{{$clinic->name}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Register</button>
            </form>          
        </div>
    </div>
</div>
@endsection