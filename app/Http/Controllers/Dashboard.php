<?php

namespace App\Http\Controllers;

use App\Models\mhs_lulus;
use App\Models\periode_wisuda;
use App\Models\quisioner_isi;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    public function index()
    {
        $role_id = auth()->user()->role_id;
        $id_user = auth()->user()->id;
        $id_mahasiswa = auth()->user()->username;
        /*if($role_id==3)
        {
            $baris = mhs_lulus::where('id_mahasiswa', $id_mahasiswa)->first();
            $jumlah = periode_wisuda::where('id_perwis', $baris->id_perwis)->where('statusbuka_qui',1)->count();
            if($jumlah>=1)
            {
                $jumlah_isi = quisioner_isi::where('user_id', $id_user)->count();
                if($jumlah_isi==0)
                {
                    return redirect('/isi-quisioner-alumni')->with('status', 'Silahkan isi quisioner terlebih dahulu');        
                }                
                else
                {
                    return view('dashboard');
                }
            }
            else{
                return view('dashboard');    
            }
        }
        else
        {
            return view('dashboard');
        }   */     
    }
}
