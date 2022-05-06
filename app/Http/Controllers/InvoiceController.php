<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if($request->get('search')){
            $search = $request->get('search');

            $invoices = Invoice::join('companys','companys.id','invoice.company_id')->select('invoice.*','companys.name')->where('companys.name', 'LIKE', '%'.$search.'%')->orwhere('invoice.date', 'LIKE', '%'.$search.'%')->paginate(10);
        }
        else{
           $invoices = Invoice::orderBy('created_at','desc')->paginate(10);
        }

            return view('company.invoice',compact('invoices'));
    }
}   