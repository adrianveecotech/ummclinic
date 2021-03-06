<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Clinic;
use App\Models\User;

class ClinicController extends Controller
{
    public function index(Request $request){
        $branchDetail =  DB::table('consultations')
            ->select('consultations.*','clinics.*','employees.*','clinics.name as clinic_name','employees.name as employee_name','consultations.id as consultation_id','clinics.id as clinic_id','employees.id as employee_id','consultations.created_at as consultation_created_at','companys.name as company_name')
            ->join('clinics', 'clinics.id', '=', 'consultations.clinic_id')
            ->join('companys', 'companys.id', '=', 'consultations.company_id')
            ->join('employees', 'employees.ic', '=', 'consultations.ic')
            ->get();
        // $allClinics = Clinic::leftJoin('clinics as c1','clinics.branch_clinic_id','c1.id')->select('clinics.*','c1.name as branch_name')->get();
        if($request->get('search')){
            $search = $request->get('search');

            $clinics = Clinic::leftJoin('clinics as c1','clinics.branch_clinic_id','c1.id')->select('clinics.*','c1.name as branch_name')->where('clinics.name', 'LIKE', '%'.$search.'%');
                
        }        
        
        else if($request->get('status')){
            $status = $request->get('status');

            $clinics = Clinic::leftJoin('clinics as c1','clinics.branch_clinic_id','c1.id')->select('clinics.*','c1.name as branch_name')->where('clinics.status', 'LIKE', $status);
        }

        else{
            $clinics = Clinic::leftJoin('clinics as c1','clinics.branch_clinic_id','c1.id')->select('clinics.*','c1.name as branch_name');
        }
        $allClinics = $clinics->get();
        $clinics = $clinics->paginate(10);
        return view('admin.clinic', compact('clinics','branchDetail','allClinics'));
    }

    public function register(){
        $clinics = Clinic::whereNull('branch_clinic_id')->orderBy('name','asc')->get();

        return view('admin.register_clinic',compact('clinics'));   
    }

    public function store_clinic_details(Request $request){
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:clinics|unique:users',
            'address' => 'required|max:255',
            'contact' => 'required|max:255',
        ]);

        $email = $request->input('email');

        $clinics = new Clinic();

        $clinics->name = Str::upper($request->input('name'));
        $clinics->email = $email;
        $clinics->address = Str::upper($request->input('address'));
        $clinics->contact = $request->input('contact');
        if($request->input('branch') != 0){
            $clinics->branch_clinic_id = $request->input('branch');
        }

        $clinics->save();

        $clinic_id = DB::table('clinics')
            ->where('email', $email)
            ->first();

        $users = new User();

        $password = Str::random(8);
        $random_password = Hash::make($password);

        $users->name = Str::upper($request->input('name'));
        $users->email = $email;
        $users->password = $random_password;
        $users->clinic_id = $clinic_id->id;

        $details = [
            'body' => 'This is your temporary password: ' . $password
        ];
       
        \Mail::to($email)->send(new \App\Mail\AccountCreatePasswordMail($details));

        $users->save();

        return back()->with('message', 'Clinic Register Successfully.');
    }

    public function update_clinic_details(Request $request, $id){
        $update = Clinic::findorfail($id);

        $update->name = Str::upper($request->input('name'));
        $update->address = Str::upper($request->input('address'));
        $update->email = $request->input('email');
        $update->contact = $request->input('contact');
        $update->status = $request->input('status');

        $update->save();

        $clinic_id = DB::table('clinics')
            ->where('email', $request->input('email'))
            ->first();
            
        User::where('clinic_id', $clinic_id->id)
            ->update([
                'name' => Str::upper($request->input('name')),
                'status' => $request->input('status')
            ]);

        return back()->with('message', 'Clinic Update Successfully.');
    }

    public function delete_clinic_details($id){
        $delete = Clinic::findorfail($id);

        $delete->delete();

        return back()->with('message', 'Clinic Delete Successfully.');
    }
}
