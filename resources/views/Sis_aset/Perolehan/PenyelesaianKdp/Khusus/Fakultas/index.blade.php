@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Penyelesaian Pembangunan dengan KDP</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Perolehan</a></li>
                            <li class="breadcrumb-item active">Penyelesaian Pembangunan dengan KDP</li>
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
                                <h4 class="card-title">Daftar Penyelesaian Pembangunan dengan KDP</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                <a onClick="add()" href="javascript:void(0)" title="Tambah Data" data-bs-toggle="modal" data-bs-target=".formInputModal">
                                    <button type="button" class="btn btn-info waves-effect btn-label waves-light">
                                        <i class="bx bx-add-to-queue label-icon"></i> Tambah Data
                                    </button>
                                </a>
                                <div class="mt-3"></div>
                            </div>
                        </div>
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table class="table table-bordered table-hover table-striped" id="data-datatable">
                                <x-judul-tabel-aset>
                                    <x-isi-judul-tabel-aset namakolom="No" />
                                    <x-isi-judul-tabel-aset namakolom="Kategori" />
                                    <x-isi-judul-tabel-aset namakolom="Nama Barang" />
                                    <x-isi-judul-tabel-aset namakolom="Jumlah Item" />
                                    <x-isi-judul-tabel-aset namakolom="No Urut" />
                                    <x-isi-judul-tabel-aset namakolom="Nilai Aset Per Item" />
                                    <x-isi-judul-tabel-aset namakolom="Tanggal Perolehan" />
                                    <x-isi-judul-tabel-aset namakolom="Tanggal Buku" />
                                    <x-isi-judul-tabel-aset namakolom="" />
                                </x-judul-tabel-aset>
                            </table>
                        </div>
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
                <h5 class="modal-title">Penyelesaian Pembangunan dengan KDP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="a_id_ap" id="a_id_ap">

                    <fieldset>
                        <legend>Rincian Aset</legend>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama Kategori</label>
                            <div class="col-sm-8">
                                <select id="kategori" name="idKategori" class="form-select form-control" aria-label="Default select example" >
                                    <option value=''>---Silahkan Pilih Kategori---</option>
                                    @foreach ($daftar_kategori as $baris )
                                        <option value={{ $baris->a_kd_kt }}> {{ $baris->a_kd_kt }} - {{ $baris->a_nm_kt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Sub Kategori</label>
                            <div class="col-sm-8">
                                <select id="idSubkategori" name="idSubkategori" class="form-select form-control" aria-label="Default select example" required>
                                    <option value=''>---Silahkan Pilih Sub Kategori---</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Sub Sub Kategori</label>
                            <div class="col-sm-8">
                                <select id="idSub2kategori" name="idSub2kategori" class="form-select form-control" aria-label="Default select example" required>
                                    <option value=''>---Silahkan Pilih Sub Sub Kategori---</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Sub Sub Sub Kategori</label>
                            <div class="col-sm-8">
                                <select id="idSub3kategori" name="idSub3kategori" class="form-select form-control" aria-label="Default select example" required>
                                    <option value=''>---Silahkan Pilih Sub Sub Sub Kategori---</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Barang</label>
                            <div class="col-sm-8">
                                <select id="idBarang" name="idBarang" class="form-select form-control" aria-label="Default select example" required>
                                    <option value=''>---Silahkan Pilih Barang---</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Jumlah Item</label>
                            <div class="col-sm-8">
                            <input type="number" id="jumlah_item" name="jumlah_item" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah Item" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Perolehan</label>
                            <div class="col-sm-8">
                              <input type="date" id="tgl_perolehan" name="tgl_perolehan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Perolehan" required>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Rincian Perolehan</legend>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal Pembukuan</label>
                            <div class="col-sm-8">
                              <input type="date" id="tgl_buku" name="tgl_buku" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Buku" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Dasar Harga</label>
                            <div class="col-sm-8">
                                <select id="dasar_harga" name="dasar_harga" class="form-select form-control" aria-label="Default select example" required>
                                    <option value=''>---Silahkan Pilih Dasar Harga---</option>
                                    <option value='1'>Perolehan</option>
                                    <option value='2'>Taksiran</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Rincian Kapitalis</legend>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kuantitas</label>
                            <div class="col-sm-8">
                              <input type="number" id="kuantitas" name="kuantitas" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Kuantitas" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nilai Aset Per Item</label>
                            <div class="col-sm-8">
                              <input type="number" id="nilai_aset_peritem" name="nilai_aset_peritem" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Jumlah Item" required>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Kondisi Aset</legend>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kondisi</label>
                            <div class="col-sm-8">
                                <select id="kondisi" name="kondisi" class="form-select form-control" aria-label="Default select example" required>
                                    <option value=''>---Silahkan Pilih Kondisi---</option>
                                    <option value='1'>Baik</option>
                                    <option value='2'>Rusak Ringan</option>
                                    <option value='3'>Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>                    
                    <fieldset>
                        <legend>Tercatat Dalam</legend>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tercatat</label>
                            <div class="col-sm-8">
                                <select id="tercatat" name="tercatat" class="form-select form-control" aria-label="Default select example" required>
                                    <option value=''>---Silahkan Pilih Tercatat---</option>
                                    <option value='1'>DBR</option>
                                    <option value='2'>DBL</option>
                                    <option value='3'>KIB</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Rincian Aset Lainnya</legend>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Asal Perolehan</label>
                            <div class="col-sm-8">
                              <input type="text" id="asal_perolehan" name="asal_perolehan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Asal Perolehan" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No. Bukti Perolehan</label>
                            <div class="col-sm-8">
                              <input type="text" id="no_bukti_perolehan" name="no_bukti_perolehan" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan No. Bukti Perolehan" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Merk Aset</label>
                            <div class="col-sm-8">
                              <input type="text" id="merk_aset" name="merk_aset" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Merk Aset" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Keterangan</label>
                            <div class="col-sm-8">
                              <input type="text" id="ket" name="ket" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Keterangan" required>
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
<style>

</style>
<script>
$('#kategori').change(function(){    
    var kat = $(this).val();    
    if(kat){
        $.ajax({
           type:"GET",
           url:"/perolehan-penyelesaiankdp-opf-khusus-aset-subkategori?subKat="+kat,
           dataType: 'JSON',
           success:function(res){               
            if(res){
                $("#idSubkategori").empty();
                $("#idSubkategori").append("<option value=''>---Silahkan Pilih Sub Kategori---</option>");
                $.each(res,function(a_nm_kt_sub,a_kd_kt_sub){
                    $("#idSubkategori").append('<option value="'+a_kd_kt_sub+'">'+a_kd_kt_sub+' - '+a_nm_kt_sub+'</option>');
                });
            }else{
               $("#idSubkategori").empty();
            }
           }
        });
    }else{
        $("#idSubkategori").empty();
    }
});

$('#idSubkategori').change(function(){    
    var subkat = $(this).val();    
    if(subkat){
        $.ajax({
           type:"GET",
           url:"/perolehan-penyelesaiankdp-opf-khusus-aset-sub2kategori?sub2Kat="+subkat,
           dataType: 'JSON',
           success:function(res){               
            if(res){
                $("#idSub2kategori").empty();
                $("#idSub2kategori").append("<option value=''>---Silahkan Pilih Sub Sub Kategori---</option>");
                $.each(res,function(a_nm_kt_sub_2,a_kd_kt_sub_2){
                    $("#idSub2kategori").append('<option value="'+a_kd_kt_sub_2+'">'+a_kd_kt_sub_2+' - '+a_nm_kt_sub_2+'</option>');
                });
            }else{
               $("#idSub2kategori").empty();
            }
           }
        });
    }else{
        $("#idSub2kategori").empty();
    }
});

$('#idSub2kategori').change(function(){    
    var sub2kat = $(this).val();    
    if(sub2kat){
        $.ajax({
           type:"GET",
           url:"/perolehan-penyelesaiankdp-opf-khusus-aset-sub3kategori?sub3Kat="+sub2kat,
           dataType: 'JSON',
           success:function(res){               
            if(res){
                $("#idSub3kategori").empty();
                $("#idSub3kategori").append("<option value=''>---Silahkan Pilih Sub Sub Sub Kategori---</option>");
                $.each(res,function(a_nm_kt_sub_3,a_kd_kt_sub_3){
                    $("#idSub3kategori").append('<option value="'+a_kd_kt_sub_3+'">'+a_kd_kt_sub_3+' - '+a_nm_kt_sub_3+'</option>');
                });
            }else{
               $("#idSub3kategori").empty();
            }
           }
        });
    }else{
        $("#idSub3kategori").empty();
    }
});

$('#idSub3kategori').change(function(){    
    var sub3kat = $(this).val();    
    if(sub3kat){
        $.ajax({
           type:"GET",
           url:"/perolehan-penyelesaiankdp-opf-khusus-aset-barang?sub4Kat="+sub3kat,
           dataType: 'JSON',
           success:function(res){               
            if(res){
                $("#idBarang").empty();
                $("#idBarang").append("<option value=''>---Silahkan Pilih Barang---</option>");
                $.each(res,function(a_nm_brg,a_kd_brg){
                    $("#idBarang").append('<option value="'+a_kd_brg+'">'+a_kd_brg+' - '+a_nm_brg+'</option>');
                });
            }else{
               $("#idBarang").empty();
            }
           }
        });
    }else{
        $("#idBarang").empty();
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
        ajax: "{{ url('perolehan-penyelesaiankdp-opf-khusus-aset') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {
                data: null,
                name: 'ketegori',
                render: function(data, type, row) {
                    return row.a_nm_kt_sub + ' - ' + row.a_nm_kt_sub_2 + ' - ' + row.a_nm_kt_sub_3;
                }
            },
            {
                data: null,
                name: 'barang',
                render: function(data, type, row) {
                    return row.a_kd_brg + ' - ' + row.a_nm_brg;
                }
            },
            { data: 'a_jmlh_ap', name: 'a_jmlh_ap' },
            {
                data: null,
                name: 'no_urut',
                render: function(data, type, row) {
                    return row.a_no_awal_ap + ' - ' + row.a_no_akhir_ap;
                }
            },
            {
                data: 'a_nilai_ap',
                "render": function(data) {
                    return '<div class="right-cell">' + formatRupiah(data) + '</div>';
                }
            },
            {
                data: 'a_tgl_perolehan_ap',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },
            {
                data: 'a_tgl_buku_ap',
                render: function(data, type, row) {
                const formattedDate = dayjs(data).format('D MMMM YYYY');
                return formattedDate;
                }
            },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons += 
                    '<a href="javascript:void(0);" onClick="deleteFunc(' + row.a_id_ap + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> '  +
                    '<a href="perolehan-penyelesaiankdp-opf-khusus-aset-barang/' + row.a_id_ap_en + '"  data-bs-toggle="tooltip" data-bs-placement="left" title="Rincian Barang" class="btn btn-rounded btn-sm btn-primary"><i class="fa fa-book fa-sm"></i></a> ';
                    return buttons;
                }
            },
        ],
        order: [[0, 'desc']]
    });
});
 
