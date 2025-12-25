@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Rincian Barang</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Perubahan</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Koreksi Barang</a></li>
                            <li class="breadcrumb-item active">Rincian Barang</li>
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
                                <h4 class="card-title">Daftar Barang</h4>
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
                        <div class="col-md-12">
                            <div class="box bt-3 border-info">
                              <div class="box-header">
                                <h4 class="box-title">Rincian</strong></h4>
                              </div>
                              <div class="box-body">
                                <p> Kategori : {{ $data_aset->a_nm_kt }} - {{ $data_aset->a_nm_kt_sub }} - {{ $data_aset->a_nm_kt_sub_2 }} - {{ $data_aset->a_nm_kt_sub_3 }}</p>
                                <p> Baramg : {{ $data_aset->a_nm_brg }} </p>
                              </div>
                            </div>
                        </div>

                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table class="table table-bordered table-hover table-striped" id="data-datatable">
                                <x-judul-tabel-aset>
                                    <x-isi-judul-tabel namakolom="No" />
                                    <x-isi-judul-tabel namakolom="Kode Barang" />
                                    <x-isi-judul-tabel namakolom="No Barang" />
                                    <x-isi-judul-tabel namakolom="Kuantitas Lama" />
                                    <x-isi-judul-tabel namakolom="Kuantitas Baru" />
                                    <x-isi-judul-tabel namakolom="Nilai Aset Lama" />
                                    <x-isi-judul-tabel namakolom="Nilai Aset Baru" />
                                </x-judul-tabel-aset>
                            </table>
                        </div>
                        <a href="/perubahan-koreksi-opr-khusus-aset"  data-bs-toggle="tooltip" data-bs-placement="left" title="Kembali ke daftar perubahan koreksi barang"><button type="button" class="btn btn-success waves-effect waves-light"><i class="fas fa-reply"></i> Kembali Ke Daftar Perubahan Koreksi </button></a>
                    </div>                    
                </div>
            </div>
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
        ajax: "{{ url('perubahan-koreksi-opr-khusus-aset-barang', ['encripted_id' => $encripted_id]) }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'a_kd_brg_api', name: 'a_kd_brg_api' },
            { data: 'a_no_api', name: 'a_no_api' },   
            { data: 'a_kuantitas_lama_aui', name: 'a_kuantitas_lama_aui' },
            {
                data: 'a_nilai_lama_aui',
                "render": function(data) {
                    return '<div class="right-cell">' + formatRupiah(data) + '</div>';
                }
            },
            { data: 'a_kuantitas_aui', name: 'a_kuantitas_aui' },
            {
                data: 'a_nilai_aui',
                "render": function(data) {
                    return '<div class="right-cell">' + formatRupiah(data) + '</div>';
                }
            },

        ],
        order: [[0, 'desc']]
    });
});
 
</script>
@endsection