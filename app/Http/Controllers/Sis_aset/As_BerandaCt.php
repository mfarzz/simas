<?php

namespace App\Http\Controllers\Sis_aset;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;

class As_BerandaCt extends Controller
{
    public function index()
    {
        $alert = 'Silahkan pilih terlebih dahulu anda ingin masuk ke sistem yang mana';
        return redirect()->route('dashboard')->with('alert', $alert);
    }
    public function store(request $request)
    {
        $user_id = auth()->user()->id;
        $id_fkj = auth()->user()->id_fkj;
        $id_urj = auth()->user()->id_urj;
        if($id_fkj>0)
        {
            $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
                ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
                ->where('users.id', $user_id)->first(); 
            $request->session()->put('posisi', $datafakultas->nm_fk);
        }
        if($id_urj>0)
        {
            $dataunitrektorat = User::join('unit_rektorat_jabatan','users.id_urj','=','unit_rektorat_jabatan.id_urj')
                ->join('unit_rektorat','unit_rektorat_jabatan.id_ur','=','unit_rektorat.id_ur')
                ->where('users.id', $user_id)->first(); 
            $request->session()->put('posisi', $dataunitrektorat->nm_ur);
        }
        $pil_aplikasi = $request->pil_aplikasi;
        if ($pil_aplikasi=="inventaris") {            
            return view('Beranda.index');
        }
        else if($pil_aplikasi=="aset")
        {
            $request->session()->put('pil_aplikasi', 'aset');
            return view('Sis_aset.Beranda.index');
        }
        else
        {
            $alert = 'Silahkan pilih terlebih dahulu anda ingin masuk ke sistem yang mana';
            return redirect()->route('dashboard')->with('alert', $alert);
        }
    }
}