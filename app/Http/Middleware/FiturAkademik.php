<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Fitur
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (Auth::check() == 1) {
            if($role == 'akademik' && (auth()->user()->role_id == 1 or auth()->user()->role_id == 2))
            {
                abort(code:403);
            }                 
        }
        else{
            abort(code:403);
        }
        return $next($request);
    }
}
