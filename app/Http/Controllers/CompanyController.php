<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\User;

class CompanyController extends Controller
{
    public function index(Request $request){
        if($request->get('search')){
            $search = $request->get('search');

            $companies = Company::where('name', 'LIKE', '%'.$search.'%')
                ->paginate(10);

            return view('admin.company', compact('companies'));
        }        
        
        else if($request->get('status')){
            $status = $request->get('status');

            $companies = Company::where('status', 'LIKE', $status)
                ->paginate(10);

            return view('admin.company', compact('companies'));
        }

        else{
            $companies = Company::paginate(10);

            return view('admin.company', compact('companies'));
        }
    }

    public function store_company_details(Request $request){
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:companys|unique:users',
            'address' => 'required|max:255',
            'contact' => 'required|max:255',
        ]);

        $email = $request->input('email');

        $companys = new Company();

        $companys->name = Str::upper($request->input('name'));
        $companys->email = $email;
        $companys->address = Str::upper($request->input('address'));
        $companys->contact = $request->input('contact');

        $companys->save();

        $company_id = DB::table('companys')
            ->where('email', $email)
            ->first();

        $users = new User();

        $random_password = Hash::make(Str::random(8));

        $users->name = Str::upper($request->input('name'));
        $users->email = $email;
        $users->password = $random_password;
        $users->type = 'company';
        $users->company_id = $company_id->id;

        $users->save();

        return back()->with('message', 'Company Register Successfully.');
    }

    public function update_company_details(Request $request, $id){
        $update = Company::findorfail($id);

        $update->name = Str::upper($request->input('name'));
        $update->address = Str::upper($request->input('address'));
        $update->email = $request->input('email');
        $update->contact = $request->input('contact');
        $update->status = $request->input('status');

        $update->save();

        $company_id = DB::table('companys')
            ->where('email', $request->input('email'))
            ->first();
        
        User::where('company_id', $company_id->id)
            ->update([
                'name' => Str::upper($request->input('name')),
                'status' => $request->input('status')
            ]);

        return back()->with('message', 'Company Update Successfully.');
    }

    public function delete_company_details($id){
        $delete = Company::findorfail($id);

        $delete->delete();

        return back()->with('message', 'Company Delete Successfully.');
    }
}
