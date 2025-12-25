<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Profile extends Controller
{
    public function index()
    {   
        $id_user = auth()->user()->id;        
        $data = User::where('id','=',$id_user)->first();
        return view('akun.index',['data'=>$data]);
    }

    public function store(Request $request)
    {
        $id_user = auth()->user()->id;
        $password_lama = $request->password_lama;
        $password_baru = $request->password_baru;
        $konfirmasi_password_baru = $request->konfirmasi_password_baru;
        $gagal = 0;
        if($password_lama != '' or $password_baru != '' or $konfirmasi_password_baru != '')
        {
            $request->validate([
                'password_lama' =>'required'
            ]);
            $data = User::where('id',$id_user)->first();
            $password = Hash::check($request->password_lama, $data->password);
            if($password != 1)
            {
                $request->validate([
                    'password_lama' =>'confirmed'
                ]);
            }
            else
            {
                $request->validate([
                    'password_lama' =>'required',           
                    'password_baru' => 'min:6|required_with:konfirmasi_password_baru|same:konfirmasi_password_baru',
                    'konfirmasi_password_baru' => 'min:6'
                ]);    
                $password_baru = Hash::make($request->password_baru);
                $data->password = $password_baru;                
                $data->password_text = $request->password_baru;
                $data->save();                
                return redirect('/profile')->with('status', 'Password berhasil diubah');
            }
            $gagal=1;        
        }        
        if($gagal==0)
        {
            $file = $request->dokumen;        
            if($file!="")
            {
                $name_file = $file->hashName(); // Generate a unique, random name...
                $extension = $file->extension(); // Determine the file's extension based on the file's MIME type...        
                if($extension !== null)
                {            
                    if ($extension == "jpg" or $extension == "jpeg") {                
                        $request->file('dokumen')->store('public/akunprofile');
                        $data = User::where('id',$id_user)->first();
                        Storage::delete("public/akunprofile/$data->profile_photo_path");
                        $data->profile_photo_path = $name_file;
                        $data->save();                    
                    }
                    else
                    {
                        $request->validate([
                            'dokumen' =>'required|image:jpg,jpeg'
                        ]);
                    }
                }
                return redirect('/profile')->with('status', 'Foto berhasil diubah');
            }
            else
            {
                return redirect('/profile')->with('status', 'Tidak ada data yang diubah');
            }
        }                
        
    }
}
