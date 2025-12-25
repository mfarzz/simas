@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Hibah Keluar Barang</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Penghapusan</a></li>
                            <li class="breadcrumb-item active">Hibah Keluar</li>
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
                                <h4 class="card-title">Daftar Hibah (Keluar) Barang</h4>
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
                                    <x-isi-judul-tabel-aset namakolom="No Urut Pendaftaran" />
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
                <h5 class="modal-title">Penghapusan Hibah (Keluar)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="a_id_ah" id="a_id_ah">
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
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No Urut Pendaftaran</label>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Awal</label>
                            <div class="col-sm-8">
                            <input type="number" id="nup_awal" name="nup_awal" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan NUP Awal" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Akhir</label>
                            <div class="col-sm-8">
                            <input type="number" id="nup_akhir" name="nup_akhir" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan NUP Akhir" required>
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
                    </fieldset>
                    
                    <fieldset>
                        <legend>Rincian Keputusan</legend>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No SK</label>
                            <div class="col-sm-8">
                              <input type="text" id="no_sk" name="no_sk" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan No SK" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal SK</label>
                            <div class="col-sm-8">
                              <input type="date" id="tgl_sk" name="tgl_sk" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Dasar" required>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Keterangan</label>
                            <div class="col-sm-8">
                              <input type="text" id="ket_sk" name="ket_sk" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Keterangan" required>
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
           url:"/penghapusan-hibahkeluar-opf-khusus-aset-subkategori?subKat="+kat,
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
           url:"/penghapusan-hibahkeluar-opf-khusus-aset-sub2kategori?sub2Kat="+subkat,
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
           url:"/penghapusan-hibahkeluar-opf-khusus-aset-sub3kategori?sub3Kat="+sub2kat,
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
           url:"/penghapusan-hibahkeluar-opf-khusus-aset-barang?sub4Kat="+sub3kat,
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
        ajax: "{{ url('penghapusan-hibahkeluar-opf-khusus-aset') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {
                data: null,
                name: 'kategori',
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
            { data: 'a_jmlh_ah', name: 'a_jmlh_ah' },
            {
                data: null,
                name: 'no_urut',
                render: function(data, type, row) {
                    return row.a_no_awal_ah + ' - ' + row.a_no_akhir_ah;
                }
            },
            {
                data: 'a_tgl_buku_ah',
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
                    '<a href="javascript:void(0);" onClick="deleteFunc(' + row.a_id_ah + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> '  +
                    '<a href="penghapusan-hibahkeluar-opf-khusus-aset-barang/' + row.a_id_ah_en + '"  data-bs-toggle="tooltip" data-bs-placement="left" title="Rincian Barang" class="btn btn-rounded btn-sm btn-primary"><i class="fa fa-book fa-sm"></i></a> ';
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
    $('#a_id_ah').val('');
}   
 
function deleteFunc(a_id_ah){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var a_id_ah= a_id_ah;
        $.ajax({
            type:"POST",
            url: "{{ url('penghapusan-hibahkeluar-opf-khusus-aset-delete') }}",
            data: { a_id_ah : a_id_ah },
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
            }
        });
    }
}

$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('penghapusan-hibahkeluar-opf-khusus-aset-store')}}",
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
                showDangerMessage('Data gagal disimpan, nup barang yang anda entrikan tidak tersedia');
            } 
            else if (data.status === 3) 
            {
                alert(data.nuk_ada)
                showDangerMessage('Data gagal disimpan');
            } 
            else if (data.status === 4) 
            {
                showDangerMessage('NUP akhir tidak boleh kecil dari NUP awal');
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