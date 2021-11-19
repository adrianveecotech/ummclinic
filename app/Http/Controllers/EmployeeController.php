<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    // Admin
    public function admin_employee_index(Request $request){
        if($request->get('search')){
            $search = $request->get('search');

            $employees = DB::table('employees')
                ->join('companys', 'employees.company_id', 'companys.id')
                ->select(
                    'employees.*',
                    'companys.name as company_name'
                )
                ->where(function ($query) use ($search) {
                    $query
                        ->where('employees.name', 'LIKE', '%'.$search.'%')
                        ->orwhere('employees.ic', 'LIKE', $search)
                        ->orwhere('employees.company_employee_id', 'LIKE', $search)
                        ->orwhere('companys.name', 'LIKE', '%'.$search.'%');
                    })
                ->orderBy('employees.created_at', 'desc')
                ->paginate(10);

            $consultations = Consultation::all();

            return view('admin.employee_details', compact('employees', 'consultations'));
        }        
        
        if($request->get('status')){
            $status = $request->get('status');

            $employees = DB::table('employees')
                ->join('companys', 'employees.company_id', 'companys.id')
                ->select(
                    'employees.*',
                    'companys.name as company_name'
                )
                ->where('employees.status', 'LIKE', $status)
                ->orderBy('employees.created_at', 'desc')
                ->paginate(10);

            $consultations = Consultation::all();

            return view('admin.employee_details', compact('employees', 'consultations'));
        }
        else{
            $employees = DB::table('employees')
                ->join('companys', 'employees.company_id', 'companys.id')
                ->select(
                    'employees.*',
                    'companys.name as company_name'
                )
                ->orderBy('employees.created_at', 'desc')
                ->paginate(10);

            $consultations = Consultation::all();

            return view('admin.employee_details', compact('employees', 'consultations'));
        }
    }

    // Company
    public function index(Request $request){
        if($request->get('search')){
            $search = $request->get('search');

            $employees = DB::table('employees')
                ->where('company_id', Auth::user()->company_id)
                ->where('name', 'LIKE', '%'.$search.'%')
                ->orwhere('ic', 'LIKE', $search)
                ->orwhere('company_employee_id', 'LIKE', $search)
                ->paginate(10);

            return view('company.employee', compact('employees'));
        }
        else if($request->get('status')){
            $status = $request->get('status');

            $employees = DB::table('employees')
                ->where('company_id', Auth::user()->company_id)
                ->where('status', 'LIKE', $status)
                ->paginate(10);

            return view('company.employee', compact('employees'));
        }
        else{
            $employees = Employee::where('company_id', Auth::user()->company_id)
                ->paginate(10);

            return view('company.employee', compact('employees'));
        }
    }

    public function register_index(){
        $company_id = Auth::user()->company_id;

        $company_name = Company::where('id', $company_id)
                ->select('name')
                ->first();

        return view('company.register_employee', compact('company_name'));
    }

    public function store_employee_details(Request $request){
        $request->validate([
            'name' => 'required|max:255',
            'ic' => 'required|unique:employees',
            'address' => 'required',
            'contact' => 'required|max:255',
            'dob' => 'required',
            'company_employee_id' => 'required|unique:employees',
        ]);

        $employees = new Employee();

        $employees->name = Str::upper($request->input('name'));
        $employees->ic = $request->input('ic');
        $employees->address = Str::upper($request->input('address'));
        $employees->contact = $request->input('contact');
        $employees->dob = $request->input('dob');
        $employees->company_employee_id = $request->input('company_employee_id');
        $employees->company_id = $request->input('company_id');

        $employees->save();

        return back()->with('message', 'Employee created successfully.');
    }

    public function update_employee_details(Request $request, $id){
        $employees = Employee::findorfail($id);

        $employees->name = Str::upper($request->input('name'));
        $employees->ic = $request->input('ic');
        $employees->address = Str::upper($request->input('address'));
        $employees->contact = $request->input('contact');
        $employees->dob = $request->input('dob');
        $employees->status = $request->input('status');

        $employees->save();

        return back()->with('message', 'Employee update successfully.');
    }

    public function delete_employee_details($id){
        $delete = Employee::findorfail($id);

        $delete->delete();

        return back()->with('message', 'Clinic Delete Successfully.');
    }
}
