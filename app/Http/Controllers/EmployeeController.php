<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Models\Consultation;
use App\Models\Dependent;
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
        else if($request->get('status')){
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
            $monthly_spent = Consultation::where('id',$employee->id)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->sum('price');
            if($employee->monthly_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employee->monthly_limit_end_date){
                if($monthly_spent > $employee->monthly_limit)
                    $employees[$key]->monthly_limit_exceeded = 'true';
                else
                    $employees[$key]->monthly_limit_exceeded = 'false';
            }else{
                $employees[$key]->monthly_limit_exceeded = 'Limit not set';
            }
            if($employee->category == 'employee'){
                $employees_all = Employee::where('employee_id',$employee->id)->pluck('id')->toArray();
                $overall_spent = Consultation::whereIn('employee_id',$employees_all)->whereBetween('created_at', [date($employee->overall_limit_start_date), date($employee->overall_limit_end_date)])->sum('price');
                if($employee->overall_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employee->overall_limit_end_date){
                    if($overall_spent > $employee->overall_limit)
                        $employees[$key]->overall_limit_exceeded = 'true';
                    else
                        $employees[$key]->overall_limit_exceeded = 'false';
                }else{
                    $employees[$key]->overall_limit_exceeded = 'Limit not set';
                }
            }else{
                $employee_of_dependent = Employee::where('id',$employee->employee_id)->first();
                $employees_all = Employee::where('employee_id',$employee_of_dependent->id)->pluck('id')->toArray();
                $overall_spent = Consultation::whereIn('employee_id',$employees_all)->whereBetween('created_at', [date($employee_of_dependent->overall_limit_start_date), date($employee_of_dependent->overall_limit_end_date)])->sum('price');
                if($employee_of_dependent->overall_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employee_of_dependent->overall_limit_end_date){
                    if($overall_spent > $employee_of_dependent->overall_limit)
                        $employees[$key]->overall_limit_exceeded = 'true';
                    else
                        $employees[$key]->overall_limit_exceeded = 'false';
                }else{
                    $employees[$key]->overall_limit_exceeded = 'Limit not set';
                }
            }
            
            if($employee->daily_limit){
                $daily_spent = Consultation::where('ic',$employee->ic)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->whereDay('created_at',date('d'))->sum('price');
                if($daily_spent > $employee->daily_limit)
                    $employees[$key]->daily_limit_exceeded = 'true';
                else
                    $employees[$key]->daily_limit_exceeded = 'false';
            }else{
                $employees[$key]->daily_limit_exceeded = 'Limit not set';
            }
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
                ->where('employees.category','=','employee')
                ->paginate(10);
        }
        else if($request->get('status')){
            $status = $request->get('status');

            $employees = DB::table('employees')
                ->where('company_id', Auth::user()->company_id)
                ->where('employees.category','=','employee')
                ->where('status', 'LIKE', $status)
                ->paginate(10);
        }
        else{
            $employees = Employee::where('company_id', Auth::user()->company_id)
                ->where('employees.category','=','employee')
                ->paginate(10);
        }
        foreach ($employees as $key => $employee) {
            $monthly_spent = Consultation::where('id',$employee->id)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->sum('price');
            if($employee->monthly_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employee->monthly_limit_end_date){
                if($monthly_spent > $employee->monthly_limit)
                    $employees[$key]->monthly_limit_exceeded = 'true';
                else
                    $employees[$key]->monthly_limit_exceeded = 'false';
            }else{
                $employees[$key]->monthly_limit_exceeded = 'Limit not set';
            }

            if($employee->category == 'employee'){
                $employees_all = Employee::where('employee_id',$employee->id)->pluck('id')->toArray();
                $overall_spent = Consultation::whereIn('employee_id',$employees_all)->whereBetween('created_at', [date($employee->overall_limit_start_date), date($employee->overall_limit_end_date)])->sum('price');
                if($employee->overall_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employee->overall_limit_end_date){
                    if($overall_spent > $employee->overall_limit)
                        $employees[$key]->overall_limit_exceeded = 'true';
                    else
                        $employees[$key]->overall_limit_exceeded = 'false';
                }else{
                    $employees[$key]->overall_limit_exceeded = 'Limit not set';
                }
            }else{
                $employee_of_dependent = Employee::where('id',$employee->employee_id)->first();
                $employees_all = Employee::where('employee_id',$employee_of_dependent->id)->pluck('id')->toArray();
                $overall_spent = Consultation::whereIn('employee_id',$employees_all)->whereBetween('created_at', [date($employee_of_dependent->overall_limit_start_date), date($employee_of_dependent->overall_limit_end_date)])->sum('price');
                if($employee->overall_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employee->overall_limit_end_date){
                    if($overall_spent > $employee_of_dependent->overall_limit)
                        $employees[$key]->overall_limit_exceeded = 'true';
                    else
                        $employees[$key]->overall_limit_exceeded = 'false';
                }else{
                    $employees[$key]->overall_limit_exceeded = 'Limit not set';
                }
            }
            
            if($employee->daily_limit){
                $daily_spent = Consultation::where('ic',$employee->ic)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->whereDay('created_at',date('d'))->sum('price');
                if($daily_spent > $employee->daily_limit)
                    $employees[$key]->daily_limit_exceeded = 'true';
                else
                    $employees[$key]->daily_limit_exceeded = 'false';
            }else{
                $employees[$key]->daily_limit_exceeded = 'Limit not set';
            }
            
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
            'date_joined' => 'required',
            'department' => 'required',
            'monthly_limit' => 'required',
            'monthly_limit_start_date' => 'required',
            'monthly_limit_end_date' => 'required',
            'overall_limit' => 'required',
            'overall_limit_start_date' => 'required',
            'overall_limit_end_date' => 'required',
            'daily_limit' => 'required',
        ]);

        $employees = new Employee();

        $employees->name = Str::upper($request->input('name'));
        $employees->ic = $request->input('ic');
        $employees->address = Str::upper($request->input('address'));
        $employees->contact = $request->input('contact');
        $employees->dob = $request->input('dob');
        $employees->company_employee_id = $request->input('company_employee_id');
        if(Auth::user()->type == 'Admin' || Auth::user()->type == 'clinic'){
            $employees->company_id = $request->company;
        }
        else if(Auth::user()->type == 'company'){
            $employees->company_id = $request->input('company_id');
        }
        // if($request->monthly_limit_distributed == 'true')
        //     $employees->monthly_limit_distributed = 1;
        // else
        //     $employees->monthly_limit_distributed = 0;
        $employees->date_joined = $request->input('date_joined');
        $employees->department = $request->input('department');
        $employees->monthly_limit = $request->input('monthly_limit');
        $employees->monthly_limit_start_date = $request->input('monthly_limit_start_date');
        $employees->monthly_limit_end_date = $request->input('monthly_limit_end_date');
        $employees->overall_limit = $request->input('overall_limit');
        $employees->overall_limit_start_date = $request->input('overall_limit_start_date');
        $employees->overall_limit_end_date = $request->input('overall_limit_end_date');
        $employees->daily_limit = $request->input('daily_limit');
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
        $employees->overall_limit = $request->input('overall_limit');
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
