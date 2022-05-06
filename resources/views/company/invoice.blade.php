@extends('layouts.company')

@section('pageTitle', 'Invoice')

@section('styles')
<style>
  .badge {
    font-size: 0.9rem;
  }
  #search{
        width: 250px !important;
    }
#amount_card{
    font-size: 16px !important;
    border: none;
    background-color: #e8e8e7;
}
.modal-header, .modal-title{
    font-size: 16px !important;
}
</style>
@endsection

@section('sub_header')
<li class="breadcrumb-item"><a href="/">Dashboard</a></li>
<li class="breadcrumb-item active">Invoice</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
          <div class="float-left mb-3">
            <form action="{{ url('company/invoice') }}" method="GET">
              <input type="text" name="search" value="{{ request()->get('search') ? request()->get('search') : ''}}" id="search" class="form-control" placeholder="Search Company Name or Year Month">
            </form>
          </div>
          <table class="table table-bordered table-hover bg-white" id="consultation_table">
            <thead>
              <tr>
                <th scope="col">Company</th>
                <th scope="col" style="width: 12%">Date</th>
                <th scope="col" style="width: 13%">Uploaded At</th>
                <th scope="col" style="width: 1%">File</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoices as $index => $invoice)
                <tr>
                  <td>{{ $invoice->company->name }}</td>
                  <td>{{ $invoice->date }}</td>
                  <td >{{ $invoice->created_at }}</td>
                  <td class="text-center"><a class="btn btn-info" href="{{url('files/invoice/'. $invoice->file)}}">Download</a></td>
                </tr>                 
              @endforeach
            </tbody>
        </table>
        {{ $invoices->appends(request()->input())->links() }}
        </div>
      </div>
</div>
@endsection