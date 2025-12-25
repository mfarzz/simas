@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Lokasi Fakultas</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Lokasi</a></li>
                            <li class="breadcrumb-item active">Fakultas</li>
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
                                <h4 class="card-title">Daftar Lokasi Fakultas</h4>
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
                                    <x-isi-judul-tabel-aset namakolom="Kode Lokasi" />
                                    <x-isi-judul-tabel-aset namakolom="Kode UAPKB" />
                                    <x-isi-judul-tabel-aset namakolom="No UAPKB" />
                                    <x-isi-judul-tabel-aset namakolom="JK" />
                                    <x-isi-judul-tabel-aset namakolom="Keterangan" />
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
                <h5 class="modal-title">Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" id="DataForm" name="DataForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="a_id_al" id="a_id_al">
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Kode UAPKB</label>
                        <div class="col-sm-8">
                          <input type="number" id="kode" name="kode" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Kode Kategori" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">No UAPKB</label>
                        <div class="col-sm-8">
                          <input type="number" id="no_uapkb" name="no_uapkb" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Nama Kategori" required>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">JK</label>
                        <div class="col-sm-8">
                            <select id="jenis_kelompok" name="jenis_kelompok" class="form-select form-control" required>
                                <option value=''>---Silahkan JK---</option>
                                <option value='KD'> Kantor Daerah</option>
                                <option value='KP'> Kantor Pusat</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Fakultas</label>
                        <div class="col-sm-8">
                            <select id="fakultas" name="fakultas" class="form-select form-control" required >
                                <option value=''>---Silahkan Pilih Fakultas---</option>
                                @foreach ($daftar_fakultas as $baris )
                                    <option value={{ $baris->id_fk }}> {{ $baris->id_fk }} - {{ $baris->nm_fk }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
$(document).ready( function () {
    $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#data-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('master-lokasi-fakultas-aset') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'a_kd_al', name: 'a_kd_al' },
            { data: 'a_uakpb', name: 'a_uakpb' },
            { data: 'a_no_al', name: 'a_no_al' },
            { data: 'a_jk_al', name: 'a_jk_al' },
            { data: 'nm_fk', name: 'nm_fk' },
            { 
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var buttons = '';
                    buttons += '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Ubah Data" onClick="editFunc(' + row.a_id_al + ')" data-original-title="Edit" class="edit btn btn-warning btn-rounded btn-sm waves-effect"><i class="fa fa-edit fa-sm"></i></a> ' +
                    '<a href="javascript:void(0);" onClick="deleteFunc(' + row.a_id_al + ')" data-bs-toggle="tooltip" data-bs-placement="left" title="Hapus Data" class="delete btn btn-danger btn-rounded btn-sm waves-effect"><i class="fa fa-trash fa-sm"></i></a> ';
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
    $('#a_id_al').val('');
}   
     
function editFunc(a_id_al){
    $.ajax({
        type:"POST",
        url: "{{ url('master-lokasi-fakultas-aset-edit') }}",
        data: { a_id_al: a_id_al },
        dataType: 'json',
        success: function(res){
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#a_id_al').val(res.a_id_al);
            $('#kode').val(res.a_uakpb);
            $('#no_uapkb').val(res.a_no_al);
            $('#jenis_kelompok').val(res.a_jk_al);
            $('#fakultas').val(res.id_fk);
        }
    });
}  
 
function deleteFunc(a_id_al){
    var pesan = "Apakah anda yakin akan menghapus data ini ?";
    if (confirm(pesan) == true) {
        var a_id_al = a_id_al;
        $.ajax({
            type:"POST",
            url: "{{ url('master-lokasi-fakultas-aset-delete') }}",
            data: { a_id_al : a_id_al },
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
        url: "{{ url('master-lokasi-fakultas-aset-store')}}",
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
                showDangerMessage('Data gagal disimpan. Kode UAPKB dan No UAPKB tidak boleh sama');
            }
            else if (data.status === 3) { 
                $("#data-modal").modal('hide');
                var oTable = $('#data-datatable').dataTable();
                oTable.fnDraw(false);
                $("#btn-save").html('Submit');
                $("#btn-save"). attr("disabled", false);
                showWarningMessage('Tidak ada data yang diubah.');
            }
            else if (data.status === 11) { 
                showDangerMessage('Kode dan nomor UAPKB tidak boleh sama');
            }
            else if (data.status === 12) { 
                showDangerMessage('Kode dan nomor UAPKB tidak boleh sama');
            }
            else if (data.status === 13) { 
                showDeleteMessage('Data gagal diubah, kode kategori sudah digunakan pada data sub kategori, anda hanya bisa mengubah nama kategori');
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