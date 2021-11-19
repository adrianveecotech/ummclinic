@extends('layouts.company')

@section('pageTitle', 'Change Password')

@section('sub_header')
  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
  <li class="breadcrumb-item active">Change Password</li>
@endsection

@section('content')
    @include('partials.change_password')
@endsection