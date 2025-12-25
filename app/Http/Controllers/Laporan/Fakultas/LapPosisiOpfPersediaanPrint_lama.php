<?php
namespace App\Http\Controllers\Laporan\Fakultas;
use App\Http\Controllers\Controller;
use App\Models\BarangKeluarDetailModel;
use App\Models\BarangKeluarFakultasDetailModel;
use App\Models\BarangMasukFakultasDetailModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangMasukModel;
use App\Models\JabpenfkModel;
use App\Models\LokasiModel;
use App\Models\OpfkdetitmModel;
use App\Models\OpsikFkDetModel;
use App\Models\TempBarangMasukModel;
use App\Models\User;
use App\Models\VLapPosisi4Model;
use Illuminate\Support\Facades\Crypt;
use PDF;

class LapPosisiOpfPersediaanPrint extends Controller
{
    Public Function index($filter)
    {
        $tgl_akhir = Crypt::decryptString($filter);
        $user_id = auth()->user()->id;
        $barislok = User::
        join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
        ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
        ->where('users.id', $user_id)
        ->first();

        $datafakultas = User::join('fakultas_jabatan','users.id_fkj','=','fakultas_jabatan.id_fkj')
            ->join('fakultas','fakultas_jabatan.id_fk','=','fakultas.id_fk')
            ->where('users.id', $user_id)->first();  

        $lokasi = $barislok->kd_lks;
        $id_fk = $barislok->id_fk;
        $datalokasi = LokasiModel::where('kd_lks', $lokasi)->first();

        $pejabatanpimpinan = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 1)->first();
        $pejabatanop = JabpenfkModel::join('jabatan_fakultas','jabatan_pengesahan_fakultas.id_jabfk','=','jabatan_fakultas.id_jabfk')->where('id_fk', $datafakultas->id_fk)->where('jabatan_pengesahan_fakultas.id_jabfk', 2)->first();

