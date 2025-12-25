<?php

namespace App\Http\Controllers;

use App\Models\AsetBarangModel;
use App\Models\AsetKategoriModel;
use App\Models\AsetKategoriSub2Model;
use App\Models\AsetKategoriSub3Model;
use App\Models\AsetKategoriSubModel;
use App\Models\AsetPerolehanItemModel;
use App\Models\AsetPerolehanModel;
use App\Models\AsetPerolehanRincianModel;
use App\Models\AsetTBidModel;
use App\Models\AsetTGolModel;
use App\Models\AsetTKelModel;
use App\Models\AsetTSKelModel;
use App\Models\AsetTSSKelModel;
use App\Models\AsetUbahModel;
use App\Models\BarangMasukFakultasModel;
use App\Models\BarangModel;
use App\Models\JenisSatuanModel;
use App\Models\KategoriModel;
use App\Models\KelompokModel;
use App\Models\SubKategoriModel;
use App\Models\SubSubKategoriModel;
use App\Models\T_brgModel;
use App\Models\TkelModel;
use App\Models\Tmapbmn12Model;
use App\Models\TMasteruModel;
use App\Models\TskelModel;
use App\Models\TSpmModel;
use App\Models\TsskelModel;
use Illuminate\Http\Request;

class Sinkronisasi extends Controller
{
    public function t_kel()
    {
        $query = TkelModel::where('kd_gol', '1')->where('kd_bid', '01')->get();
        foreach($query as $baris)
        {            
            $kd_bd = "$baris->kd_gol$baris->kd_bid";
            $kd_kt = "$kd_bd$baris->kd_kel";
            $no_kt = $baris->kd_kel;
            $nm_kt = $baris->ur_kel;

            $jumlah = KategoriModel::where('kd_kt', "$kd_bd$baris->kd_kel")->count();
            if($jumlah==0)
            {
                $data = new KategoriModel();
                $data->kd_bd = "$baris->kd_gol$baris->kd_bid"; 
                $data->kd_kt = "$kd_bd$baris->kd_kel"; 
                $data->no_kt = $baris->kd_kel; 
                $data->nm_kt = $baris->ur_kel;        
                $data->save();
            }
            echo "$kd_kt <br>";
            
        }
    }

    public function t_skel()
    {
        $query = TskelModel::where('kd_gol', '1')->where('kd_bid', '01')->get();
        foreach($query as $baris)
        {            
            $kd_kt = "$baris->kd_gol$baris->kd_bid$baris->kd_kel";
            $kd_skt = "$kd_kt$baris->kd_skel";
            $no_skt = $baris->kd_skel;
            $nm_skt = $baris->ur_skel;

            $jumlah = SubKategoriModel::where('kd_skt', "$kd_kt$baris->kd_skel")->count();
            if($jumlah==0)
            {
                $data = new SubKategoriModel();
                $data->kd_kt = "$baris->kd_gol$baris->kd_bid$baris->kd_kel"; 
                $data->kd_skt = "$kd_kt$baris->kd_skel"; 
                $data->no_skt = $no_skt; 
                $data->nm_skt = $nm_skt;        
                $data->save();
            }
            echo "$kd_skt <br>";
            
        }
    }

    public function t_sskel()
    {
        $query = TsskelModel::where('kd_gol', '1')->where('kd_bid', '01')->get();
        foreach($query as $baris)
        {            
            $kd_skt = "$baris->kd_gol$baris->kd_bid$baris->kd_kel$baris->kd_skel";
            $kd_sskt = "$kd_skt$baris->kd_sskel";

            $jumlah = SubSubKategoriModel::where('kd_skt', "$kd_skt$baris->kd_sskel")->count();
            if($jumlah==0)
            {
                $data = new SubSubKategoriModel();
                $data->kd_skt = "$baris->kd_gol$baris->kd_bid$baris->kd_kel$baris->kd_skel"; 
                $data->kd_sskt = "$kd_skt$baris->kd_sskel"; 
                $data->no_sskt = $baris->kd_sskel; 
                $data->nm_sskt = $baris->ur_sskel;
                $data->kd_kl = $baris->kdperkbr;        
                $data->save();
            }
            echo "$kd_sskt <br>";
            
        }
    }

