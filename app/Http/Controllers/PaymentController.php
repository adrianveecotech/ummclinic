<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Auth;  
use App\Models\Payment;
use App\Models\Company;
use App\Models\Consultation;
use App\Models\Invoice;

class PaymentController extends Controller
{
    //Admin
    public function outstanding_index(Request $request){
        $payments = '';
        $paymentsWithDetails = array();
        $allPayments = '';
        $paymentDetails = array();
        $companies = Company::orderBy('name')->get();
        $payments = DB::table('payments')
        ->join('companys', 'payments.company_id', 'companys.id')
        ->select(
            'companys.name as company_name',
            DB::raw('sum(payments.amount) as total_amount'),
            'payments.*'
        )
        ->where('payments.status','unsettled')
        ->groupBy('payments.company_id')
        ->orderBy('payments.created_at', 'desc');
        $allPayments = $payments->get();
        $payments = $payments->paginate(10);
        if($request->get('search')){
            $search = $request->get('search');
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'employees.name as employee_name',
                    'companys.name as company_name',
                    'clinics.name as clinic_name',
                    'payments.*'
                )
                ->where(function ($query) use ($search) {
                    $query
                        ->where('payments.ic', 'LIKE', $search)
                        ->orwhere('companys.name', 'LIKE', '%'.$search.'%')
                        ->orwhere('clinics.name', 'LIKE', '%'.$search.'%');
                    })
                ->orderBy('payments.created_at', 'desc');
                $allPayments = $payments->get();
                $payments = $payments->paginate(10);
        }
        return view('admin.outstanding', compact('payments','companies','allPayments'));
    }

    public function outstanding_monthly($company_id){
        $payments = DB::table('payments')
        ->select(
            DB::raw('DATE_FORMAT(created_at,"%Y-%M") as date, sum(payments.amount) as total_amount'),
            'payments.*'
        )
        ->where('payments.status','unsettled')
        ->where('payments.company_id',$company_id)
        ->orderBy('payments.created_at', 'desc')
        ->groupBy('date')
        ->paginate(10);

        foreach ($payments as $key => $payment) {
            $uploaded = Invoice::where('company_id',$company_id)->where('date',$payment->date)->orderBy('created_at','desc')->first();
            if($uploaded){
                $payments[$key]->file = $uploaded->file;
            }else
                $payments[$key]->file = '';
        }

        $company = Company::find($company_id);

        return view('admin.outstanding_monthly', compact('payments','company'));
    }

    public function outstanding_detail($company_id,$date){
        $payments = $this->getPaymentsByCompanyAndDate($company_id, $date);
        $allPayments = $payments->get();
        $payments = (($payments))->paginate(10);
        $company = Company::find($company_id);

        return view('admin.outstanding_detail', compact('payments','company','date','allPayments'));
    }

    public function getPaymentsByCompanyAndDate($company_id,$date){
        $payments = DB::table('payments')
        ->leftJoin('consultations','consultations.id','payments.consultation_id')
        ->leftJoin('clinics','consultations.clinic_id','clinics.id')
        ->leftJoin('employees','consultations.employee_id','employees.id')
        ->select(
            'payments.*','clinics.name as clinic_name','employees.name as patient_name'
        )
        ->where('payments.status','unsettled')
        ->where('payments.company_id',$company_id)
        ->whereRaw("DATE_FORMAT(payments.created_at,'%Y-%M') = '$date'")
        ->orderBy('payments.created_at', 'desc');

        // $paymentsDependent = DB::table('payments')
        // ->leftJoin('consultations','consultations.id','payments.consultation_id')
        // ->leftJoin('clinics','consultations.clinic_id','clinics.id')
        // ->leftJoin('dependents','consultations.dependent_id','dependents.id')
        // ->select(
        //     'payments.*','clinics.name as clinic_name','dependents.name as patient_name'
        // )
        // ->where('payments.status','unsettled')
        // ->where('payments.company_id',$company_id)
        // ->whereRaw("DATE_FORMAT(payments.created_at,'%Y-%M') = '$date'")
        // ->where('consultations.dependent_id','!=','NULL')
        // ->orderBy('payments.created_at', 'desc');
        
        // $payments = $paymentsEmployee->union($paymentsDependent);
        return $payments;
    }

    public function upload_invoice(Request $request){

        $invoice = '';
        if ($request->hasFile('invoice')) {
            $file = $request->file('invoice');
            $invoice = date('YmdHis') . "." . $file->getClientOriginalExtension();
            $file->move('files/invoice', $invoice);
        }
        $newInvoice = new Invoice();
        $newInvoice->company_id = $request->company_id;
        $newInvoice->date = $request->date;
        $newInvoice->file = $invoice;
        if($newInvoice->save()){
            return back()->with('success','Invoice has been successfully uploaded for ' .$request->date);
        }else{
            return back()->with('fail','There is an issue while uploading the invoice.');
        }



    }

    public function payment_history_index(Request $request){
        $payments = '';
        $paymentsWithDetails = array();
        $allPayments = '';
        $paymentDetails = array();
        $companies = Company::orderBy('name')->get();
        if($request->get('group_by') == 'company' && $request->get('month')){
            $date = $request->month;
            $date = explode('-',$date);
            $year = $date[0];
            $month = $date[1];
            $payments = DB::table('payments')
            ->join('companys', 'payments.company_id', 'companys.id')
            ->select(
                'companys.name as company_name',
                DB::raw('sum(payments.amount) as total_amount'),
                'payments.*'
            )
            ->whereMonth('payments.created_at',$month)
            ->whereYear('payments.created_at',$year)
            ->where('payments.status','unsettled')
            ->groupBy('payments.company_id')
            ->orderBy('payments.created_at', 'desc');
            $allPayments = $payments->get();
            $payments = $payments->paginate(10);


        }else if($request->get('group_by') == 'company'){
            $payments = DB::table('payments')
            ->join('companys', 'payments.company_id', 'companys.id')
            ->select(
                'companys.name as company_name',
                DB::raw('sum(payments.amount) as total_amount'),
                'payments.*'
            )
            ->where('payments.status','unsettled')
            ->groupBy('payments.company_id')
            ->orderBy('payments.created_at', 'desc');
            $allPayments = $payments->get();
            $payments = $payments->paginate(10);
            $paymentDetails = Payment::get();
            $paymentsWithDetails =  DB::table('payments')
            ->select('payments.*','consultations.*','clinics.*','employees.*','payments.id as payment_id','payments.created_at as payment_created_at','clinics.name as clinic_name')
            ->join('consultations', 'payments.consultation_id', '=', 'consultations.id')
            ->join('clinics', 'clinics.id', '=', 'consultations.clinic_id')
            ->join('employees', 'employees.ic', '=', 'consultations.ic')
            ->where('payments.status','unsettled')
            ->get();       
        }
        else if($request->get('search')){
            $search = $request->get('search');
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'employees.name as employee_name',
                    'companys.name as company_name',
                    'clinics.name as clinic_name',
                    'payments.*'
                )
                ->where(function ($query) use ($search) {
                    $query
                        ->where('payments.ic', 'LIKE', $search)
                        ->orwhere('companys.name', 'LIKE', '%'.$search.'%')
                        ->orwhere('clinics.name', 'LIKE', '%'.$search.'%');
                    })
                ->orderBy('payments.created_at', 'desc');
                $allPayments = $payments->get();
                $payments = $payments->paginate(10);
        }             
        else if($request->get('status')){
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $status = $request->get('status');
            $company_id = $request->get('company');
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'employees.name as employee_name',
                    'companys.name as company_name',
                    'clinics.name as clinic_name',
                    'payments.*'
                );

            if($start_date && $end_date){
                $payments = $payments                    
                    ->whereDate('payments.created_at', '>=', $start_date)
                    ->whereDate('payments.created_at', '<=', $end_date);
            }             
            if($status){
                $payments = $payments->where('payments.status', 'LIKE', $status);
            }    
            if($company_id){
                $payments = $payments->where('companys.id', '=', $company_id);
            }
            $payments = $payments
                ->orderBy('payments.created_at', 'desc');
            $allPayments = $payments->get();
            $payments = $payments->paginate(10);

        }

        else if($request->get('start_date') && $request->get('end_date')){
            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $company_id = $request->get('company');
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'employees.name as employee_name',
                    'companys.name as company_name',
                    'clinics.name as clinic_name',
                    'payments.*'
                )
                ->whereDate('payments.created_at', '>=', $start_date)
                ->whereDate('payments.created_at', '<=', $end_date);
            if($company_id){
                $payments = $payments->where('companys.id', '=', $company_id);
            }    
            $payments = $payments->orderBy('payments.created_at', 'desc');
            $allPayments = $payments->get();
            $payments = $payments->paginate(10);
        
        }
        else if($request->get('company')){
            $company_id = $request->get('company');
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'employees.name as employee_name',
                    'companys.name as company_name',
                    'clinics.name as clinic_name',
                    'payments.*'
                )
                ->where('companys.id', '=', $company_id)
                ->orderBy('payments.created_at', 'desc');
                $allPayments = $payments->get(); 
                $payments = $payments->paginate(10);
        }
        else{
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'employees.name as employee_name',
                    'companys.name as company_name',
                    'clinics.name as clinic_name',
                    'payments.*'
                )
                ->orderBy('payments.created_at', 'desc');
            $allPayments = $payments->get();
            $payments = $payments->paginate(10);
        }
        return view('admin.payment_history', compact('payments','companies','allPayments','paymentDetails','paymentsWithDetails'));
    }
    // Clinic
    public function index(Request $request){
        if($request->get('search')){
            $search = $request->get('search');

            $payments = DB::table('payments')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->select(
                    'companys.name as company_name',
                    'payments.status as status',
                    'payments.company_id as company_id',
                    DB::raw('sum(payments.amount) as total_amount'),
                    DB::raw('COUNT(payments.id) as count_unsettled')
                )
                ->where('payments.status', 'unsettled')
                ->where('payments.clinic_id', Auth::user()->clinic_id)
                ->where('companys.name', 'LIKE', '%'.$search.'%')
                ->groupBy('company_name')
                ->paginate(10);

            return view('clinic.payment', compact('payments'));
        }

        else{
            $payments = DB::table('payments')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->select(
                    'companys.name as company_name',
                    'payments.status as status',
                    'payments.company_id as company_id',
                    DB::raw('sum(payments.amount) as total_amount'),
                    DB::raw('COUNT(payments.id) as count_unsettled')
                )
                ->where('payments.status', 'unsettled')
                ->where('payments.clinic_id', Auth::user()->clinic_id)
                ->groupBy('company_name')
                ->paginate(10);
            return view('clinic.payment', compact('payments'));
        }
    }    
    
    public function outstanding_details(Request $request){
        if($request->get('search')){
            $search = $request->get('search');
            $company_id = $request->get('company');
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('consultations', 'payments.consultation_id', 'consultations.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('medications_consultation','medications_consultation.consultation_id','consultations.id')
                ->select(
                    'employees.name as patient_name', 
                    'companys.name as company_name', 
                    'consultations.description as consultations_description',
                    'consultations.mc_startdate',
                    'consultations.mc_enddate',
                    'consultations.price',
                    'doctors.name as doctor_name',
                    'payments.*',
                    'payments.created_at as payment_created_at',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')

                )
                ->where('payments.clinic_id', Auth::user()->clinic_id)
                ->where('payments.company_id', $company_id)
                ->where('payments.status', 'unsettled')
                ->orderby('payments.created_at', 'desc')
                ->where(function ($query) use ($search) {
                    $query
                        ->where('payments.ic', 'LIKE', $search)
                        ->orwhere('companys.name', 'LIKE', '%'.$search.'%');
                    });
            $allPayments = $payments->get();
            $payments = $payments->paginate(10);         
            // // $paymentsDependents = DB::table('payments')
            // //     ->join('dependents', 'payments.ic', 'dependents.ic')
            // //     ->join('companys', 'payments.company_id', 'companys.id')
            // //     ->join('consultations', 'payments.consultation_id', 'consultations.id')
            // //     ->join('doctors', 'consultations.doctor_id', 'doctors.id')
            // //     ->select(
            // //         'dependents.name as patient_name', 
            // //         'companys.name as company_name', 
            // //         'consultations.description as consultations_description',
            // //         'doctors.name as doctor_name',
            // //         'payments.*'
            // //     )
            // //     ->where('payments.clinic_id', Auth::user()->clinic_id)
            // //     ->where('payments.company_id', $company_id)
            // //     ->orderby('payments.created_at', 'desc')
            // //     ->where(function ($query) use ($search) {
            // //         $query
            // //             ->where('payments.ic', 'LIKE', $search)
            // //             ->orwhere('companys.name', 'LIKE', '%'.$search.'%');
            // //         });
            // $payments = $paymentsEmployees->unionAll($paymentsDependents);

            $companys = Company::select('name')->where('id', $company_id)->get();

            $company_name = "";
            foreach($companys as $company){
                $company_name = $company->name;
            }

            $outstanding_amount = DB::table('payments')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->select(
                    'companys.name as company_name', 
                    DB::raw("sum(payments.amount) as total_amount")
                )
                ->where('payments.status', 'unsettled')
                ->where('payments.company_id', $company_id)
                ->where('payments.clinic_id', Auth::user()->clinic_id)
                ->where(function ($query) use ($search) {
                    $query
                        ->where('payments.ic', 'LIKE', $search)
                        ->orwhere('companys.name', 'LIKE', '%'.$search.'%');
                    })
                ->get();

            return view('clinic.outstanding_details', compact('payments', 'allPayments', 'company_name', 'outstanding_amount'));
        }

        if($request->get('status')){
            $company_id = $request->get('company');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');
            $status = $request->get('status');
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('consultations', 'payments.consultation_id', 'consultations.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('medications_consultation','medications_consultation.consultation_id','consultations.id')
                ->select(
                    'consultations.description as consultations_description',
                    'consultations.mc_startdate',
                    'consultations.mc_enddate',
                    'employees.name as patient_name', 
                    'companys.name as company_name', 
                    'consultations.price',
                    'doctors.name as doctor_name',
                    'payments.*',
                    'payments.created_at as payment_created_at',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                )->groupBy('payments.id');
            // $paymentsDependents = DB::table('payments')
            //     ->join('dependents', 'payments.ic', 'dependents.ic')
            //     ->join('companys', 'payments.company_id', 'companys.id')
            //     ->join('consultations', 'payments.consultation_id', 'consultations.id')
            //     ->join('doctors', 'consultations.doctor_id', 'doctors.id')
            //     ->select(
            //         'dependents.name as patient_name', 
            //         'companys.name as company_name', 
            //         'consultations.price',
            //         'doctors.name as doctor_name',
            //         'payments.*',
            //         'payments.created_at as payment_created_at',
            //     );
            // $payments = $paymentsEmployees->unionAll($paymentsDependents);
            $outstanding_amount = DB::table('payments')
            ->join('companys', 'payments.company_id', 'companys.id')
            ->select(
                'companys.name as company_name', 
                DB::raw("sum(payments.amount) as total_amount")
            );
            
            if($start_date && $end_date){
                $payments = $payments                    
                    ->whereDate('payment_created_at', '>=', $start_date)
                    ->whereDate('payment_created_at', '<=', $end_date);
                
                $outstanding_amount = $outstanding_amount
                    ->whereDate('payment_created_at', '>=', $start_date)
                    ->whereDate('payment_created_at', '<=', $end_date);
            }             
            if($status){
                $payments = $payments->where('payments.status', 'LIKE', $status);

                $outstanding_amount = $outstanding_amount->where('payments.status', 'LIKE', $status);
            }    
            $payments = $payments
                ->where('payments.clinic_id', Auth::user()->clinic_id)
                ->where('payments.company_id', $company_id)
                ->orderby('payment_created_at', 'desc');

            $allPayments = $payments->get();               
            $payments = $payments->paginate(10);
            
            $outstanding_amount = $outstanding_amount         
                ->where('payments.status', 'unsettled')
                ->where('payments.company_id', $company_id)
                ->where('payments.clinic_id', Auth::user()->clinic_id)           
                ->get();

            $companys = Company::select('name')->where('id', $company_id)->get();

            $company_name = "";
            foreach($companys as $company){
                $company_name = $company->name;
            }

            return view('clinic.outstanding_details', compact('payments', 'allPayments', 'company_name', 'outstanding_amount'));
        }

        if($request->get('start_date') && $request->get('end_date')){
            $company_id = $request->get('company');

            $start_date = $request->get('start_date');
            $end_date = $request->get('end_date');

            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('consultations', 'payments.consultation_id', 'consultations.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('medications_consultation','medications_consultation.consultation_id','consultations.id')
                ->select(
                    'consultations.description as consultations_description',
                    'consultations.mc_startdate',
                    'consultations.mc_enddate',
                    'consultations.price',
                    'employees.name as patient_name', 
                    'companys.name as company_name', 
                    'consultations.price',
                    'doctors.name as doctor_name',
                    'payments.*',
                    'payments.created_at as payment_created_at',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                )
                ->whereDate('payments.created_at', '>=', $start_date)
                ->whereDate('payments.created_at', '<=', $end_date)
                ->where('payments.clinic_id', Auth::user()->clinic_id)
                ->where('payments.status', 'unsettled')
                ->where('payments.company_id', $company_id)
                ->orderby('payments.created_at', 'desc');

            $allPayments = $payments->get();               
            $payments = $payments->paginate(10);

            // $paymentsDependents = DB::table('payments')
            //     ->join('dependents', 'payments.ic', 'dependents.ic')
            //     ->join('companys', 'payments.company_id', 'companys.id')
            //     ->join('consultations', 'payments.consultation_id', 'consultations.id')
            //     ->join('doctors', 'consultations.doctor_id', 'doctors.id')
            //     ->select(
            //         'dependents.name as patient_name', 
            //         'companys.name as company_name', 
            //         'consultations.price',
            //         'doctors.name as doctor_name',
            //         'payments.*',
            //         'payments.created_at as payment_created_at',
            //     )
            //     ->whereDate('payments.created_at', '>=', $start_date)
            //     ->whereDate('payments.created_at', '<=', $end_date)
            //     ->where('payments.clinic_id', Auth::user()->clinic_id)
            //     ->where('payments.company_id', $company_id)
            //     ->orderby('payments.created_at', 'desc');
            // $payments = $paymentsEmployees->unionAll($paymentsDependents)->orderBy('payment_created_at', 'desc')->paginate(10);

            $companys = Company::select('name')->where('id', $company_id)->get();

            $company_name = "";
            foreach($companys as $company){
                $company_name = $company->name;
            }

            $outstanding_amount = DB::table('payments')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->select(
                    'companys.name as company_name', 
                    DB::raw("sum(payments.amount) as total_amount")
                )
                ->where('payments.status', 'unsettled')
                ->where('payments.company_id', $company_id)
                ->where('payments.clinic_id', Auth::user()->clinic_id)
                ->whereDate('payments.created_at', '>=', $start_date)
                ->whereDate('payments.created_at', '<=', $end_date)                
                ->get();

            return view('clinic.outstanding_details', compact('payments', 'allPayments', 'company_name', 'outstanding_amount'));
        }

        if($request->get('company')){
            $company_id = $request->get('company');
            $payments = DB::table('payments')
                ->join('employees', 'payments.ic', 'employees.ic')
                ->join('companys', 'payments.company_id', 'companys.id')
                ->join('consultations', 'payments.consultation_id', 'consultations.id')
                ->join('doctors', 'consultations.doctor_id', 'doctors.id')
                ->join('medications_consultation','medications_consultation.consultation_id','consultations.id')
                ->select(
                    'consultations.description as consultations_description',   
                    'consultations.mc_startdate',
                    'consultations.mc_enddate',                  
                    'consultations.price',
                    'employees.name as patient_name', 
                    'companys.name as company_name', 
                    'doctors.name as doctor_name',
                    'payments.*',
                    'payments.created_at as payment_created_at',
                    DB::raw('group_concat(medications_consultation.name) as medications_name')
                )
                ->where('payments.company_id', $company_id)
                ->where('payments.clinic_id', Auth::user()->clinic_id)
                ->where('payments.status', 'unsettled')
                ->groupBy('consultations.id')
                ->orderBy('payment_created_at', 'desc');

                $allPayments = $payments->get();               
                $payments = $payments->paginate(10);
            // $paymentsDependents = DB::table('payments')
            //     ->join('dependents', 'payments.ic', 'dependents.ic')
            //     ->join('companys', 'payments.company_id', 'companys.id')
            //     ->join('consultations', 'payments.consultation_id', 'consultations.id')
            //     ->join('doctors', 'consultations.doctor_id', 'doctors.id')
            //     ->select(
            //         'dependents.name as patient_name', 
            //         'companys.name as company_name', 
            //         'consultations.price',
            //         'doctors.name as doctor_name',
            //         'payments.*',
            //         'payments.created_at as payment_created_at',
            //     )
            //     ->where('payments.company_id', $company_id)
            //     ->where('payments.clinic_id', Auth::user()->clinic_id);
            // $payments = $paymentsEmployees->unionAll($paymentsDependents)->orderBy('payment_created_at', 'desc')->paginate(10);

            $companys = Company::select('name')->where('id', $company_id)->get();

            $company_name = "";
            foreach($companys as $company){
                $company_name = $company->name;
            }

            $outstanding_amount = DB::table('payments')
                ->select(
                    DB::raw("sum(payments.amount) as total_amount")
                )
                ->where('status', 'unsettled')
                ->where('company_id', $company_id)
                ->where('clinic_id', Auth::user()->clinic_id)
                ->get();
                
            return view('clinic.outstanding_details', compact('payments','allPayments', 'company_name', 'outstanding_amount'));
        }
    }

    public function billing(Request $request, $payment_id){
        $request->validate([
            'amount' => 'required',
            'reference' => 'required'
        ]);

        $input_amount = $request->input('amount');
        $outstanding_amount = Payment::where('id', $payment_id)->select('amount')->first();
        $remaining_amount = $outstanding_amount->amount - $input_amount;

        $billings = Payment::findorfail($payment_id);
        $billings->reference = $request->input('reference');

        if($remaining_amount == 0.00){
            $billings->status = 'settled';
            $billings->amount = $remaining_amount;
            $billings->settled_date = Carbon::now();
    
            $billings->save();

            return back()->with('message', 'Payment update successfully.');
        }
        if($remaining_amount > 0.00){
            $billings->amount = $remaining_amount;

            $billings->save();

            return back()->with('message', 'Payment update successfully.');
        }
        if($remaining_amount < 0.00){
            return back()->withErrors('Amount paid should not be bigger than the outstanding amount.');
        }
    }

    public function processing(Request $request, $payment_id){
        $payment = Payment::find($payment_id);
        $payment->status = 'processing';
        $payment->save();

        return back()->with('success','Payment status has been changed to Proccessing.');
    }

    public function monthly_settle(Request $request){
        $company_id = $request->company_id;
        $date = $request->date;
        $payments = $this->getPaymentsByCompanyAndDate($company_id, $date);
        $payments = $payments->get();
        foreach ($payments as $payment) {
            $paymentFound = Payment::find($payment->id);
            $paymentFound->status = 'settled';
            $paymentFound->save();
        }
        return back()->with('success','Payment status has been changed to Settled.');

    }

    public function multiple_billing(Request $request, $company_id){
        $ids = DB::table('payments')
            ->select('id')
            ->where('company_id', $company_id)
            ->where('clinic_id', Auth::user()->clinic_id)
            ->where('status', 'unsettled')
            ->orderBy('created_at', 'asc')
            ->get();

        $payment_ids = [];
        foreach($ids as $id){
            $payment_ids[] = $id->id;
        }

        $outstanding_amount = DB::table('payments')
            ->select(
                DB::raw('sum(amount) as total_outstanding'),
            )
            ->whereIn('id', $payment_ids)
            ->get();
        
        $input_amount = $request->input('amount');

        if($input_amount > $outstanding_amount[0]->total_outstanding){
            return back()->withErrors('Amount paid should not be bigger than the outstanding amount.');
        }
        else{
            $balance = $input_amount;

            foreach($payment_ids as $payment_id){
                $outstanding_amount = DB::table('payments')
                    ->select(
                        'amount',
                    )
                    ->where('id', $payment_id)
                    ->first();

                if($balance > 0){
                    $balance = (($balance * 100)-($outstanding_amount->amount * 100)) / 100;

                    $billing = Payment::findorfail($payment_id);

                    if($balance >= 0.00){
                        $billing->status = 'settled';
                        $billing->amount = 0;
                        $billing->reference = $request->input('reference');
                        $billing->settled_date = carbon::now();
    
                        $billing->save();
                    }
                    if($balance < 0.00){
                        $billing->reference = $request->input('reference');
                        $billing->amount = number_format((float)(-$balance), 2);

                        $billing->save();
                    }
                }
            }
            return back()->with('message', 'Payment Settled');
        }
    }

    public function company_payment(Request $request){
        $clinics = Clinic::get();
        if($request->get('clinic') && $request->get('month'))
        {
            $clinic = $request->get('clinic');
            $yearMonth = $request->get('month');
            $date = explode('-',$yearMonth);
            $year = $date[0];
            $month = $date[1];

            $outstandings_amount = DB::table('payments')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'clinics.name as clinic_name',
                    'clinics.id as clinic_id',
                    DB::raw('sum(payments.amount) as total_outstanding')
                )
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.status', 'unsettled')
                ->groupBy('clinics.id')
                ->where('clinics.id', '=', $clinic)
                ->whereMonth('payments.created_at',$month)
                ->whereYear('payments.created_at',$year)
                ->get();
        }
        else if($request->get('clinic'))
        {
            $clinic = $request->get('clinic');

            $outstandings_amount = DB::table('payments')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'clinics.name as clinic_name',
                    'clinics.id as clinic_id',
                    DB::raw('sum(payments.amount) as total_outstanding')
                )
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.status', 'unsettled')
                ->groupBy('clinics.id')
                ->where('clinics.id', '=', $clinic)
                ->get();
        }
        else if($request->get('month'))
        {
            $yearMonth = $request->get('month');
            $date = explode('-',$yearMonth);
            $year = $date[0];
            $month = $date[1];

            $outstandings_amount = DB::table('payments')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'clinics.name as clinic_name',
                    'clinics.id as clinic_id',
                    DB::raw('sum(payments.amount) as total_outstanding')
                )
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.status', 'unsettled')
                ->groupBy('clinics.id')
                ->whereMonth('payments.created_at',$month)
                ->whereYear('payments.created_at',$year)
                ->get();
        }
        else if($request->get('search')){
            $search = $request->get('search');

            $outstandings_amount = DB::table('payments')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'clinics.name as clinic_name',
                    'clinics.id as clinic_id',
                    DB::raw('sum(payments.amount) as total_outstanding')
                )
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.status', 'unsettled')
                ->groupBy('clinics.id')
                ->where('clinics.name', 'LIKE', '%'.$search.'%')
                ->get();
        }
        else{
            $outstandings_amount = DB::table('payments')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->select(
                    'clinics.name as clinic_name',
                    'clinics.id as clinic_id',
                    DB::raw('sum(payments.amount) as total_outstanding'),
                    'payments.*'
                )
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.status', 'unsettled')
                ->groupBy('clinics.id')
                ->get();
        }
        return view('company.payment', compact('outstandings_amount','clinics'));
    }

    public function company_consultation_details(Request $request){
        $clinic_id = $request->clinic;
    

        $payments = DB::table('payments')
        ->join('clinics', 'payments.clinic_id', 'clinics.id')
        ->join('employees', 'employees.ic', 'payments.ic')
        ->select(
            'payments.*',
            'clinics.name as clinic_name',
            'employees.company_employee_id as employee_id',
            'employees.name as employee_name'
        )
        ->where('payments.clinic_id',$clinic_id)
        ->where('payments.company_id',Auth::user()->company_id);
        if($request->get('month')){
            $date = $request->month;
            $date = explode('-',$date);
            $year = $date[0];
            $month = $date[1];

            $payments = $payments
            ->whereMonth('payments.created_at',$month)
            ->whereYear('payments.created_at',$year);
        }
        $payments = $payments->paginate(10);
        return view('company.outstanding_details',compact('payments'));
    }

    public function export_report(Request $request){
        $outstandings_amount = DB::table('payments')
                ->join('clinics', 'payments.clinic_id', 'clinics.id')
                ->join('employees','employees.ic','payments.ic')
                ->select(
                    'clinics.name as clinic_name',
                    'employees.ic as employee_ic',
                    'employees.name as employee_name',
                    'payments.created_at as created_at',
                    'payments.amount as amount' 
                )
                ->where('payments.company_id', Auth::user()->company_id)
                ->where('payments.status', 'unsettled');

        if($request->clinic){
            $clinic = $request->clinic;
            $outstandings_amount = $outstandings_amount->where('clinics.id', '=', $clinic);
        }
        if($request->yearmonth){
            $clinic = $request->clinic;
            $yearmonth = $request->yearmonth;
            $date = explode("-",$yearmonth);
            $year = $date[0];
            $month = $date[1];
            $outstandings_amount = $outstandings_amount->whereMonth('payments.created_at',$month)->whereYear('payments.created_at',$year);
        }
        $outstandings_amount = $outstandings_amount->get();
        return array($outstandings_amount);

        
    }
}
