<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ConsultationController extends Controller
{
    //admin
    public function admin_consultation_index(Request $request){
        if($request->get('search')){
            $search = $request->get('search');

            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status'
                    )
                ->where(function ($query) use ($search) {
                    $query
                        ->where('employees.name', 'LIKE', '%'.$search.'%')
                        ->orwhere('employees.ic', 'LIKE', $search)
                        ->orwhere('clinics.name', 'LIKE', '%'.$search.'%');
                    })
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

            return view('admin.consultation_history', compact('consultations'));
        }
        else if($request->get('status')){
            $status = $request->get('status');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status'
                );

                if($start_date && $end_date){
                    $consultations = $consultations
                        ->whereDate('consultations.created_at', '>=', $start_date)
                        ->whereDate('consultations.created_at', '<=', $end_date);
                }             
                if($status){
                    $consultations = $consultations->where('payments.status', 'LIKE', $status);
                }    
    
                $consultations = $consultations
                    ->orderBy('consultations.created_at', 'desc')
                    ->paginate(10);

            return view('admin.consultation_history', compact('consultations'));
        }

        else if($request->get('start_date') && $request->get('end_date')){
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status'
                )
                    ->whereDate('consultations.created_at', '>=', $start_date)
                    ->whereDate('consultations.created_at', '<=', $end_date)
                    ->orderBy('consultations.created_at', 'desc')
                    ->paginate(10);

            return view('admin.consultation_history', compact('consultations'));
        }

        else{
            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status'
                    )
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

        return view('admin.consultation_history', compact('consultations'));
        }    
    }

    // clinic
    public function index($ic){
        $employees = DB::table('employees')
            ->join('companys', 'company_id', 'companys.id')
            ->select('companys.name as company_name', 'employees.*')
            ->where('employees.ic', 'LIKE', $ic)
            ->get();

        $doctors = DB::table('doctors')
            ->where('clinic_id', Auth::user()->clinic_id)
            ->where('status', 'active')
            ->get();

        return view('clinic.consult', compact('employees', 'doctors'));
    }

    public function search_patient(Request $request){
        $query = $request->get('query');
        if($query){
            $employees = DB::table('employees')
                ->join('companys', 'company_id', 'companys.id')
                ->select('companys.name as company_name', 'employees.*')
                ->where('employees.ic', 'LIKE', $query)
                ->orwhere('employees.company_employee_id', 'LIKE', $query)
                ->get();

            $status = [];
            foreach($employees as $employee){
                $status = $employee->status;
            }

            $consultations = DB::table('consultations')
                    ->join('doctors', 'doctor_id', 'doctors.id')
                    ->select('doctors.name as doctor_name', 'consultations.*')
                    ->where('consultations.ic', $query)
                    ->orwhere('consultations.company_employee_id', $query)
                    ->where('consultations.clinic_id', Auth::user()->clinic_id)
                    ->orderBy('consultations.created_at', 'desc')
                    ->paginate(5);

            $consultation_details = DB::table('consultations')
                    ->join('employees', 'consultations.ic', 'employees.ic')
                    ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                    ->select('employees.name as employee_name', 'employees.ic as employee_ic', 'doctors.name as doctor_name', 'consultations.*')
                    ->where('consultations.ic', $query)
                    ->orwhere('consultations.company_employee_id', $query)
                    ->orderBy('consultations.created_at', 'desc')
                    ->paginate(5);

            return view('clinic.search_patient', compact('employees', 'status', 'consultations', 'consultation_details'));
        }
        else{            
            $employees = [];
            $status = [];

            return view('clinic.search_patient', compact('employees',  'status'));
        }
    }

    public function consultation_history(Request $request){
        if($request->get('search')){
            $search = $request->get('search');

            $consultation_details = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('companys', 'consultations.company_id', 'companys.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->select(
                    'employees.name as employee_name', 
                    'employees.ic as employee_ic', 
                    'companys.name as company_name',
                    'doctors.name as doctor_name', 
                    'consultations.*'
                    )
                ->where(function ($query) use ($search) {
                    $query
                        ->orwhere('employees.ic', 'LIKE', $search)
                        ->orwhere('companys.name', 'LIKE', '%'.$search.'%');
                    })
                ->where('consultations.clinic_id', Auth::user()->clinic_id)
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

            return view('clinic.consultation_history', compact('consultation_details'));
        }
        else if($request->get('start_date') && $request->get('end_date')){
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $consultation_details = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('companys', 'consultations.company_id', 'companys.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->select(
                    'employees.name as employee_name', 
                    'employees.ic as employee_ic', 
                    'companys.name as company_name',
                    'doctors.name as doctor_name', 
                    'consultations.*'
                    )
                ->whereDate('consultations.created_at', '>=', $start_date)
                ->whereDate('consultations.created_at', '<=', $end_date)
                ->where('consultations.clinic_id', Auth::user()->clinic_id)
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

            return view('clinic.consultation_history', compact('consultation_details'));
        }
        else{
            $consultation_details = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('companys', 'consultations.company_id', 'companys.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->select(
                    'employees.name as employee_name', 
                    'employees.ic as employee_ic', 
                    'companys.name as company_name',
                    'doctors.name as doctor_name', 
                    'consultations.*'
                    )
                ->where('consultations.clinic_id', Auth::user()->clinic_id)
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

            return view('clinic.consultation_history', compact('consultation_details'));
        }
    }

    public function store(Request $request){
        $request->validate([
            'ic' => 'required',
            'company_employee_id' => 'required',
            'company_id' => 'required',
            'clinic_id' => 'required',
            'description' => 'required',
            'price' => 'required',
        ]);

        $ic = $request->input('ic');
        $company_employee_id = $request->input('company_employee_id');

        $company_id = $request->input('company_id');
        
        $consultations = new Consultation();

        $consultations->description = $request->input('description');
        $consultations->price = number_format((float)($request->input('price')), 2);
        $consultations->ic = $ic;
        $consultations->company_employee_id = $company_employee_id;
        $consultations->company_id = $company_id;
        $consultations->clinic_id = $request->input('clinic_id');
        $consultations->doctor_id = $request->input('doctor_id');

        $consultations->save();

        $count_consultations = Consultation::all();
        $rows_consultations = count($count_consultations);

        $payments = new Payment();

        $payments->ic = $ic;
        $payments->amount = number_format((float)($request->input('price')), 2);
        $payments->clinic_id = Auth::user()->clinic_id;
        $payments->company_id = $company_id;
        $payments->consultation_id = $rows_consultations;

        $payments->save();

        return redirect('/clinic/search?query='.$ic)->with('message', 'Consultation Details Stored.');
    }

    // company
    public function employee_consultation_report_index(Request $request){
        if($request->get('search')){
            $search = $request->get('search');

            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status'
                    )
                ->where('consultations.company_id', Auth::user()->company_id)
                ->where(function ($query) use ($search) {
                    $query
                        ->where('employees.name', 'LIKE', '%'.$search.'%')
                        ->orwhere('employees.ic', 'LIKE', $search)
                        ->orwhere('clinics.name', 'LIKE', '%'.$search.'%');
                    })
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

            return view('company.consultation_report', compact('consultations'));
        }
        else if($request->get('status')){
            $status = $request->get('status');
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status'
                );
                if($start_date && $end_date){
                    $consultations = $consultations
                        ->whereDate('consultations.created_at', '>=', $start_date)
                        ->whereDate('consultations.created_at', '<=', $end_date);
                }             
                if($status){
                    $consultations = $consultations->where('payments.status', 'LIKE', $status);
                }
                
                $consultations = $consultations
                    ->where('consultations.company_id', Auth::user()->company_id)
                    ->where('payments.status', 'LIKE', $status)
                    ->orderBy('consultations.created_at', 'desc')
                    ->paginate(10);

            return view('company.consultation_report', compact('consultations'));
        }
        else if($request->get('start_date') && $request->get('end_date')){
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status'
                    )
                ->whereDate('consultations.created_at', '>=', $start_date)
                ->whereDate('consultations.created_at', '<=', $end_date)
                ->where('consultations.company_id', Auth::user()->company_id)
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

            return view('company.consultation_report', compact('consultations'));
        }
        else{
            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status'
                    )
                ->where('consultations.company_id', Auth::user()->company_id)
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

        return view('company.consultation_report', compact('consultations'));
        }
    }
}