    public function t_mapbmn12()
    {
        $query = T_brgModel::get();
        foreach($query as $baris)
        {            
            $jumlah = BarangModel::where('kd_kl', "$baris->KDPERK6B")->count();
            if($jumlah==0)
            {
                $data = new KelompokModel();
                $data->kd_kl = $baris->KDPERK6B; 
                $data->kd_kl2 = $baris->KDPERK6L; 
                $data->nm_kl = $baris->NMPERK6B;       
                $data->save();
            }
            echo "$baris->KDPERK6B <br>";
            
        }
    }

    public function t_brg()
    {
        $query = TsskelModel::get();
        foreach($query as $baris)
        {   
            $query2 = T_brgModel::select('kd_kbrg', 'kd_brg', 'ur_brg', 'kd_perk', 'kd_jbrg', 'satuan', 'kd_lokasi')
            ->where('kd_kbrg','=', $baris->kd_brg)->groupby('kd_kbrg','kd_brg', 'ur_brg', 'kd_perk', 'kd_jbrg', 'satuan', 'kd_lokasi')->get();
            foreach($query2 as $baris2)
            {
                $jumlah = BarangModel::where('kd_brg', "$baris2->kd_brg")->count();
                if($jumlah==0)
                {
                    $queryjenis = JenisSatuanModel::where('nm_js',$baris2->satuan)->first();

                    $data = new BarangModel();
                    $data->kd_brg = $baris2->kd_brg; 
                    $data->kd_sskt = "$baris2->kd_kbrg$baris->kd_jbrg"; 
                    $data->no_brg = $baris2->kd_jbrg; 
                    $data->nm_brg = trim($baris2->ur_brg); 
                    $data->id_js = $queryjenis->id;      
                    $data->save();
                    echo "$baris2->kd_kbrg = $baris2->satuan <br>";
                }
                
            }
        }
    }

    public function stok_barang()
    {
        $query = BarangMasukFakultasModel::orderby('kd_brg')->get();
        foreach($query as $baris)
        {  
            $jmlh = BarangModel::where('kd_brg',$baris->kd_brg)->count();
            
            if($jmlh > 0)
            {
            //$databrg = BarangModel::where('kd_brg',$baris->kd_brg)->first();
            //$databrg->stok_brg = $databrg->stok_brg+$baris->jmlh_awal_bmf;
            //$databrg->nilai_brg = $databrg->nilai_brg + ($baris->jmlh_awal_bmf * $baris->hrg_bmf);
            //$databrg->save();
            }
            else
            {
                echo "$baris->id_bmf = $baris->kd_lks = $baris->kd_brg = $baris->jmlh_awal_bmf = $baris->jmlh_awal_bmf * $baris->hrg_bmf  <br>";
            }

           
        }
    }

    public function aset_kategori()
    {
        $query = AsetTGolModel::where('kd_gol','!=',1)->get();
        foreach($query as $baris)
        {   
            $jumlah = AsetKategoriModel::where('a_kd_kt', "$baris->kd_gol")->count();
            if($jumlah==0)
            {
                $data = new AsetKategoriModel();
                $data->a_kd_kt = $baris->kd_gol;
                $data->a_nm_kt = trim($baris->ur_gol);
                $data->user_id = 1;
                $data->save();
                echo "$baris->kd_gol = $baris->ur_gol <br>";
            }
        }
    }

    public function aset_kategori_sub()
    {
        $no=1;
        $query = AsetTBidModel::where('kd_gol','!=',1)->get();
        foreach($query as $baris)
        {   
            $jumlah = AsetKategoriSubModel::where('a_kd_kt_sub', "$baris->kd_bidbrg")->count();
            if($jumlah==0)
            {
                $data = new AsetKategoriSubModel();
                $data->a_kd_kt_sub = "$baris->kd_gol$baris->kd_bid";
                $data->a_kd_kt = $baris->kd_gol;
                $data->a_no_kt_sub = trim($baris->kd_bid);
                $data->a_nm_kt_sub = trim($baris->ur_bid);
                $data->user_id = 1;
                $data->save();
                echo "$no = $baris->kd_bidbrg = $baris->ur_bid <br>";
                $no++;
            }
        }
    }

