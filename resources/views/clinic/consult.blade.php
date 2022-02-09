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
td { width: 25vw; }

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
                <form id="formConsultation" action="{{ url('clinic/consult/store') }}" method="POST">
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
                                <select class="custom-select mb-3" name="doctor_id" id="doctor_id" onchange="changeDoctor()">
                                    <option selected></option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                    @endforeach
                                  </select>
                            </div>
                            <div class="col">
                                <strong><label for="price">Price</label><span style="color:red">*</span></strong>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text" id="basic-addon1">RM</span>
                                    </div>
                                    <input type="number" class="form-control" name="price" min="1" step="0.01" pattern="\$?(\d)+(\.\d\d)?">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                            <strong><label for="new_doctor">Insert New Doctor</label></strong>
                                <input type="text" class="form-control" id="new_doctor" name="new_doctor" placeholder="Leave empty if not applicable">
                            </div>
                            <div class="col">
                                <strong><label for="doctor_ic">Doctor's IC</label></strong>
                                <input type="text" class="form-control" id="new_doctor_ic" name="new_doctor_ic" placeholder="Only applicable if new doctor name is entered" >
                            </div>
                        </div>
                        <br>
                        <strong><label for="description">Diagnosis</label><span style="color:red">*</span></strong>
                        <textarea id="description" name="description" rows="10" class="form-control"></textarea><br>
                        <strong><label id="medication_details">Mediciation Details</label></strong>
                        <div class="row">
                            <div class="col">
                            <strong><label for="medication_name">Medication Name</label></strong>
                                <input type="text" class="form-control" id="medication_name" name="medication_name">
                            </div>
                            <div class="col">
                                <strong><label for="quantity">Quantity</label></strong>
                                <input type="number" id="quantity" name="quantity"  class="form-control">
                            </div>
                            <div class="col">
                                <strong><label for="unit">Unit</label></strong>
                                <input type="text" class="form-control" id="unit" name="unit">
                            </div>
                            <div class="col">
                                <strong><label for="medication_price">Price</label></strong>
                                <input type="number" id="medication_price" name="medication_price" class="form-control">
                            </div>
                        </div>
                        <button type="button" class="btn btn-success mt-2" id="updateButton" onclick="productUpdate();">Add Medication</button>
                        <br>
                        <div class="row">
                            <div class="col">
                                <br>
                                <table id="medicationTable" class="table table-bordered table-condensed table-striped">
                                    <thead>
                                        <tr>
                                            <th>Edit</th>
                                            <th>Medication Name</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Price</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div>
                        <strong><label>MC</label></strong><br>
                        <div class="row">
                            <div class="col-2">
                                <strong><label for="mc_startdate">Start Date</label></strong>
                                <input type="date" id="mc_startdate" name="mc_startdate" class="form-control">
                            </div>
                            <div class="col-2">
                                <strong><label for="mc_enddate">End Date</label></strong>
                                <input type="date" id="mc_enddate" name="mc_enddate" class="form-control">
                            </div>
                        </div>
                        </div>  
                        <br>
                        <strong><label for="clinic_admin">Admin Name</label><span style="color:red">*</span></strong>
                        <input type="text" class="form-control" id="clinic_admin" name="clinic_admin">
                        <br>
                        <br>
                        <button type="submit" class="btn btn-success">Submit Consultation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var _row = null;
     // Next ID for adding a new Product
    var _nextId = 1;
    
    // ID of Product currently editing
    var _activeId = 0;
    $(document).ready(function () {

    });
    var data = [];
    $("#formConsultation").submit( function(eventObj) {
        var oTable = document.getElementById('medicationTable');

        //gets rows of table
        var rowLength = oTable.rows.length;

        var x = 0;

        //loops through rows    
        for (i = 0; i < rowLength; i++){

        //gets cells of current row  
        var oCells = oTable.rows.item(i).cells;

        //gets amount of cells of current row
        var cellLength = oCells.length;
        
        var med = [];

        //loops through each cell in current row
        for(var j = 0; j < cellLength; j++){
                // get your cell info here
            var cellVal = oCells.item(j).innerHTML;
            if(i != 0 && (j == 1 || j == 2 || j == 3 || j == 4)){
                med.push(cellVal);
            }
        }
        if(med.length != 0)
        {
            data.push(med);
        }
        }
        $("<input />").attr("type", "hidden")
            .attr("name","medications")
            .attr("value", JSON.stringify(data))
            .appendTo(this);

        return true;
    });

    function changeDoctor(){
        if (document.getElementById("doctor_id").value == ""){
            $("#new_doctor").attr("disabled", false);
            $("#new_doctor_ic").attr("disabled", false);
            
        }
        else{
            $("#new_doctor").prop("disabled", true);
            $("#new_doctor_ic").prop("disabled", true);
        }
    }

    function formClear() {
        $("#medication_name").val("");
        $("#quantity").val("");
        $("#unit").val("");
        $("#medication_price").val("");
    }

    function productDisplay(ctl) {
        var row = $(ctl).parents("tr");
        var cols = row.children("td");
        _activeId = $($(cols[0]).children("button")[0]).data("id");
        $("#medication_name").val($(cols[1]).text());
        $("#quantity").val($(cols[2]).text());
        $("#unt").val($(cols[3]).text());
        $("#medication_price").val($(cols[4]).text());
        
        // Change Update Button Text
        $("#updateButton").text("Update");
    }

    function productAddToTable() {
        // First check if a <tbody> tag exists, add one if not
        if ($("#medicationTable tbody").length == 0) {
            $("#medicationTable").append("<tbody></tbody>");
        }

        // Append product to the table
        $("#medicationTable tbody").append(productBuildTableRow(_nextId));

        _nextId += 1;
    }

    function productUpdate() {
        if($("#medication_name").val() == '' || $("#quantity").val() == '' || $("#unit").val() == '' || $("#medication_price").val() == '')
        {
            alert('Please make sure all medication details are filled up.');
            return;
        }
        if ($("#updateButton").text() == "Update") {
            productUpdateInTable(_activeId);
        }
        else {
            productAddToTable();
        }
        
        // Clear form fields
        formClear();
        
        // Focus to product name field
        $("#medication_name").focus();
    }

    function productUpdateInTable(id) {
        // Add changed product to table
        var row = $("#medicationTable button[data-id='" + id + "']").parents("tr")[0];
        // Add changed product to table
        $(row).after(productBuildTableRow(id));
            // Remove original product
        $(row).remove();
        
        // Clear form fields
        formClear();
        
        // Change Update Button Text
        $("#updateButton").text("Add");
    }


    function productBuildTableRow(id) {
        var ret = "<tr>" +
        "<td>" +
        "<button type='button' onclick='productDisplay(this);' class='btn btn-primary' data-id='" + id + "'>" +
        "<i class='fa fa-pencil'></i>" +
        "</button>" +
        "</td>" +
        "<td>" + $("#medication_name").val() + "</td>" +
        "<td>" + $("#quantity").val() + "</td>" +
        "<td>" + $("#unit").val() + "</td>" +
        "<td>" + $("#medication_price").val() + "</td>" +
        "<td>" +
        "<button type='button' onclick='productDelete(this);' class='btn btn-danger' data-id='" + id + "'>" +
        "<i class='fa fa-trash'></i>" +
        "</button>" +
        "</td>" +
        "</tr>"

    return ret;
    }

    function productDelete(ctl) {
        $(ctl).parents("tr").remove();
    }

</script>
@endsection