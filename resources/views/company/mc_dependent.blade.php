@extends('layouts.company')

@section('pageTitle', 'Company :: MC')

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{url('company/mc')}}">MC</a></li>
<li class="breadcrumb-item active">{{$employee->name}}</li>
@endsection

@section('styles')
<style>
  #search{
        width: 250px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
              <table class="table table-bordered table-hover bg-white" id="consultation_table">
                <thead>
                  <tr>
                    <th scope="col">Dependent Name</th>
                    <th scope="col">Dependent IC</th>
                    <th scope="col">Clinic Name</th>
                    <th scope="col">MC</th>
                    <th scope="col">Diagnosis</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($consultations as $index => $consultation)
                    <tr>
                      <td>{{$consultation->employee_name}}</td>
                      <td>{{$consultation->employee_ic}}</td>
                      <td>{{$consultation->clinic_name}}</td>
                      <td>{{$consultation->mc_startdate}} - {{$consultation->mc_enddate}}</td>
                      <td>{{$consultation->description}}</td>
                    </td>
                    </tr>                 
                  @endforeach
                </tbody>
            </table>
            {{ $consultations->appends(request()->input())->links() }}
        </div>
      </div>
        </div>
    </div>
</div>
@endsection