    public function aset_kategori_sub_2()
    {
        $no=1;
        $query = AsetTKelModel::where('kd_gol','!=',1)->get();
        foreach($query as $baris)
        {   
            $jumlah = AsetKategoriSub2Model::where('a_kd_kt_sub_2', "$baris->kd_kelbrg")->count();
            if($jumlah==0)
            {
                $data = new AsetKategoriSub2Model();
                $data->a_kd_kt_sub_2 = "$baris->kd_gol$baris->kd_bid$baris->kd_kel";
                $data->a_kd_kt_sub = "$baris->kd_gol$baris->kd_bid";
                $data->a_no_kt_sub_2 = trim($baris->kd_kel);
                $data->a_nm_kt_sub_2 = trim($baris->ur_kel);
                $data->user_id = 1;
                $data->save();
                echo "$no = $baris->kd_kelbrg = $baris->ur_kel <br>";
                $no++;
            }
        }
    }

    public function aset_kategori_sub_3()
    {
        $no=1;
        $query = AsetTSKelModel::where('kd_gol','!=',1)->get();
        foreach($query as $baris)
        {   
            $jumlah = AsetKategoriSub3Model::where('a_kd_kt_sub_3', "$baris->kd_skelbrg")->count();
            if($jumlah==0)
            {
                $data = new AsetKategoriSub3Model();
                $data->a_kd_kt_sub_3 = "$baris->kd_gol$baris->kd_bid$baris->kd_kel$baris->kd_skel";
                $data->a_kd_kt_sub_2 = "$baris->kd_gol$baris->kd_bid$baris->kd_kel";
                $data->a_no_kt_sub_3 = trim($baris->kd_skel);
                $data->a_nm_kt_sub_3 = trim($baris->ur_skel);
                $data->user_id = 1;
                $data->save();
                echo "$no = $baris->kd_skelbrg = $baris->ur_skel <br>";
                $no++;
            }
        }
    }

    public function aset_barang()
    {
        $no=1;
        $query = AsetTSSKelModel::where('kd_gol','!=',1)->get();
        foreach($query as $baris)
        {   
            $jumlah = AsetBarangModel::where('a_kd_brg', "$baris->kd_brg")->count();
            if($jumlah==0)
            {
                $data = new AsetBarangModel();
                $data->a_kd_brg = "$baris->kd_gol$baris->kd_bid$baris->kd_kel$baris->kd_skel$baris->kd_sskel";
                $data->a_kd_kt_sub_3 = "$baris->kd_gol$baris->kd_bid$baris->kd_kel$baris->kd_skel";
                $data->a_no_brg = trim($baris->kd_sskel);
                $data->a_nm_brg = trim($baris->ur_sskel);
                $data->user_id = 1;
                $data->save();
                echo "$no = $baris->kd_brg = $baris->ur_sskel <br>";
                $no++;
            }
        }
    }

