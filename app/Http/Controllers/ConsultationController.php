<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\ConsultationMedication;
use App\Models\Doctor;
use App\Models\Employee;
use App\Models\MedicationConsultation;
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
                ->join('users', 'users.id', 'consultations.clinic_admin')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status',
                    'users.name as clinic_admin',
                    'consultations.id as consultation_id'
                    )
                ->where(function ($query) use ($search) {
                    $query
                        ->where('employees.name', 'LIKE', '%'.$search.'%')
                        ->orwhere('employees.ic', 'LIKE', $search)
                        ->orwhere('clinics.name', 'LIKE', '%'.$search.'%');
                    })
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);
                $clinics = Clinic::orderBy('name')->get();
        }
        else if($request->get('start_date') && $request->get('end_date')){
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $clinic_id = $request->get('clinic');

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
                    'payments.status as payment_status',
                    'consultations.clinic_admin as clinic_admin'
                )
                ->whereDate('consultations.created_at', '>=', $start_date)
                ->whereDate('consultations.created_at', '<=', $end_date);
            if($clinic_id){
                $consultations = $consultations->where('clinics.id', '=', $clinic_id);
            }
            $consultations = $consultations->orderBy('consultations.created_at', 'desc')->paginate(10);
            $clinics = Clinic::orderBy('name')->get();

        }
        else if($request->get('clinic')){
            $clinic_id = $request->get('clinic');

            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->join('users', 'users.id', 'consultations.clinic_admin')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status',
                    'users.name as clinic_admin'
                )
                    ->where('clinics.id', '=', $clinic_id)
                    ->orderBy('consultations.created_at', 'desc')
                    ->paginate(10);
            $clinics = Clinic::orderBy('name')->get();

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
                    'payments.status as payment_status',
                    )
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);
            $clinics = Clinic::orderBy('name')->get();

        }    
        $medications = MedicationConsultation::get();
        return view('admin.consultation_history', compact('consultations','clinics','medications'));
    }

    // clinic
    public function index($ic){
        $employees = DB::table('employees')
            ->join('companys', 'company_id', 'companys.id')
            ->join('employees as e1','employees.employee_id','e1.id')
            ->select('companys.name as company_name', 'employees.*','e1.name as employee_name','e1.company_employee_id as employee_company_employee_id','employees.id as employee_id')
            ->where('employees.ic', 'LIKE', $ic)
            ->get();
        // if($employees->count() == 0){
        //     $employees = DB::table('dependents')
        //     ->join('employees', 'employee_id', 'employees.id')
        //     ->join('companys', 'employees.company_id', 'companys.id')
        //     ->select('companys.name as company_name', 'employees.*' , 'employees.name as employee_name' , 'dependents.*')
        //     ->where('dependents.ic', 'LIKE', $ic)
        //     ->get();
        // }
        $doctors = DB::table('doctors')
            ->where('clinic_id', Auth::user()->clinic_id)
            ->where('status', 'active')
            ->get();

        return view('clinic.consult', compact('employees', 'doctors'));
    }

    public function search_patient(Request $request){
        $employees = [];
        $status = [];
        $daily_limit_exceeded = '';
        $monthly_limit_exceeded = '';
        $overall_limit_exceeded = '';
        $query = $request->get('query');
        if($query){
            $employees = DB::table('employees')
                ->join('companys', 'company_id', 'companys.id')
                ->select('companys.name as company_name', 'employees.*')
                ->where('employees.ic', 'LIKE', $query)
                ->orwhere('employees.company_employee_id', 'LIKE', $query)
                ->first();
            if(($employees) == null){
                return view('clinic.search_patient', compact('employees',  'status'));
            }
            if($employees->category == 'dependent' || $employees->category == 'spouse'){
                $status = $employees->status;
                $consultations = DB::table('consultations')
                    ->join('doctors', 'doctor_id', 'doctors.id')
                    ->select('doctors.name as doctor_name', 'consultations.*')
                    ->where('consultations.ic', $query)
                    ->where('consultations.clinic_id', Auth::user()->clinic_id)
                    ->orderBy('consultations.created_at', 'desc');
                $number_of_mc = Consultation::whereNotNull('mc_startdate')->where('ic',$query)->where('consultations.clinic_id', Auth::user()->clinic_id)->get()->count();
                $consultations = $consultations->paginate(5);
                
                $employee_of_dependent = Employee::where('id',$employees->employee_id)->first();
                $employees_all = Employee::where('employee_id',$employee_of_dependent->id)->pluck('id')->toArray();
                $overall_spent = Consultation::whereIn('employee_id',$employees_all)->whereBetween('created_at', [date($employee_of_dependent->overall_limit_start_date), date($employee_of_dependent->overall_limit_end_date)])->sum('price');
                if($employee_of_dependent->overall_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employee_of_dependent->overall_limit_end_date){
                    if($overall_spent > $employee_of_dependent->overall_limit)
                        $overall_limit_exceeded = 'true';
                    else
                        $overall_limit_exceeded = 'false';
                }else{
                    $overall_limit_exceeded = 'Limit not set';
                }

                $monthly_spent = Consultation::where('id',$employees->id)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->sum('price');
                if($employees->monthly_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employees->monthly_limit_end_date){
                    if($monthly_spent > $employees->monthly_limit)
                        $monthly_limit_exceeded = 'true';
                    else
                        $monthly_limit_exceeded = 'false';
                }else{
                    $monthly_limit_exceeded = 'Limit not set';
                }

                if($employees->daily_limit){
                    $daily_spent = Consultation::where('ic',$employees->ic)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->whereDay('created_at',date('d'))->sum('price');
                    if($daily_spent > $employees->daily_limit)
                        $daily_limit_exceeded = 'true';
                    else
                        $daily_limit_exceeded = 'false';
                }else{
                    $daily_limit_exceeded = 'Limit not set';
                }
                $dependents = $employees;
                $consultation_details = DB::table('consultations')
                        ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                        ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                        ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                        ->select('doctors.name as doctor_name', 'consultations.*','clinics.name as clinic_name','consultations.clinic_admin as clinic_admin',
                        DB::raw('group_concat(medications_consultation.name) as medications_name')
                    )
                        ->where('consultations.ic', $query)
                        ->orderBy('consultations.created_at', 'desc')
                        ->groupBy('consultations.id')
                        ->paginate(5);
                return view('clinic.search_dependent', compact('dependents','status', 'consultations', 'consultation_details','number_of_mc','overall_limit_exceeded','employee_of_dependent','overall_spent','dependents','daily_limit_exceeded','monthly_limit_exceeded','monthly_spent','daily_spent'));
            }else{
                $status = $employees->status;
                $consultations = DB::table('consultations')
                        ->join('doctors', 'doctor_id', 'doctors.id')
                        ->select('doctors.name as doctor_name', 'consultations.*')
                        ->where('consultations.ic', $query)
                        ->where('consultations.clinic_id', Auth::user()->clinic_id)
                        ->orwhere('consultations.company_employee_id', $query)
                        ->orderBy('consultations.created_at', 'desc');
                $number_of_mc = Consultation::whereNotNull('mc_startdate')->where('ic',$query)->orWhere('company_employee_id', $query)->where('consultations.clinic_id', Auth::user()->clinic_id)->get()->count();
                $consultations = $consultations->paginate(5);

                $daily_spent = Consultation::where('ic',$query)->whereDate('created_at', date('Y-m-d'))->sum('price');
                $daily_limit_exceeded = 'false';

                // $monthly_spent = Consultation::where('ic',$query)->whereBetween('created_at', [$employees->monthly_limit_start_date,$employees->monthly_limit_end_date ])->sum('price');
                $monthly_spent = Consultation::where('ic',$query)->whereYear('created_at',date('Y') )->whereMonth('created_at',date('m') )->sum('price');
                $monthly_limit_exceeded = 'false';

                $dependents_of_employee = Employee::where('employee_id',$employees->id)->where('category','!=','employee')->get()->toArray();
                // $ics = array_merge(array_column($dependents_of_employee,'ic'),array($employees->ic));
                // $overall_spent = Consultation::whereIn('ic', $ics)->whereBetween('created_at', [$employees->overall_limit_start_date,$employees->overall_limit_end_date ])->sum('price');
                $overall_limit_exceeded = 'false';
                $employees_all = Employee::where('employee_id',$employees->id)->pluck('id')->toArray();
                $overall_spent = Consultation::whereIn('employee_id',$employees_all)->whereBetween('created_at', [date($employees->overall_limit_start_date), date($employees->overall_limit_end_date)])->sum('price');
                if(($employees) != null){
                    // if($monthly_spent > $employees->monthly_limit){
                    //     $monthly_limit_exceeded = 'true';
                    // };
                    // if($overall_spent > $employees->overall_limit){
                    //     $overall_limit_exceeded = 'true';
                    // }
                    // if($daily_spent > $employees->daily_limit){
                    //     $daily_limit_exceeded = 'true';
                    // }
                    if($employees->monthly_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employees->monthly_limit_end_date){
                        if($monthly_spent > $employees->monthly_limit)
                            $monthly_limit_exceeded = 'true';
                        else
                            $monthly_limit_exceeded = 'false';
                    }else{
                        $monthly_limit_exceeded = 'Limit not set';
                    }
                    if($employees->overall_limit_start_date <= date('Y-m-d') && date('Y-m-d') <= $employees->overall_limit_end_date){
                        if($overall_spent > $employees->overall_limit)
                            $overall_limit_exceeded = 'true';
                        else
                            $overall_limit_exceeded = 'false';
                    }else{
                        $overall_limit_exceeded = 'Limit not set';
                    }
                    if($employees->daily_limit){
                        if($daily_spent > $employees->daily_limit)
                            $daily_limit_exceeded = 'true';
                        else
                            $daily_limit_exceeded = 'false';
                    }else{
                        $daily_limit_exceeded = 'Limit not set';
                    }
                }
                $consultation_details = DB::table('consultations')
                        ->join('employees', 'consultations.ic', 'employees.ic')
                        ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                        ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                        ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                        ->select('employees.name as employee_name', 'employees.ic as employee_ic', 'doctors.name as doctor_name', 'consultations.*','clinics.name as clinic_name',
                        DB::raw('group_concat(medications_consultation.name) as medications_name')
                        )
                        ->where('consultations.ic', $query)
                        ->orwhere('consultations.company_employee_id', $query)
                        ->groupBy('consultations.id')
                        ->orderBy('consultations.created_at', 'desc')
                        ->paginate(5);

                return view('clinic.search_patient', compact('employees', 'status', 'consultations', 'consultation_details','number_of_mc','monthly_limit_exceeded','overall_limit_exceeded','daily_limit_exceeded','dependents_of_employee','daily_spent','overall_spent','monthly_spent'));
            }
        }
        else{            
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
                ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                ->select(
                    'employees.name as employee_name', 
                    'employees.ic as employee_ic', 
                    'companys.name as company_name',
                    'doctors.name as doctor_name', 
                    'consultations.*',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                    )
                ->where(function ($query) use ($search) {
                    $query
                        ->orwhere('employees.ic', 'LIKE', $search)
                        ->orwhere('companys.name', 'LIKE', '%'.$search.'%');
                    })
                ->where('consultations.clinic_id', Auth::user()->clinic_id)
                ->groupBy('consultations.id')
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
                ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                ->select(
                    'employees.name as employee_name', 
                    'employees.ic as employee_ic', 
                    'companys.name as company_name',
                    'doctors.name as doctor_name', 
                    'consultations.*',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                    )
                ->whereDate('consultations.created_at', '>=', $start_date)
                ->whereDate('consultations.created_at', '<=', $end_date)
                ->where('consultations.clinic_id', Auth::user()->clinic_id)
                ->groupBy('consultations.id')
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

            return view('clinic.consultation_history', compact('consultation_details'));
        }
        else{
            $consultation_details = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('companys', 'consultations.company_id', 'companys.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                ->select(
                    'employees.name as employee_name', 
                    'employees.ic as employee_ic', 
                    'companys.name as company_name',
                    'doctors.name as doctor_name', 
                    'consultations.*',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                    )
                ->where('consultations.clinic_id', Auth::user()->clinic_id)
                ->groupBy('consultations.id')
                ->orderBy('consultations.created_at', 'desc')
                ->paginate(10);

            return view('clinic.consultation_history', compact('consultation_details'));
        }
    }

    public function store(Request $request){
        $request->validate([
            'ic' => 'required',
            'company_id' => 'required',
            'clinic_id' => 'required',
            'price' => 'required',
            'description' => 'required',
            'clinic_admin' => 'required',
            'doctor_id' => 'required_without:new_doctor',
            'new_doctor' => 'required_without:doctor_id',
            'new_doctor_ic' => 'required_with:new_doctor'
        ],
        [
            'doctor_id.required_without' => 'Please select a doctor or create a new doctor by entering name.',
            'new_doctor.required_without' => '',
            'new_doctor_ic.required_with' => "The new doctor's IC is required."
        ]);
        $doctor = '';
        if($request->new_doctor){
            $doctor = new Doctor();
            $doctor->name = $request->new_doctor;
            $doctor->ic = $request->new_doctor_ic;
            $doctor->clinic_id = Auth::user()->clinic_id;
            $doctor->save();
        }
        $ic = $request->input('ic');
        $company_employee_id = $request->input('company_employee_id');

        $company_id = $request->input('company_id');
        
        $consultations = new Consultation();

        $consultations->price = number_format((float)($request->input('price')), 2);
        $consultations->ic = $ic;
        $consultations->company_employee_id = $company_employee_id;
        $consultations->company_id = $company_id;
        $consultations->clinic_id = $request->input('clinic_id');
        if($request->input('doctor_id'))
            $consultations->doctor_id = $request->input('doctor_id');
        elseif ($request->input('new_doctor'))
            $consultations->doctor_id = $doctor->id;
        $consultations->clinic_admin = $request->clinic_admin;
        $consultations->mc_startdate = $request->mc_startdate;
        $consultations->mc_enddate = $request->mc_enddate;
        $consultations->description = $request->description;
        $consultations->employee_id = $request->employee_id;

        $consultations->save();

        $medicationsInput = json_decode($request->medications);
        foreach ($medicationsInput as $medication) {
            $new_medication = new MedicationConsultation();
            $new_medication->consultation_id = $consultations->id;
            $new_medication->name = $medication[0];
            $new_medication->quantity = $medication[1];
            $new_medication->unit = $medication[2];
            $new_medication->price = $medication[3];
            $new_medication->save();
        }

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
                ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                    )
                ->where('consultations.company_id', Auth::user()->company_id)
                ->where(function ($query) use ($search) {
                    $query
                        ->where('employees.name', 'LIKE', '%'.$search.'%')
                        ->orwhere('employees.ic', 'LIKE', $search)
                        ->orwhere('clinics.name', 'LIKE', '%'.$search.'%');
                    })
                ->groupBy('consultations.id')
                ->orderBy('consultations.created_at', 'desc');

            $allConsultations = $consultations->get();
            $consultations = $consultations->paginate(10);

            return view('company.consultation_report', compact('consultations','allConsultations'));
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
                ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                )
                ->groupBy('consultations.id');

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
                    ->orderBy('consultations.created_at', 'desc');

                $allConsultations = $consultations->get();
                $consultations = $consultations->paginate(10);

            return view('company.consultation_report', compact('consultations','allConsultations'));
        }
        else if($request->get('start_date') && $request->get('end_date')){
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                    )
                ->whereDate('consultations.created_at', '>=', $start_date)
                ->whereDate('consultations.created_at', '<=', $end_date)
                ->where('consultations.company_id', Auth::user()->company_id)
                ->groupBy('consultations.id')
                ->orderBy('consultations.created_at', 'desc');

            $allConsultations = $consultations->get();
            $consultations = $consultations->paginate(10);

            return view('company.consultation_report', compact('consultations','allConsultations'));
        }
        else{
            $consultations = DB::table('consultations')
                ->join('employees', 'consultations.ic', 'employees.ic')
                ->join('clinics', 'consultations.clinic_id', 'clinics.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('payments', 'consultations.id', 'payments.consultation_id')
                ->join('medications_consultation', 'medications_consultation.consultation_id', 'consultations.id')
                ->select(
                    'employees.name as employee_name',
                    'clinics.name as clinic_name',
                    'consultations.*',
                    'doctors.name as doctor_name',
                    'payments.status as payment_status',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                    )
                ->where('consultations.company_id', Auth::user()->company_id) 
                ->groupBy('consultations.id')
                ->orderBy('consultations.created_at', 'desc');

            $allConsultations = $consultations->get();
            $consultations = $consultations->paginate(10);

        return view('company.consultation_report', compact('consultations','allConsultations'));
        }
    }

    public function mc_index(Request $request){
        if($request->get('month')){
            $date = $request->month;
            $date = explode('-',$date);
            $year = $date[0];
            $month = $date[1];
            $consultations = DB::table('consultations')
            ->join('employees', 'consultations.ic', 'employees.ic')
            ->join('clinics', 'clinics.id', 'consultations.clinic_id')
            ->select(
                'employees.name as employee_name',
                'employees.ic as employee_ic',
                'clinics.name as clinic_name',
                'consultations.*',
                DB::raw('count(consultations.id) as total_mc'),
                )
            ->where('consultations.company_id', Auth::user()->company_id)
            ->where('consultations.mc_startdate', '!=',null)
            ->where('consultations.mc_enddate', '!=',null)
            ->where(function($query) use($month,$year) {
                $query->whereMonth('consultations.mc_startdate',$month)->whereYear('consultations.mc_startdate',$year)
                ->orWhere(function($query1) use($month,$year) {
                    $query1->whereMonth('consultations.mc_enddate',$month)->whereYear('consultations.mc_enddate',$year);
                });
            })
            ->orderBy('consultations.mc_enddate', 'desc')
            ->groupBy('consultations.ic')
            ->paginate(10);
            $consultation_details = DB::table('consultations')
            ->join('clinics', 'clinics.id', 'consultations.clinic_id')
            ->select(
                'clinics.name as clinic_name',
                'consultations.*',
                )
            ->where('consultations.company_id', Auth::user()->company_id)
            ->where('consultations.mc_startdate', '!=',null)
            ->where('consultations.mc_enddate', '!=',null)
            ->where(function($query) use($month,$year) {
                $query->whereMonth('consultations.mc_startdate',$month)->whereYear('consultations.mc_startdate',$year)
                ->orWhere(function($query1) use($month,$year) {
                    $query1->whereMonth('consultations.mc_enddate',$month)->whereYear('consultations.mc_enddate',$year);
                });
            })
            ->orderBy('consultations.mc_enddate', 'desc')
            ->get();
        }
        else{
            $consultations = DB::table('consultations')
            ->join('employees', 'consultations.ic', 'employees.ic')
            ->join('clinics', 'clinics.id', 'consultations.clinic_id')
            ->select(
                'employees.name as employee_name',
                'employees.ic as employee_ic',
                'clinics.name as clinic_name',
                'consultations.*',
                DB::raw('count(consultations.id) as total_mc'),
                )
            ->where('consultations.company_id', Auth::user()->company_id)
            ->where('consultations.mc_startdate', '!=',null)
            ->where('consultations.mc_enddate', '!=',null)
            ->where('employees.category', '=','employee')
            ->orderBy('consultations.mc_enddate', 'desc')
            ->groupBy('consultations.ic')
            ->paginate(10);
            $consultation_details = DB::table('consultations')
            ->join('clinics', 'clinics.id', 'consultations.clinic_id')
            ->select(
                'clinics.name as clinic_name',
                'consultations.*',
                )
            ->where('consultations.company_id', Auth::user()->company_id)
            ->where('consultations.mc_startdate', '!=',null)
            ->where('consultations.mc_enddate', '!=',null)
            ->orderBy('consultations.mc_enddate', 'desc')
            ->get();
        }
        
        return view('company.mc', compact('consultations','consultation_details'));
    }

    public function mc_dependent($id){
        $dependents = Employee::where('category','dependent')->where('employee_id',$id)->pluck('id')->toArray();
        $employee = Employee::find($id)->first();
        $consultations = DB::table('consultations')
        ->join('employees', 'consultations.ic', 'employees.ic')
        ->join('clinics', 'clinics.id', 'consultations.clinic_id')
        ->select(
            'employees.name as employee_name',
            'employees.ic as employee_ic',
            'clinics.name as clinic_name',
            'consultations.*',
            )
        ->whereIn('consultations.employee_id', $dependents)
        ->where('consultations.mc_startdate', '!=',null)
        ->where('consultations.mc_enddate', '!=',null)
        ->where('employees.category', '!=','employee')
        ->orderBy('consultations.mc_enddate', 'desc')
        ->paginate(10);
        return view('company.mc_dependent', compact('consultations','employee'));
    }
}
