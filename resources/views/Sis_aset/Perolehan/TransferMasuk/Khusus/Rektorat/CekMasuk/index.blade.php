@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Transfer Masuk Barang Cek</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Perolehan</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Transfer Masuk Barang</a></li>
                            <li class="breadcrumb-item active">Cek</li>
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
                                <h4 class="card-title">Daftar Transfer Masuk Barang Cek</h4>
                            </div>
                            <div class="col-md-6 text-end">
                         
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
                                    <x-isi-judul-tabel-aset namakolom="" />
                                </x-judul-tabel-aset>
                            </table>
                        </div>
                        <a href="/perolehan-transfermasuk-opr-khusus-aset"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar transfer masuk"><button type="button" class="btn btn-success waves-effect waves-light"><i class="fas fa-reply"></i> Kembali Ke Daftar Transfer Masuk </button></a>
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
                <h5 class="modal-title">Hibah</h5>
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
                                <select id="idKategori" name="idKategori" class="form-select form-control" aria-label="Default select example" disabled>
                                    <option value=''>---Silahkan Pilih Nama Kategori---</option>
                                    @foreach ($daftar_kategori as $baris )
                                        <option value={{ $baris->a_kd_kt }}> {{ $baris->a_kd_kt }} - {{ $baris->a_nm_kt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Sub Kategori</label>
                            <div class="col-sm-8">
                                <select id="idSubkategori" name="idSubkategori" class="form-select form-control" aria-label="Default select example" disabled>
                                    <option value=''>---Silahkan Pilih Sub Kategori---</option>
                                    @foreach ($daftar_kategori_sub as $baris )
                                        <option value={{ $baris->a_kd_kt_sub }}> {{ $baris->a_kd_kt_sub }} - {{ $baris->a_nm_kt_sub }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Sub Sub Kategori</label>
                            <div class="col-sm-8">
                                <select id="idSub2kategori" name="idSub2kategori" class="form-select form-control" aria-label="Default select example" disabled>
                                    <option value=''>---Silahkan Pilih Sub Sub Kategori---</option>
                                    @foreach ($daftar_kategori_sub_2 as $baris )
                                        <option value={{ $baris->a_kd_kt_sub_2 }}> {{ $baris->a_kd_kt_sub_2 }} - {{ $baris->a_nm_kt_sub_2 }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Sub Sub Sub Kategori</label>
                            <div class="col-sm-8">
                                <select id="idSub3kategori" name="idSub3kategori" class="form-select form-control" aria-label="Default select example" disabled>
                                    <option value=''>---Silahkan Pilih Sub Sub Sub Kategori---</option>
                                    @foreach ($daftar_kategori_sub_3 as $baris )
                                        <option value={{ $baris->a_kd_kt_sub_3 }}> {{ $baris->a_kd_kt_sub_3 }} - {{ $baris->a_nm_kt_sub_3 }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Barang</label>
                            <div class="col-sm-8">
                                <select id="idBarang" name="idBarang" class="form-select form-control" aria-label="Default select example" readonly>
                                    <option value=''>---Silahkan Pilih Barang---</option>
                                    @foreach ($daftar_barang as $baris )
                                        <option value={{ $baris->a_kd_brg }}> {{ $baris->a_kd_brg }} - {{ $baris->a_nm_brg }}</option>
                                    @endforeach
                                </select>
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
                            <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Tanggal BAST</label>
                            <div class="col-sm-8">
                              <input type="date" id="tgl_bast" name="tgl_bast" class="form-control" id="horizontal-firstname-input" placeholder="Entrikan Tanggal Bast" required>
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
        ajax: "{{ url('perolehan-transfermasuk-opr-khusus-aset-cek') }}",
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
            { data: 'a_jmlh_ah', name: 'a_jmlh_ah' },
            {
                data: null,
                name: 'no_urut',
                render: function(data, type, row) {
                    return row.a_no_awal_ah + ' - ' + row.a_no_akhir_ah;
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
                    '<a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="left" title="Validasi Data" onClick="editFunc(' + row.a_id_ah + ')" data-original-title="Edit" class="edit btn btn-primary btn-rounded btn-sm waves-effect"><i class="fa fa-check fa-sm"></i></a>';
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

function editFunc(a_id_ah){
    $.ajax({
        type:"POST",
        url: "{{ url('perolehan-transfermasuk-opr-khusus-aset-cek-edit') }}",
        data: { a_id_ah: a_id_ah },
        dataType: 'json',
        success: function(res){            
            $('.modal-title').html("Form Edit Data");
            $('#data-modal').modal('show');
            $('#a_id_ah').val(res.a_id_ah);
            $('#idKategori').val(res.a_kd_kt);
            $('#idSubkategori').val(res.a_kd_kt_sub);
            $('#idSub2kategori').val(res.a_kd_kt_sub_2);
            $('#idSub3kategori').val(res.a_kd_kt_sub_3);
            $('#idBarang').val(res.a_kd_brg);
        }
    });
}  

$('#DataForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        type:'POST',
        url: "{{ url('perolehan-transfermasuk-opr-khusus-aset-store')}}",
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
            else if (data.status === 2) {      
                showDangerMessage('Data gagal disimpan');
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