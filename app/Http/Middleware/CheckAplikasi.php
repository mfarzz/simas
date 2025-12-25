<?php
namespace App\Http\Middleware;

use App\Http\Controllers\BerandaCt;
use Closure;

class CheckAplikasi
{
    public function handle($request, Closure $next, string $aplikasi)
    {
        // Mengambil data dari sesi
        $pil_aplikasi = $request->session()->get('pil_aplikasi');
        if($aplikasi == "aset" and $pil_aplikasi == "aset" and (auth()->user()->pengguna == 2 or auth()->user()->pengguna == 3))
        {
            return $next($request);
        }
        else if($aplikasi == "inventaris" and $pil_aplikasi == "inventaris" and (auth()->user()->pengguna == 1 or auth()->user()->pengguna == 3))
        {
            return $next($request);
        }
        else
        {
            abort(code:403);
        }
    }
}