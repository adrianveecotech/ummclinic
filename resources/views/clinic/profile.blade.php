@extends('layouts.clinic')

@section('pageTitle', 'Profile')

@section('sub_header')
  <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
  <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
@include('partials.profile')
@endsection