function add(){
    $('#DataForm').trigger("reset");
    $('.modal-title').html("Form Tambah Data");
    $('#data-modal').modal('show');
    $('#a_id_ap').val('');
}   
 
function deleteFunc(a_id_ap){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var a_id_ap = a_id_ap;
        $.ajax({
            type:"POST",
            url: "{{ url('perolehan-penyelesaiankdp-opf-khusus-aset-delete') }}",
            data: { a_id_ap : a_id_ap },
            dataType: 'json',
            success: function(res){    
                if (res.status === 1) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showSaveMessage('Data berhasil dihapus');
                }  
                else if (res.status === 2) {
                    var oTable = $('#data-datatable').dataTable();
                    oTable.fnDraw(false);
                    showDeleteMessage('Data gagal dihapus, kode kategori sudah digunakan pada data sub kategori');
                }
                else if (res.status === 3) {
                    showDangerMessage('Maaf, tanggal perolehan yang anda hapus lebih kecil dari data tanggal perolehan yang sudah ada pada pembelian barang');
                }
            }
        });
    }
}

$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('perolehan-penyelesaiankdp-opf-khusus-aset-store')}}",
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
            else if (data.status === 2) 
            {
                showDangerMessage('Data gagal disimpan, tanggal perolehan yang anda entrikan lebih kecil dari data tanggal perolehan yang sudah ada pada pembelian barang');
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