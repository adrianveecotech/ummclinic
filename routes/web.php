<?php

use App\Http\Controllers\ClinicController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/', function () {   return redirect('/login');    });
Route::get('register', function () {   return redirect('/login');    });
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::post('change-password/{id}', [UserController::class, 'changePassword'])->name('password.change')->middleware('auth');

Route::get('/dashboard', function () {
    if(Auth::user()->type == 'Admin'){
        return redirect('/admin');
    }
    if(Auth::user()->type == 'clinic'){
        return redirect('/clinic');
    }   
    if(Auth::user()->type == 'company'){
        return redirect('/company');
    }
})->middleware('auth');

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('admin', [IndexController::class, 'admin_index']);
    Route::get('admin/profile', [UserController::class, 'admin_profile']);
    Route::get('admin/change-password', function () {   return view('admin.change_password');    });

    // Clinic Management
    Route::get('admin/clinic', [ClinicController::class, 'index']);
    Route::get('admin/clinic/new', function () {   return view('admin.register_clinic');    });
    Route::post('admin/clinic/store', [ClinicController::class, 'store_clinic_details']);
    Route::put('admin/clinic/update/{id}', [ClinicController::class, 'update_clinic_details']);
    Route::delete('admin/clinic/delete/{id}', [ClinicController::class, 'delete_clinic_details']);

    // Company Management
    Route::get('admin/company', [CompanyController::class, 'index']);
    Route::get('admin/company/new', function () {   return view('admin.register_company');    });
    Route::post('admin/company/store', [CompanyController::class, 'store_company_details']);
    Route::put('admin/company/update/{id}', [CompanyController::class, 'update_company_details']);
    Route::delete('admin/company/delete/{id}', [CompanyController::class, 'delete_company_details']);

    // Patient Management
    Route::get('admin/employee', [EmployeeController::class, 'admin_employee_index']);
    Route::get('admin/consultation', [ConsultationController::class, 'admin_consultation_index']);
    Route::get('admin/payment-history', [PaymentController::class, 'payment_history_index']);
});

Route::middleware(['auth', 'is_clinic'])->group(function () {
    Route::get('clinic', [IndexController::class, 'clinic_index']);
    Route::get('clinic/profile', [UserController::class, 'clinic_profile']);
    Route::post('clinic/profile/update/{clinic_id}', [UserController::class, 'updateClinicProfile']);
    Route::get('clinic/change-password', function () {   return view('clinic.change_password');    });

    // Search and Consultation
    Route::get('clinic/search', [ConsultationController::class, 'search_patient']);
    Route::get('clinic/consultation', [ConsultationController::class, 'consultation_history']);
    Route::get('clinic/consult/{ic}', [ConsultationController::class, 'index']);
    Route::post('clinic/consult/store', [ConsultationController::class, 'store']);

    // Staff Management (Clinic)
    Route::get('clinic/staff', [DoctorController::class, 'index']);
    Route::get('clinic/staff/new', [DoctorController::class, 'register_index']);
    Route::post('clinic/staff/store', [DoctorController::class, 'store']);
    Route::put('clinic/staff/update/{id}', [DoctorController::class, 'update']);
    Route::delete('clinic/staff/delete/{id}', [DoctorController::class, 'delete']);

    // Payments
    Route::get('clinic/payment', [PaymentController::class, 'index']);
    Route::get('clinic/payment/outstanding-details', [PaymentController::class, 'outstanding_details']);
    Route::post('clinic/payment/billing/{payment_id}', [PaymentController::class, 'billing']);
    Route::post('clinic/multiple_billing/{company_id}', [PaymentController::class, 'multiple_billing']);
});

Route::middleware(['auth', 'is_company'])->group(function () {
    Route::get('company', [IndexController::class, 'company_index']);
    Route::get('company/profile', [UserController::class, 'company_profile']);
    Route::post('company/profile/update/{company_id}', [UserController::class, 'updateCompanyProfile']);
    Route::get('company/change-password', function () {   return view('company.change_password');    });

    // Employee Management
    Route::get('company/employee', [EmployeeController::class, 'index']);
    Route::get('company/employee/new', [EmployeeController::class, 'register_index']);
    Route::post('company/employee/store', [EmployeeController::class, 'store_employee_details']);
    Route::put('company/employee/update/{id}', [EmployeeController::class, 'update_employee_details']);
    Route::delete('company/employee/delete/{id}', [EmployeeController::class, 'delete_employee_details']);

    // Consultation Management
    Route::get('company/consultation', [ConsultationController::class, 'employee_consultation_report_index']);

    //Payment Management
    Route::get('company/payment', [PaymentController::class, 'company_payment']);
    Route::get('company/outstanding-details', [PaymentController::class, 'company_consultation_details']);
});