    public function aset_perolehan()
    {
        $no=1;
        $query = TMasteruModel::where('kd_lokasi',"023170800677513019KD")->orderBy('thn_ang')->orderBy('no_sppa')->orderBy('no_aset')->get();
        foreach($query as $baris)
        {   
            $a_kd_ajkt = substr($baris->no_sppa,0,3);
            if($a_kd_ajkt == "A02")
            {
                $a_kd_brg_api = "$baris->kd_lokas$baris->kd_brg$baris->no_aset";
                $jumlah = AsetPerolehanModel::where('a_kd_al','023170800677513019KD')->where('a_nosppa_ap', "$baris->no_sppa")->count();
                if($jumlah==0)
                {
                    $datapembelian = new AsetPerolehanModel();
                    $datapembelian->a_kd_al = $baris->kd_lokasi;
                    $datapembelian->a_kd_ajkt = $a_kd_ajkt;
                    $datapembelian->a_kd_brg = $baris->kd_brg;
                    $datapembelian->a_nosppa_ap = $baris->no_sppa;
                    $datapembelian->a_tgl_perolehan_ap = $baris->tgl_perlh;
                    $datapembelian->a_tgl_buku_ap = $baris->tgl_buku;
                    $datapembelian->a_kuantitas_ap = $baris->kuantitas;
                    $datapembelian->a_nilai_ap = $baris->rph_sat;
                    $datapembelian->a_asal_ap = $baris->asal_perlh;
                    $datapembelian->a_no_bukti_ap = $baris->no_bukti;
                    $datapembelian->a_merk_ap = $baris->merk_type;
                    $datapembelian->a_ket_ap = $baris->keterangan;
                    $datapembelian->a_dsr_hrg_ap = $baris->dsr_hrg;
                    $datapembelian->a_tercatat_ap = $baris->tercatat;
                    $datapembelian->a_thn_ap = $baris->thn_ang;
                    $datapembelian->a_periode_ap = $baris->periode;
                    $datapembelian->a_tgl_bast_ap = $baris->tgl_dsr_mts;
                    $datapembelian->a_tgl_bast_ap = $baris->tgl_dsr_mts;
                    $datapembelian->user_id = 1;
                    $datapembelian->save();
                    $a_id_ap = $datapembelian->a_id_ap;
                    echo "$no = $a_kd_ajkt = $baris->no_sppa = $baris->kd_brg = $baris->no_aset <br>";
                    $no++;
                }
                
                $jumlah = AsetPerolehanItemModel::where('a_kd_brg_api', "$baris->no_sppa")->count();
                if($jumlah==0)
                {
                    $data = new AsetPerolehanItemModel();
                    $data->a_id_ap = $a_id_ap;
                    $data->a_kd_brg_api = $a_kd_brg_api;
                    $data->a_no_api = $baris->no_aset;
                    $data->a_kuantitas_api = $baris->kuantitas;
                    $data->a_nilai_api = $baris->rph_sat;
                    $data->a_kondisi_api = $baris->kondisi;
                    $data->a_status_api = 1;
                    $data->user_id = 1;
                    $data->save();
                    echo "$no = $a_kd_ajkt = $baris->no_sppa = $baris->kd_brg = $baris->no_aset <br>";
                    $no++;
                }
            }
        }
    }
    public function aset_perolehan_spm()
    {
        $no=1;
        $query = TSpmModel::where('kd_lokasi',"023170800677513019KD")->orderBy('thn_ang')->orderBy('no_sppa')->get();
        foreach($query as $baris)
        {   
            $a_kd_ajkt = substr($baris->no_sppa,0,3);
            if($a_kd_ajkt == "A02")
            {
                $bariscek = AsetPerolehanModel::where('a_kd_al',"023170800677513019KD")->where('a_nosppa_ap', "$baris->no_sppa")->first();
            
                if($baris->no_sp2d > 0)
                {
                    $jumlah = AsetPerolehanRincianModel::where('a_id_ap', "$bariscek->a_id_ap")->where('a_no_sp2d', "$baris->no_sp2d")->count();
                    if($jumlah==0)
                    {
                        $data = new AsetPerolehanRincianModel();
                        $data->a_id_ap = $bariscek->a_id_ap;
                        $data->a_no_sp2d = $baris->no_sp2d;
                        $data->a_tgl_sp2d = $baris->tgl_sp2d;
                        $data->a_kl_belanja_apr = $baris->bkpk;
                        $data->a_nilai_spm = $baris->jml_spm;
                        $data->user_id = 1;
                        $data->save();
                        echo "$no = $baris->no_sppa = $baris->no_sp2d <br>";
                        $no++;
                    }
                }

                $bariscekasetawal = AsetPerolehanItemModel::where('a_id_ap',$bariscek->a_id_ap)->orderBy('a_no_api','asc')->first();

                $barisapawal = AsetPerolehanModel::where('a_id_ap',$bariscek->a_id_ap)->first();
                $barisapawal->a_no_awal_ap = $bariscekasetawal->a_no_api;
                $barisapawal->save();

                $bariscekasetakhir = AsetPerolehanItemModel::where('a_id_ap',$bariscek->a_id_ap)->orderBy('a_no_api','desc')->first();

                $barisapakhir = AsetPerolehanModel::where('a_id_ap',$bariscek->a_id_ap)->first();
                $barisapakhir->a_no_akhir_ap = $bariscekasetakhir->a_no_api;
                $barisapakhir->save();

                $a_jmlh_ap = ($bariscekasetakhir->a_no_api - $bariscekasetawal->a_no_api) + 1;

                $baristotal = AsetPerolehanModel::where('a_id_ap',$bariscek->a_id_ap)->first();
                $baristotal->a_jmlh_ap = $a_jmlh_ap;
                $baristotal->save();

                echo "$no = $bariscek->a_id_ap = $bariscekasetawal->a_no_api = $bariscekasetakhir->a_no_api <br>";
            }
        }
    }

