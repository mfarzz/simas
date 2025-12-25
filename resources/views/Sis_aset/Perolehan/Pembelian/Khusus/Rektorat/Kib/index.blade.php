@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Rincian KIB Pembelian</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Perolehan</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Pembelian Barang</a></li>
                            <li class="breadcrumb-item active">Rincian KIB</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Daftar KIB Pembelian</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="mt-3"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="box bt-3 border-info">
                              <div class="box-header">
                                <h4 class="box-title">Rincian</strong></h4>
                              </div>
                              <div class="box-body">
                                <p> Kategori : {{ $data_aset->a_nm_kt }} - {{ $data_aset->a_nm_kt_sub }} - {{ $data_aset->a_nm_kt_sub_2 }} - {{ $data_aset->a_nm_kt_sub_3 }}</p>
                                <p> Barang : {{ $data_aset->a_nm_brg }} </p>
                              </div>
                            </div>
                        </div>

                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table class="table table-bordered table-hover table-striped" id="data-datatable">
                                <x-judul-tabel-aset>
                                    <x-isi-judul-tabel namakolom="No" />
                                    <x-isi-judul-tabel namakolom="No KIB" />
                                    <x-isi-judul-tabel namakolom="Tanggal" />
                                    <x-isi-judul-tabel namakolom="Nilai" />
                                    <x-isi-judul-tabel-aset namakolom="" />
                                </x-judul-tabel-aset>
                            </table>
                        </div>
                        <a href="/perolehan-pembelian-opr-khusus-aset"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar pembelian"><button type="button" class="btn btn-success waves-effect waves-light"><i class="fas fa-reply"></i> Kembali Ke Daftar Pembelian </button></a>
                    </div>                    
                </div>
            </div>
        </div>

    </div>
</div>

