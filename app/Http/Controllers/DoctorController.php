<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    public function index(Request $request){
        if($request->get('search')){
            $search = $request->get('search');

            $doctors = Doctor::where('name', 'LIKE', '%'.$search.'%')
                ->where('clinic_id', Auth::user()->clinic_id)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->paginate(5);

            return view('clinic.staff', compact('doctors'));
        }
        else if($request->get('status')){
            $status = $request->get('status');

            $doctors = Doctor::where('status', 'LIKE', $status)
                ->where('clinic_id', Auth::user()->clinic_id)
                ->orderBy('created_at', 'desc')
                ->paginate(5);

            return view('clinic.staff', compact('doctors'));
        }
        else{
            $doctors = Doctor::where('clinic_id', Auth::user()->clinic_id)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->paginate(5);

            return view('clinic.staff', compact('doctors'));
        }
    }

    public function register_index(){
        $doctors = Doctor::paginate(10);

        return view('clinic.register-staff', compact('doctors'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|max:255',
            'ic' => 'required|unique:doctors',
            'address' => 'required',
            'contact' => 'required|max:255',
            'dob' => 'required',
            'clinic_id' => 'required',
        ]);

        $doctors = new Doctor();

        $doctors->name = Str::upper($request->input('name'));
        $doctors->ic = $request->input('ic');
        $doctors->address = Str::upper($request->input('address'));
        $doctors->contact = $request->input('contact');
        $doctors->dob = $request->input('dob');
        $doctors->clinic_id = $request->input('clinic_id');

        $doctors->save();

        return back()->with('message', 'Doctor added successfully.');
    }

    public function update(Request $request, $id){
        $doctors = Doctor::findorfail($id);

        $doctors->name = Str::upper($request->input('name'));
        $doctors->ic = $request->input('ic');
        $doctors->address = Str::upper($request->input('address'));
        $doctors->contact = $request->input('contact');
        $doctors->dob = $request->input('dob');
        $doctors->clinic_id = $request->input('clinic_id');
        $doctors->status = $request->input('status');

        $doctors->save();

        return back()->with('message', 'Doctor details updated successfully.');
    }

    public function delete($id){
        $doctors = Doctor::findorfail($id);

        $doctors->delete();

        return back()->with('message', 'Doctor deleted successfully');
    }
}