        $jumlah = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1')->count();
        if($jumlah != 0)
        {
        $datadeletetbm = TempBarangMasukModel::where('user_id', $user_id)->where('jns_tbm','=','1');
        $datadeletetbm->delete();  
        }
        $databarangmasukfakultas = BarangMasukFakultasModel::
        where('kd_lks', '=', $lokasi )
        ->where('tglperolehan_bmf', '<=', $tgl_akhir )
        ->orderBy('tglperolehan_bmf','asc')
        ->get();
        foreach($databarangmasukfakultas as $barisbmf)
        {
            if($barisbmf->sisa_bmf==$barisbmf->jmlh_awal_bmf)
            {
                $datatbmf = new TempBarangMasukModel();                    
                $datatbmf->kd_brg = $barisbmf->kd_brg;
                $datatbmf->sisa_tbm = $barisbmf->jmlh_awal_bmf;
                $datatbmf->hrg_tbm = $barisbmf->hrg_bmf;
                $datatbmf->kd_lks = $lokasi;
                $datatbmf->user_id = $user_id;
                $datatbmf->jns_tbm = 1;
                $datatbmf->save();
            }
            else
            {
                $tjmlh_bkfd = 0;
                $databarangkeluar = BarangKeluarFakultasDetailModel::
                join('barang_keluar_fakultas','barang_keluar_fakultas_detail.id_bkf','=','barang_keluar_fakultas.id_bkf')
                ->where('id_bmf', '=', $barisbmf->id_bmf)
                ->where('tglambil_bkf', '<=', $tgl_akhir )
                ->get();
                foreach($databarangkeluar as $barisbkfd)                    
                {
                    $tjmlh_bkfd = $barisbkfd->jmlh_bkfd + $tjmlh_bkfd;
                }
                $jmlh_awal_bmf = $barisbmf->jmlh_awal_bmf;
                //$diambil = $barisbmf->sisa_bmf + $tjmlh_bkfd;

                /*if($barisbmf->sisa_bmf!=$barisbmf->jmlh_awal_bmf)
                {
                    $tjmlh_opsikkurang = 0;
                    $databarangopsikkurang = OpfkdetitmModel::
                    join('opsik_fakultas_detail','opfik_fakultas_detail_item.id_opfkdet','=','opsik_fakultas_detail.id_opfkdet')
                    ->join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                    ->where('id_bmf', '=', $barisbmf->id_bmf)
                    ->where('tgl_opfk', '<=', $tgl_akhir )
                    ->where('id_bkfd', '<', '1' )
                    ->get();
                    foreach($databarangopsikkurang as $barisopsikkurang)                    
                    {
                        $tjmlh_opsikkurang = $barisopsikkurang->jmlh_opfkdetitm + $tjmlh_opsikkurang;
                    }
                }

                $tjmlh_opsiktambah = 0;
                if($barisbmf->sisa_bmf!=$barisbmf->jmlh_awal_bmf)
                {
                    $databarangopsiktambah = OpfkdetitmModel::
                    join('opsik_fakultas_detail','opfik_fakultas_detail_item.id_opfkdet','=','opsik_fakultas_detail.id_opfkdet')
                    ->join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                    ->where('id_bmf', '=', $barisbmf->id_bmf)
                    ->where('tgl_opfk', '<=', $tgl_akhir )
                    ->where('id_bkfd', '>', 0 )
                    ->get();
                    foreach($databarangopsiktambah as $barisopsiktambah)                    
                    {
                        $tjmlh_opsiktambah = $barisopsiktambah->jmlh_opfkdetitm + $tjmlh_opsiktambah;
                    }
                }

                $sisa_tbmf = ($jmlh_awal_bmf - $tjmlh_bkfd) + $tjmlh_opsiktambah - $tjmlh_opsikkurang;*/

                $tjmlh_opsik = 0;
                if($barisbmf->sisa_bmf!=$barisbmf->jmlh_awal_bmf)
                {                    
                    $jumlah = OpsikFkDetModel::                    
                    join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                    ->where('id_fk', '=', $id_fk)
                    ->where('kd_brg', '=', $barisbmf->kd_brg)
                    ->where('tgl_opfk', '<=', $tgl_akhir )
                    ->where('status_opfk', '=', 1 )
                    ->count();
                    if($jumlah != 0)
                    {
                        $jmlh_bm = BarangMasukFakultasModel::
                        where('kd_lks', '=', $lokasi )
                        ->where('kd_brg', '=', $barisbmf->kd_brg )
                        ->where('tglperolehan_bmf', '<=', $tgl_akhir )
                        ->orderBy('tglperolehan_bmf','asc')
                        ->count();

                        $databarangopsik = OpsikFkDetModel::                    
                        join('opsik_fakultas','opsik_fakultas_detail.id_opfk','=','opsik_fakultas.id_opfk')
                        ->where('id_fk', '=', $id_fk)
                        ->where('kd_brg', '=', $barisbmf->kd_brg)
                        ->where('tgl_opfk', '<=', $tgl_akhir )
                        ->first();
                        $stok_sistem_opfkdet = $databarangopsik->stok_sistem_opfkdet;
                        $stok_opsik_opfkdet = $databarangopsik->stok_opsik_opfkdet;
                        if($stok_sistem_opfkdet < $stok_opsik_opfkdet)
                        {
                            $tjmlh_opsik = $stok_opsik_opfkdet - $stok_sistem_opfkdet;
                        }
                        else if($stok_sistem_opfkdet > $stok_opsik_opfkdet)
                        {
                            $tjmlh_opsik =  $stok_opsik_opfkdet;
                        }
                        $tjmlh_opsik = $tjmlh_opsik / $jmlh_bm;
                    }
                }
                
                if($jumlah==0)
                {
                    $sisa_tbmf = ($jmlh_awal_bmf - $tjmlh_bkfd) + $tjmlh_opsik;
                }
                else
                {
                    $sisa_tbmf = $tjmlh_opsik;
                }

                //echo "$jmlh_awal_bmf = $tjmlh_bkfd = $tjmlh_opsik<br>";

                $datatbmf = new TempBarangMasukModel();                    
                $datatbmf->kd_brg = $barisbmf->kd_brg;
                $datatbmf->sisa_tbm = $sisa_tbmf;
                $datatbmf->hrg_tbm = $barisbmf->hrg_bmf;
                $datatbmf->kd_lks = $lokasi;
                $datatbmf->user_id = $user_id;
                $datatbmf->jns_tbm = 1;
                $datatbmf->save();
            }
        }

        function rupiah($angka){
	
            $hasil_rupiah = number_format($angka,0,',','.');
            return $hasil_rupiah;
         
        }
   
