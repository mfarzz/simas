<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Datatables;

class BerandaCt extends Controller
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
        $id_ursj = auth()->user()->id_ursj;
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
        if($id_ursj>0)
        {
            $dataunitrumahsakit = User::join('unit_rumah_sakit_jabatan','users.id_ursj','=','unit_rumah_sakit_jabatan.id_ursj')
                ->join('unit_rumah_sakit','unit_rumah_sakit_jabatan.id_urs','=','unit_rumah_sakit.id_urs')
                ->where('users.id', $user_id)->first(); 
            $request->session()->put('posisi', $dataunitrumahsakit->nm_urs);
        }


        $pil_aplikasi = $request->pil_aplikasi;
        if ($pil_aplikasi=="inventaris") {
            $request->session()->put('pil_aplikasi', 'inventaris');
            return view('Beranda.index');
        }
        else if($pil_aplikasi=="aset")
        {
            return view('Sis_aset.Beranda.index');
        }
        else
        {
            $alert = 'Silahkan pilih terlebih dahulu anda ingin masuk ke sistem yang mana';
            return redirect()->route('dashboard')->with('alert', $alert);
        }
    }
}