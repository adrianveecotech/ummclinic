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
        $companies = Company::orderBy('name')->get();
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

        }
        foreach ($employees as $key => $employee) {
            $monthly_spent = Consultation::where('ic',$employee->ic)->whereMonth('created_at',date('m') )->sum('price');
            if($monthly_spent > $employee->monthly_limit)
                $employees[$key]->monthly_limit_exceeded = 'true';
            else
                $employees[$key]->monthly_limit_exceeded = 'false';

            $yearly_spent = Consultation::where('ic',$employee->ic)->whereYear('created_at',date('Y') )->sum('price');
            if($yearly_spent > $employee->yearly_limit)
                $employees[$key]->yearly_limit_exceeded = 'true';
            else
                $employees[$key]->yearly_limit_exceeded = 'false';
        }   
        return view('admin.employee_details', compact('employees', 'consultations','companies'));
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
        }
        else if($request->get('status')){
            $status = $request->get('status');

            $employees = DB::table('employees')
                ->where('company_id', Auth::user()->company_id)
                ->where('status', 'LIKE', $status)
                ->paginate(10);
        }
        else{
            $employees = Employee::where('company_id', Auth::user()->company_id)
                ->paginate(10);
        }
        foreach ($employees as $key => $employee) {
            $monthly_spent = Consultation::where('ic',$employee->ic)->whereMonth('created_at',date('m') )->sum('price');
            if($monthly_spent > $employee->monthly_limit)
                $employees[$key]->monthly_limit_exceeded = 'true';
            else
                $employees[$key]->monthly_limit_exceeded = 'false';

            $yearly_spent = Consultation::where('ic',$employee->ic)->whereYear('created_at',date('Y') )->sum('price');
            if($yearly_spent > $employee->yearly_limit)
                $employees[$key]->yearly_limit_exceeded = 'true';
            else
                $employees[$key]->yearly_limit_exceeded = 'false';
        }   
        return view('company.employee', compact('employees'));
    }

    public function register_index(){
        if(Auth::user()->type == 'Admin')
        {
            $companies = Company::orderBy('name')->get();   
            return view('admin.register_employee', compact('companies'));
        }
        else if(Auth::user()->type == 'company')
        {
            $company_id = Auth::user()->company_id;

            $company_name = Company::where('id', $company_id)
                    ->select('name')
                    ->first();
            return view('company.register_employee', compact('company_name'));
        }else if(Auth::user()->type == 'clinic')
        {
            $companies = Company::orderBy('name')->get();   
            return view('clinic.register_employee', compact('companies'));
        }
    }

    public function store_employee_details(Request $request){
        if(Auth::user()->type == 'Admin' || Auth::user()->type == 'clinic'){
            $request->validate([
                'company' => 'required',
            ]);
        }
        $request->validate([
            'name' => 'required|max:255',
            'ic' => 'required|unique:employees',
            'address' => 'required',
            'contact' => 'required|max:255',
            'dob' => 'required',
            'company_employee_id' => 'required|unique:employees',
            'monthly_limit' => 'required',
            'yearly_limit' => 'required'
        ]);

        $employees = new Employee();

        $employees->name = Str::upper($request->input('name'));
        $employees->ic = $request->input('ic');
        $employees->address = Str::upper($request->input('address'));
        $employees->contact = $request->input('contact');
        $employees->dob = $request->input('dob');
        $employees->company_employee_id = $request->input('company_employee_id');
        $employees->monthly_limit = $request->input('monthly_limit');
        $employees->yearly_limit = $request->input('yearly_limit');
        if(Auth::user()->type == 'Admin' || Auth::user()->type == 'clinic'){
            $employees->company_id = $request->company;
        }
        else if(Auth::user()->type == 'company'){
            $employees->company_id = $request->input('company_id');
        }
        $employees->date_joined = $request->input('date_joined');
        $employees->department = $request->input('department');
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
        $employees->monthly_limit = $request->input('monthly_limit');
        $employees->yearly_limit = $request->input('yearly_limit');
        $employees->date_joined = $request->input('date_joined');
        $employees->department = $request->input('department');

        $employees->save();

        return back()->with('message', 'Employee update successfully.');
    }

    public function delete_employee_details($id){
        $delete = Employee::findorfail($id);

        $delete->delete();

        return back()->with('message', 'Clinic Delete Successfully.');
    }
}
