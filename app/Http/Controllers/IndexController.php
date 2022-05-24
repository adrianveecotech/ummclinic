<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Auth;  
use App\Models\Consultation;
use App\Models\Clinic;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function admin_index(){
        $count_today_consultation = Consultation::whereDate('created_at', Carbon::today())->count();
        $count_total_consultation = Consultation::count();
        $count_total_clinic = Clinic::where('status', 'active')->count();
        $count_total_company = Company::where('status', 'active')->count();

        $count_total_yesterday = Consultation::whereDate('created_at', Carbon::yesterday())->count();

        if($count_today_consultation == 0){
            $today_percent = 0;
        }
        else{
            $today_percent = ($count_today_consultation - $count_total_yesterday) * 100;
        }

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
            ->paginate(5);

        return view('admin.dashboard', compact('count_today_consultation', 'count_total_consultation', 'count_total_clinic', 'count_total_company', 'today_percent', 'consultations'));
    }

    public function clinic_index(){
        $count_today_consultation = Consultation::where('clinic_id', Auth::user()->clinic_id)
            ->whereDate('created_at', Carbon::today())->count();
        
        $count_total_consultation = Consultation::where('clinic_id', Auth::user()->clinic_id)->count();

        $count_monthly_consultation = Consultation::whereBetween('created_at', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->where('clinic_id', Auth::user()->clinic_id)
            ->count();

        $count_total_yesterday = Consultation::whereDate('created_at', Carbon::yesterday())->count();

        if($count_today_consultation == 0){
            $today_percent = 0;
        }
        else{
            $today_percent = ($count_today_consultation - $count_total_yesterday) * 100;
        }

        $consultations = DB::table('consultations')
            ->join('employees', 'consultations.ic', 'employees.ic')
            ->join('clinics', 'consultations.clinic_id', 'clinics.id')
            ->join('doctors', 'consultations.doctor_id', 'doctors.id')
            ->join('payments', 'consultations.id', 'payments.consultation_id')
            ->join('companys', 'consultations.company_id', 'companys.id')
            ->select(
                'employees.name as patient_name',
                'companys.name as companys_name',
                'clinics.name as clinic_name',
                'consultations.*',
                'doctors.name as doctor_name',
                'payments.status as payment_status',
                'consultations.created_at as consultation_created_at'
                )
            ->where('consultations.clinic_id', Auth::user()->clinic_id)
            ->orderBy('consultation_created_at', 'desc')
            ->paginate(5);
        return view('clinic.dashboard', compact('count_today_consultation', 'count_total_consultation', 'count_monthly_consultation', 'today_percent', 'consultations'));
    }

    public function company_index(){
        $count_today_consultation = Consultation::where('company_id', Auth::user()->company_id)
            ->whereDate('created_at', Carbon::today())->count();
    
        $count_total_consultation = Consultation::where('company_id', Auth::user()->company_id)->count();

        $count_monthly_consultation = Consultation::whereBetween('created_at', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->where('company_id', Auth::user()->company_id)
            ->count();

        $count_total_yesterday = Consultation::whereDate('created_at', Carbon::yesterday())->count();

        if($count_today_consultation == 0){
            $today_percent = 0;
        }
        else{
            $today_percent = ($count_today_consultation - $count_total_yesterday) * 100;
        }

        $total_outstandings = DB::table('payments')
            ->select(
                DB::raw('sum(amount) as total_outstanding'),
            )
            ->where('status', 'unsettled')
            ->where('company_id', Auth::user()->company_id)
            ->get();
        
        $count_total_outstanding = "";
        foreach($total_outstandings as $total_outstanding){
            $count_total_outstanding = $total_outstanding->total_outstanding;
        }

        $consultations = DB::table('consultations')
            ->leftJoin('employees','consultations.employee_id','employees.id')
            ->leftJoin('employees AS e1','employees.employee_id','e1.id')
            ->join('clinics', 'consultations.clinic_id', 'clinics.id')
            ->join('doctors', 'consultations.doctor_id', 'doctors.id')
            ->join('payments', 'consultations.id', 'payments.consultation_id')
            ->select(
                'employees.name as employee_name',
                'employees.category as employee_category',
                'clinics.name as clinic_name',
                'consultations.*',
                'doctors.name as doctor_name',
                'payments.status as payment_status',
                'employees.company_employee_id as company_employee_id_new'
                )
            ->where('consultations.company_id', Auth::user()->company_id)
            ->orderBy('consultations.created_at', 'desc');
        // $consultationsDependent = DB::table('consultations')
        //     ->leftJoin('dependents','consultations.dependent_id','dependents.id')
        //     ->leftJoin('employees','dependents.employee_id','employees.id')
        //     ->join('clinics', 'consultations.clinic_id', 'clinics.id')
        //     ->join('doctors', 'consultations.doctor_id', 'doctors.id')
        //     ->join('payments', 'consultations.id', 'payments.consultation_id')
        //     ->select(
        //         'dependents.name as employee_name',
        //         'dependents.category as employee_category',
        //         'clinics.name as clinic_name',
        //         'consultations.*',
        //         'doctors.name as doctor_name',
        //         'payments.status as payment_status',
        //         'employees.company_employee_id as company_employee_id_new'
        //         )
        //     ->where('consultations.company_id', Auth::user()->company_id)
        //     ->where('consultations.dependent_id','!=','NULL')
        //     ->orderBy('consultations.created_at', 'desc');

        // $consultations = $consultationsEmployee->union($consultationsDependent);
        $consultations = $consultations->paginate(5);

        return view('company.dashboard', compact('count_today_consultation', 'count_total_consultation', 'count_monthly_consultation', 'count_total_outstanding', 'today_percent', 'consultations'));
    }
}
