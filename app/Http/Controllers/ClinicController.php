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
        if($request->get('search')){
            $search = $request->get('search');

            $clinics = Clinic::where('name', 'LIKE', '%'.$search.'%')
                ->paginate(10);

            return view('admin.clinic', compact('clinics'));

        }        
        
        else if($request->get('status')){
            $status = $request->get('status');

            $clinics = Clinic::where('status', 'LIKE', $status)
                ->paginate(10);

            return view('admin.clinic', compact('clinics'));
        }

        else{
            $clinics = Clinic::paginate(10);

            return view('admin.clinic', compact('clinics'));
        }
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

        $random_password = Hash::make(Str::random(8));

        $users->name = Str::upper($request->input('name'));
        $users->email = $email;
        $users->password = $random_password;
        $users->clinic_id = $clinic_id->id;

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
