<?php
namespace App\Http\Controllers\OpnameFisik\Universitas;

use App\Http\Controllers\Controller;
use App\Models\OpsikFakultasModel;
use Illuminate\Http\Request;
use Datatables;
use Illuminate\Support\Facades\Crypt;

class OpsikUvCt extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        if(request()->ajax()) {
            return datatables()->of(OpsikFakultasModel::
            join('fakultas','opsik_fakultas.id_fk','=','fakultas.id_fk')
            ->where('opsik_fakultas.status_opfk','=','1')
            ->get())
            ->addColumn('id_opfk', function ($data) {
                return $data->id_opfk; 
            })
            ->addColumn('id_opfk_en', function ($data) {
                $id_opfk_en = Crypt::encryptString($data->id_opfk);
                return $id_opfk_en; 
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('OpnameFisik.Universitas.index');
    }

    public function storeuploadcek(Request $request)
    {   
        $data = OpsikFakultasModel::where('id_opfk',$request->id_opfk)->first();
        return Response()->json($data);
    }
}