        $tahunanggaran = substr($tgl_akhir, 0, 4);
        PDF::SetTitle('Laporan Posisi Persedian Di Neraca');
        PDF::AddPage();
        PDF::SetFont('times', '', 8);
        $tgl = \Carbon\Carbon::parse($tgl_akhir)->locale('id')->isoFormat('D MMMM Y');
        $tgl = strtoupper($tgl);

        PDF::SetFont('times', 'b', 14);
        PDF::Cell(0, 0, 'LAPORAN POSISI PERSEDIAN DI NERACA', 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', 'b', 10);
        PDF::Cell(0, 0, "UNTUK PERIODE YANG BERAKHIR TANGGAL $tgl", 0, 1, 'C', 0, '', 0);
        PDF::Cell(0, 0, "TAHUN ANGGARAN $tahunanggaran", 0, 1, 'C', 0, '', 0);
        PDF::SetFont('times', '', 10);
        
        PDF::ln(5);
        PDF::Cell(28, 0, "UAPKB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$datalokasi->nm_lks", 0, 1, 'L', 0, '', true);
        PDF::ln(0);
        PDF::Cell(28, 0, "Kode UAPKPB", 0, 0, 'L', 0, '', true);
        PDF::Cell(5, 0, ": ", 0, 0, 'C', 0, '', true);
        PDF::Cell(42, 0, "$lokasi", 0, 1, 'L', 0, '', true);

        PDF::SetFont('times', 'b', 10);
        PDF::ln(5);
        PDF::Cell(28, 0, "KODE", 1, 0, 'C', 0, '', true);
        PDF::Cell(120, 0, "URAIAN", 1, 0, 'C', 0, '', true);
        PDF::Cell(40, 0, "NILAI", 1, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::SetFont('times', '', 10);
        //if($lokasi == "023170800677513009KD")
        //{            
            $total_nilai = 0;
            $datalap = VLapPosisi4Model::
            join('kategori','v_lap_posisi4.v_kd_kt','=','kategori.kd_kt')
            ->where('v_lap_posisi4.v_kd_lks','=',$lokasi)
            ->where('v_lap_posisi4.user_id','=',$user_id)
            ->where('v_lap_posisi4.v_jns_tbm','=',1)
            ->get();
            foreach($datalap as $barislap)
            {
                $nilairp = rupiah($barislap->total_nilai);
                PDF::Cell(28, 0, "$barislap->kd_kt", 1, 0, 'C', 0, '', true);
                PDF::Cell(120, 0, "$barislap->nm_kt", 1, 0, 'L', 0, '', true);
                PDF::Cell(40, 0, "$nilairp", 1, 1, 'R', 0, '', true);
                PDF::ln(0);         
                $total_nilai = $total_nilai + $barislap->total_nilai;
            }
            $total_nilai2 = rupiah($total_nilai);
            PDF::SetFont('times', 'b', 10);
            PDF::Cell(28, 0, "", 1, 0, 'C', 0, '', true);
            PDF::Cell(120, 0, "Jumlah", 1, 0, 'R', 0, '', true);
            PDF::Cell(40, 0, "$total_nilai2", 1, 1, 'R', 0, '', true);
        //}
        //else if($lokasi == "023170800677513000KD")
        //{

        //}
        //else
        //{

        //}
        $tgl = ucwords(strtolower($tgl));
        PDF::SetFont('times', '', 10);
        PDF::ln(10);
        PDF::Cell(60, 0, "Disetujui tanggal: $tgl", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "", 0, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabfk", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "$pejabatanop->nm_jabfk", 0, 1, 'C', 0, '', true);
        PDF::ln(20);
        PDF::Cell(60, 0, "$pejabatanpimpinan->nm_jabpenfk", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "$pejabatanop->nm_jabpenfk", 0, 1, 'C', 0, '', true);
        PDF::ln(0);
        PDF::Cell(60, 0, "NIP $pejabatanpimpinan->nik_jabpenfk", 0, 0, 'C', 0, '', true);
        PDF::Cell(60, 0, "", 0, 0, 'R', 0, '', true);
        PDF::Cell(60, 0, "NIP $pejabatanop->nik_jabpenfk", 0, 1, 'C', 0, '', true);

        PDF::Output('laporan_posisi_persedian_di_neraca.pdf');
    }
}