<div  class="modal fade" id="data-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">pembelian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="a_id_aprk" id="a_id_aprk">
                    <input type="hidden" name="encripted_id" id="encripted_id" value="{{ $encripted_id }}">

                    <fieldset>
                        <legend>Unit Barang</legend>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Luas Bangunan</label>
                            <div class="col-sm-8">
                              <input type="text" id="luas_bangunan" name="luas_bangunan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Luas Bangunan" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Luas Dasar Bangunan</label>
                            <div class="col-sm-8">
                              <input type="text" id="luas_dasar_bangunan" name="luas_dasar_bangunan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Luas Dasar Bangunan" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah Lantai</label>
                            <div class="col-sm-8">
                              <input type="text" id="jumlah_lantai" name="jumlah_lantai" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah Lantai" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tipe</label>
                            <div class="col-sm-8">
                              <input type="text" id="tipe" name="tipe" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tipe" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tahun selesai dibangun</label>
                            <div class="col-sm-8">
                              <input type="number" id="tahun_selesai_dibangun" name="tahun_selesai_dibangun" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tahun selesai dibangun" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tahun selesai digunakan</label>
                            <div class="col-sm-8">
                              <input type="number" id="tahun_selesai_digunakan" name="tahun_selesai_digunakan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tahun selesai digunakan" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No IMB</label>
                            <div class="col-sm-8">
                              <input type="text" id="no_imb" name="no_imb" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan no imb" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal IMB</label>
                            <div class="col-sm-8">
                              <input type="date" id="tgl_imb" name="tgl_imb" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan tgl imb" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Lokasi</label>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Propinsi</label>
                            <div class="col-sm-8">
                                <select id="idProvinsi" name="idProvinsi" class="form-select form-control" aria-label="Default select example" >
                                    <option value=''>---Silahkan Pilih Provinsi---</option>
                                    @foreach ($data_provinsi as $baris )
                                        <option value={{ $baris->kd_rprov }}> {{ $baris->kd_rprov }} - {{ $baris->nm_rprov }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kota</label>
                            <div class="col-sm-8">
                                <select id="idKota" name="idKota" class="form-select form-control" aria-label="Default select example" required>
                                    <option value=''>---Silahkan Pilih Kota---</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kecamatan</label>
                            <div class="col-sm-8">
                              <input type="text" id="kecamatan" name="kecamatan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan kecamatan" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kelurahan</label>
                            <div class="col-sm-8">
                              <input type="text" id="kelurahan" name="kelurahan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan kelurahan" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">RT/RW</label>
                            <div class="col-sm-8">
                              <input type="text" id="rt" name="rt" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan RT/RW" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanda-tanda Batas Tanah</label>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Utara</label>
                            <div class="col-sm-8">
                              <input type="text" id="utara" name="utara" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan batas utara" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Selatan</label>
                            <div class="col-sm-8">
                              <input type="text" id="selatan" name="selatan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan batas selatan" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Barat</label>
                            <div class="col-sm-8">
                              <input type="text" id="barat" name="barat" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan batas barat" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Timur</label>
                            <div class="col-sm-8">
                              <input type="text" id="timur" name="timur" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan batas timur" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Dok Kepemilikan</label>
                            <div class="col-sm-8">
                                <select id="idDokkepemilikan" name="idDokkepemilikan" class="form-select form-control" aria-label="Default select example" >
                                    <option value=''>---Silahkan Pilih Dok Kepemilikan---</option>
                                    @foreach ($data_dok_kepemilikan as $baris )
                                        <option value={{ $baris->a_kd_ml }}> {{ $baris->a_kd_ml }} - {{ $baris->a_nm_ml }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No Sertifikat</label>
                            <div class="col-sm-8">
                              <input type="text" id="no_sertifikat" name="no_sertifikat" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan nomor sertifikat" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Sertifikat</label>
                            <div class="col-sm-8">
                              <input type="date" id="tgl_sertifikat" name="tgl_sertifikat" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan tanggal sertikat" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Instansi Penerbit</label>
                            <div class="col-sm-8">
                              <input type="date" id="intansi_sertifikat" name="instansi_sertifikat" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan tanggal sertikat" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Status Penggunaan</label>
                            <div class="col-sm-8">
                                <select id="idStatuspenggunaan" name="idStatuspenggunaan" class="form-select form-control" aria-label="Default select example" >
                                    <option value=''>---Silahkan Pilih Status Penggunaan---</option>
                                    @foreach ($data_status_digunakan as $baris )
                                        <option value={{ $baris->a_kd_asd }}> {{ $baris->a_kd_asd }} - {{ $baris->a_nm_asd }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    
        
                    <div class="col-sm-offset-2 col-sm-10"><br/>
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">Kembali</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<script>
$('#idProvinsi').change(function(){    
    var prov = $(this).val();    
    if(prov){
        $.ajax({
           type:"GET",
           url:"/perolehan-pembelian-opr-khusus-aset-kib?kota="+prov,
           dataType: 'JSON',
           success:function(res){               
            if(res){
                $("#idKota").empty();
                $("#idKota").append("<option value=''>---Silahkan Pilih Kota---</option>");
                $.each(res,function(nm_rkot,kd_rkot){
                    $("#idKota").append('<option value="'+kd_rkot+'">'+kd_rkot+' - '+nm_rkot+'</option>');
                });
            }else{
               $("#idKota").empty();
            }
           }
        });
    }else{
        $("#idKota").empty();
    }
});
$(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#data-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('perolehan-pembelian-opr-khusus-aset-kib', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'a_no_aprk', name: 'a_no_aprk' },
            {
                data: 'a_tgl_perolehan_ap',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },
            {
                data: 'a_nilai_api',
                "render": function(data) {
                    return '<div class="right-cell">' + formatRupiah(data) + '</div>';
                }
            },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.a_id_aprk + ')" data-original-title="Edit" class="edit btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-check fa-sm"></i></a> ';
                    return buttons;
                }
            },
        ],
        order: [[0, 'desc']]
    });
});
     
function editFunc(a_id_apr){
    $.ajax({
        type:"POST", 
        url: "{{ url('perolehan-pembelian-opr-khusus-aset-rincian-edit') }}",
        data: { a_id_apr : a_id_apr },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Proses Data");
            $('#data-modal').modal('show');
            $('#a_id_apr').val(res.a_id_apr);
            $('#no_sp2d').val(res.a_luas_tanah_aprk);
            $('#tgl_sp2d').val(res.a_tgl_sp2d);
            $('#kel_belanja').val(res.a_kl_belanja_apr);
            $('#nilai_spm').val(res.a_nilai_spm);
        }
    });
}
 
$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('perolehan-pembelian-opr-khusus-aset-rincian-store')}}",
        data: formData,
        cache:false,
        contentType: false,
        processData: false,
        success: (data) => {  
            if (data.status === 1) {      
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showSaveMessage('Data berhasil disimpan');
            }
            else if (data.status === 4) {      
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showSaveMessage('Data berhasil diubah');
            }
            else if (data.status === 2) { 
                showDangerMessage('Data gagal disimpan. Kode atau nama tidak boleh sama');
            }
            else if (data.status === 3) { 
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showWarningMessage('Tidak ada data yang diubah. Kode atau nama tidak boleh sama');
            }
            else if (data.status === 11) { 
                showDangerMessage('kode tidak boleh sama');
            }
            else if (data.status === 12) { 
                showDangerMessage('No SP2D tidak boleh sama');
            }
            else
            {
                showDangerMessage('Data gagal disimpan');
            }
        },
        error: function(data){
            console.log(data);
        }
    });
});
</script>
@endsection