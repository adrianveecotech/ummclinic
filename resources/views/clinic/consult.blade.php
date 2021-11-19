@extends('layouts.clinic')

@section('pageTitle', 'Clinic :: Consult')

@section('styles')
<style>
.card{
    border-radius: 10px;
}
.card-title{
    font-size: 17px !important;
    font-weight: 500;
}
p{
  font-size: 16px !important;
}
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

</style>
@endsection

@section('sub_header')
  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="/clinic/search">Patient</a></li>
  <li class="breadcrumb-item active">Consultation</li>
@endsection

@section('content')
<div class="container">
  @foreach($employees as $employee)
    <div class="card">
        <div class="card-body">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Patient Details</h2>
                    <div class="row">
                        <div class="col">
                            <p><strong>Patient Name :</strong><br> {{ $employee->name }}</p>
                        </div>            
                        <div class="col">
                            <p><strong>Patient IC :</strong><br> {{ $employee->ic }}</p>
                        </div>            
                        <div class="col">
                            <p><strong>Date of Birth :</strong><br> {{ $employee->dob }}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <p><strong>Patient Address :</strong><br> {{ $employee->address }}</p>
                        </div>                
                        <div class="col">
                            <p><strong>Company :</strong><br> {{ $employee->company_name }}</p>
                        </div>                
                        <div class="col">
                            <p><strong>Employee ID :</strong><br> {{ $employee->company_employee_id }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <form action="{{ url('clinic/consult/store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <h2 class="card-title">Consultation</h2>
                        <input type="hidden" name="ic" value="{{ $employee->ic }}">
                        <input type="hidden" name="company_employee_id" value="{{ $employee->company_employee_id }}">
                        <input type="hidden" name="company_id" value="{{ $employee->company_id }}">
                        <input type="hidden" name="clinic_id" value="{{ Auth::user()->clinic_id }}">
                        <div class="row">
                            <div class="col">
                                <strong><label for="doctor_id">Doctor</label></strong>
                                <select class="custom-select mb-3" name="doctor_id">
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                    @endforeach
                                  </select>
                            </div>
                            <div class="col">
                                <strong><label for="price">Price</label></strong>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text" id="basic-addon1">RM</span>
                                    </div>
                                    <input type="number" class="form-control" name="price" min="1" step="0.01" pattern="\$?(\d)+(\.\d\d)?">
                                </div>
                            </div>
                        </div>
                        <strong><label for="description">Description</label></strong>
                        <textarea name="description" rows="10" class="form-control"></textarea><br>
                        <button type="submit" class="btn btn-success">Submit Consultation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
</div>
@endsection