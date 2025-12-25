<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\KelompokModel;
use Datatables;
 
class EmployeeController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(Employee::get())
            ->addColumn('action', 'employee-action')
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('BarangMasuk.Khusus.index');
    }

    public function getKecamatan(Request $request){
        $kecamatan = KelompokModel::pluck('kd_kl','nm_kl');
        return response()->json($kecamatan);
    }
 
    public function store(Request $request)
    {  
  
        $employeeId = $request->id;
  
        $employee   =   Employee::updateOrCreate(
                    [
                     'id' => $employeeId
                    ],
                    [
                    'name' => $request->name, 
                    'email' => $request->email,
                    'address' => $request->address
                    ]);    
                          
        return Response()->json($employee);
    }
 
    public function edit(Request $request)
    {   
        $where = array('id' => $request->id);
        $employee  = Employee::where($where)->first();
       
        return Response()->json($employee);
    }
 
    public function destroy(Request $request)
    {
        $employee = Employee::where('id',$request->id)->delete();
       
        return Response()->json($employee);
    }
}