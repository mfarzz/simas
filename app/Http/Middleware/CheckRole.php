<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
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
            if($role == '1dan2' &&  (auth()->user()->role_id == 1 or auth()->user()->role_id == 2))
            {
                return $next($request);
            }
            elseif($role == '5dan6dan13' &&  (auth()->user()->role_id == 5 or auth()->user()->role_id == 6 or auth()->user()->role_id == 13 ))
            {
                return $next($request);
            }
            elseif($role == '1dan9dan12dan14' &&  (auth()->user()->role_id == 1 or auth()->user()->role_id == 9 or auth()->user()->role_id == 12 or auth()->user()->role_id == 14))
            {
                return $next($request);
            }
            elseif($role == '7dan8' &&  (auth()->user()->role_id == 7 or auth()->user()->role_id == 5 or auth()->user()->role_id == 8))
            {
                return $next($request);
            }
            elseif($role == 'superadmin' &&  (auth()->user()->role_id == 1 ))
            {
                return $next($request);
            }
            elseif($role == 'pengadaan' &&  (auth()->user()->role_id == 2 ))
            {
                return $next($request);
            }
            elseif($role == 'subdit' &&  (auth()->user()->role_id == 4 ))
            {
                return $next($request);
            }
            elseif($role == 'oprektorat' &&  (auth()->user()->role_id == 5 ))
            {
                return $next($request);
            }
            elseif($role == 'pimpinanrektorat' &&  (auth()->user()->role_id == 6 ))
            {
                return $next($request);
            }
            elseif($role == 'opfakultas' &&  (auth()->user()->role_id == 7 ))
            {
                return $next($request);
            }
            elseif($role == 'pimfakultas' &&  (auth()->user()->role_id == 8 ))
            {
                return $next($request);
            }
            elseif($role == 'subditakuntansi' &&  (auth()->user()->role_id == 9 ))
            {
                return $next($request);
            }
            elseif($role == 'oprumahsakit' &&  (auth()->user()->role_id == 10 ))
            {
                return $next($request);
            }
            elseif($role == 'kasilogistik' &&  (auth()->user()->role_id == 12 ))
            {
                return $next($request);
            }
            elseif($role == 'pimunitrektorat' &&  (auth()->user()->role_id == 13 ))
            {
                return $next($request);
            }
            elseif($role == 'kepalagudang' &&  (auth()->user()->role_id == 14 ))
            {
                return $next($request);
            }


            else
            {
                abort(code:403);
            }

            /*if($role == 'lpti' && auth()->user()->role_id != 1)
            {
                abort(code:403);
            }
            if($role == 'wr2' && auth()->user()->role_id != 2)
            {
                abort(code:403);
            }            
            if($role == 'rektor' && auth()->user()->role_id != 3)
            {
                abort(code:403);
            }*/           
        }
        else{
            abort(code:403);
        }
        return $next($request);
    }
}
