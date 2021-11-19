<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function clinic_profile(){
        $profiles = Clinic::where('id', Auth::user()->clinic_id)->get();

        return view('clinic.profile', compact('profiles'));
    }    
    
    public function company_profile(){
        $profiles = Company::where('id', Auth::user()->company_id)->get();

        return view('company.profile', compact('profiles'));
    }

    public function updateClinicProfile(Request $request, $clinic_id){
        $request->validate([
            'name' => 'required',
            'contact' => 'required',
            'address' => 'required',
            'email' => 'required',
        ]);

        $profile = Clinic::findorfail($clinic_id);

        $profile->name = $request->input('name');
        $profile->contact = $request->input('contact');
        $profile->address = $request->input('address');
        $profile->email = $request->input('email');

        $update_user = DB::table('users') 
            ->where('clinic_id', $clinic_id)
            ->update([
                'name' => $request->input('name'),
                'email' => $request->input('email')
            ]);

        $profile->save();

        return back()->with('message', 'Profile update successfully.');
    }    
    
    public function updateCompanyProfile(Request $request, $company_id){
        $request->validate([
            'name' => 'required',
            'contact' => 'required',
            'address' => 'required',
            'email' => 'required',
        ]);

        $profile = Company::findorfail($company_id);

        $profile->name = $request->input('name');
        $profile->contact = $request->input('contact');
        $profile->address = $request->input('address');
        $profile->email = $request->input('email');

        $update_user = DB::table('users') 
            ->where('company_id', $company_id)
            ->update([
                'name' => $request->input('name'),
                'email' => $request->input('email')
            ]);

        $profile->save();

        return back()->with('message', 'Profile update successfully.');
    }

    public function changePassword(Request $request, $id){
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $password = Hash::make($request->input('password'));

        $update = User::findorfail($id);

        $update->password = $password;

        $update->save();

        return back()->with('message', 'Password updated.');
    }
}