    public function aset_ubah()
    {
        $no=1;
        $query = TMasteruModel::where('kd_lokasi',"023170800677513001KD")->orderBy('thn_ang')->orderBy('no_sppa')->orderBy('no_aset')->get();
        foreach($query as $baris)
        {   
            $a_kd_ajkt = substr($baris->no_sppa,0,3);
            if($a_kd_ajkt == "B03")
            {
                $a_kd_brg_api = "$baris->kd_lokas$baris->kd_brg$baris->no_aset";
                $jumlah = AsetUbahModel::where('a_nosppa_au', "$baris->no_sppa")->count();
                if($jumlah==0)
                {
                    $datapembelian = new AsetUbahModel();
                    $datapembelian->a_kd_al = $baris->kd_lokasi;
                    $datapembelian->a_kd_ajkt = $a_kd_ajkt;
                    $datapembelian->a_kd_brg = $baris->kd_brg;
                    $datapembelian->a_nosppa_au = $baris->no_sppa;
                    $datapembelian->a_tgl_buku_au = $baris->tgl_buku;
                    $datapembelian->a_kuantitas_au = $baris->kuantitas;
                    $datapembelian->a_nilai_au = $baris->rph_sat;
                    $datapembelian->a_no_dasar_koreksi_au = $baris->no_dsr_mts;
                    $datapembelian->a_tgl_dasar_koreksi_au = $baris->tgl_dsr_mts;
                    $datapembelian->a_thn_ap = $baris->thn_ang;
                    $datapembelian->a_periode_ap = $baris->periode;
                    $datapembelian->a_tgl_bast_ap = $baris->tgl_dsr_mts;
                    $datapembelian->a_tgl_bast_ap = $baris->tgl_dsr_mts;
                    $datapembelian->user_id = 1;
                    $datapembelian->save();
                    $a_id_ap = $datapembelian->a_id_ap;
                    echo "$no = $a_kd_ajkt = $baris->no_sppa = $baris->kd_brg = $baris->no_aset <br>";
                    $no++;
                }
                
                $jumlah = AsetPerolehanItemModel::where('a_kd_brg_api', "$baris->no_sppa")->count();
                if($jumlah==0)
                {
                    $data = new AsetPerolehanItemModel();
                    $data->a_id_ap = $a_id_ap;
                    $data->a_kd_brg_api = $a_kd_brg_api;
                    $data->a_no_api = $baris->no_aset;
                    $data->a_kuantitas_api = $baris->kuantitas;
                    $data->a_nilai_api = $baris->rph_sat;
                    $data->a_kondisi_api = $baris->kondisi;
                    $data->a_status_api = 1;
                    $data->user_id = 1;
                    $data->save();
                    echo "$no = $a_kd_ajkt = $baris->no_sppa = $baris->kd_brg = $baris->no_aset <br>";
                    $no++;
                }
            }
        }
    }
}