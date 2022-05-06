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

class DependentController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->type == 'company') {
            if ($request->get('search')) {
                $search = $request->get('search');

                $dependents = DB::table('employees')
                    ->join('employees AS e1', 'e1.id', 'employees.employee_id')
                    ->select(
                        'employees.*',
                        'e1.name as employee_name'
                    )
                    ->where('employees.company_id', Auth::user()->company_id)
                    ->where('employees.category','!=','employee')
                    ->where(function ($query) use ($search) {
                        $query
                            ->where('employees.name', 'LIKE', '%' . $search . '%')
                            ->orwhere('employees.ic', 'LIKE', $search);
                    })
                    ->orderBy('employees.created_at', 'desc')
                    ->paginate(10);

                $consultations = Consultation::all();
            } else if ($request->get('status')) {
                $status = $request->get('status');

                $dependents = DB::table('employees')
                    ->join('employees AS e1', 'e1.id', 'employees.employee_id')
                    ->select(
                        'employees.*',
                        'e1.name as employee_name'
                    )
                    ->where('employees.company_id', Auth::user()->company_id)
                    ->where('employees.category','!=','employee')
                    ->where('employees.status', 'LIKE', $status)
                    ->orderBy('employees.created_at', 'desc')
                    ->paginate(10);

                $consultations = Consultation::all();
            } else {
                $dependents = DB::table('employees')
                    ->join('employees AS e1', 'e1.id', 'employees.employee_id')
                    ->select(
                        'employees.*',
                        'e1.name as employee_name'
                    )
                    ->where('employees.company_id', Auth::user()->company_id)
                    ->where('employees.category','!=','employee')
                    ->orderBy('employees.created_at', 'desc')
                    ->paginate(10);
                $consultations = Consultation::all();
            }
        } else {
            if ($request->get('search')) {
                $search = $request->get('search');

                $dependents = DB::table('employees')
                    ->join('employees AS e1', 'e1.id', 'employees.employee_id')
                    ->select(
                        'employees.*',
                        'e1.name as employee_name'
                    )
                    ->where('employees.category','!=','employee')
                    ->where(function ($query) use ($search) {
                        $query
                            ->where('employees.name', 'LIKE', '%' . $search . '%')
                            ->orwhere('employees.ic', 'LIKE', $search);
                    })
                    ->orderBy('employees.created_at', 'desc')
                    ->paginate(10);

                $consultations = Consultation::all();
            } else if ($request->get('status')) {
                $status = $request->get('status');

                $dependents = DB::table('employees')
                    ->join('employees AS e1', 'e1.id', 'employees.employee_id')
                    ->select(
                        'employees.*',
                        'e1.name as employee_name'
                    )
                    ->where('employees.category','!=','employee')
                    ->where('employees.status', 'LIKE', $status)
                    ->orderBy('employees.created_at', 'desc')
                    ->paginate(10);

                $consultations = Consultation::all();
            } else {
                $dependents = DB::table('employees')
                    ->join('employees AS e1', 'e1.id', 'employees.employee_id')
                    ->select(
                        'employees.*',
                        'e1.name as employee_name'
                    )
                    ->where('employees.category','!=','employee')
                    ->orderBy('employees.created_at', 'desc')
                    ->paginate(10);
                $consultations = Consultation::all();
            }
        }
        foreach ($dependents as $key => $dependent) {
            $monthly_spent = Consultation::where('id',$dependent->id)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->sum('price');
            if($dependent->monthly_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $dependent->monthly_limit_end_date){
                if($monthly_spent > $dependent->monthly_limit)
                    $dependents[$key]->monthly_limit_exceeded = 'true';
                else
                    $dependents[$key]->monthly_limit_exceeded = 'false';
            }else{
                $dependents[$key]->monthly_limit_exceeded = 'Limit not set';
            }

            $employee_of_dependent = Employee::where('id',$dependent->employee_id)->first();
                $employees_all = Employee::where('employee_id',$employee_of_dependent->id)->pluck('id')->toArray();
                $overall_spent = Consultation::whereIn('employee_id',$employees_all)->whereBetween('created_at', [date($employee_of_dependent->overall_limit_start_date), date($employee_of_dependent->overall_limit_end_date)])->sum('price');
                if($overall_spent > $employee_of_dependent->overall_limit)
                    $dependents[$key]->overall_limit_exceeded = 'true';
                else
                    $dependents[$key]->overall_limit_exceeded = 'false';

                if($dependent->daily_limit){
                    $daily_spent = Consultation::where('ic',$dependent->ic)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->whereDay('created_at',date('d'))->sum('price');
                    if($daily_spent > $dependent->daily_limit)
                        $dependents[$key]->daily_limit_exceeded = 'true';
                    else
                        $dependents[$key]->daily_limit_exceeded = 'false';
                }else{
                    $dependents[$key]->daily_limit_exceeded = 'Limit not set';
                }
        }
        if (Auth::user()->type == 'Admin') {
            return view('admin.dependent_details', compact('dependents', 'consultations'));
        } else if (Auth::user()->type == 'clinic') {
            return view('clinic.dependent_details', compact('dependents', 'consultations'));
        } else if (Auth::user()->type == 'company') {
            return view('company.dependent_details', compact('dependents', 'consultations'));
        }
    }

    public function register_index()
    {
        $employee_id = request()->get('employee');
        $employee = Employee::where('id', $employee_id)->first();
        $employee_name = $employee->name;
        
        if (Auth::user()->type == 'Admin') {
            $companies = Company::orderBy('name')->get();
            return view('admin.register_dependent', compact('employee_name'));
        } else if (Auth::user()->type == 'company') {
            $companies = Company::orderBy('name')->get();
            return view('company.register_dependent', compact('employee_name'));
        } else if (Auth::user()->type == 'clinic') {
            $companies = Company::orderBy('name')->get();
            return view('clinic.register_dependent', compact('employee_name'));
        }
    }

    public function store_dependent_details(Request $request)
    {
        $request->validate([
            'employee' => 'required',
            'name' => 'required',
            'ic' => 'required|unique:dependents',
            'address' => 'required',
            'contact' => 'required',
            'dob' => 'required',
            'category' => 'required',
        ]);

        $dependents = new Employee();

        $dependents->employee_id = $request->get('employee');
        $employee = Employee::find($dependents->employee_id);
        $dependents->name = Str::upper($request->input('name'));
        $dependents->ic = $request->input('ic');
        $dependents->address = Str::upper($request->input('address'));
        $dependents->contact = $request->input('contact');
        $dependents->dob = $request->input('dob');
        $dependents->category = $request->input('category');
        $dependents->monthly_limit_start_date = $request->input('monthly_limit_start_date');
        $dependents->monthly_limit_end_date = $request->input('monthly_limit_end_date');
        $dependents->monthly_limit = $request->input('monthly_limit');
        $dependents->daily_limit = $request->input('daily_limit');
        $dependents->company_id = $employee->company_id;
        $dependents->save();
        return back()->with('message', 'Dependent created successfully.');
    }

    public function update_employee_details(Request $request, $id)
    {
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

    public function delete_employee_details($id)
    {
        $delete = Employee::findorfail($id);

        $delete->delete();

        return back()->with('message', 'Clinic Delete Successfully.');
    }
}
