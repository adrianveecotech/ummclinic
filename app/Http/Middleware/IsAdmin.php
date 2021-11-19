<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user()->type == 'Admin') {
            if(Auth::user()->status == 'active') {
                return $next($request);
            }
            else{
            Auth::logout();
            return back()->with('message', 'Inactive account. Please check with the Administrator.');
            }
        }

        return redirect('/dashboard');
    }
}
