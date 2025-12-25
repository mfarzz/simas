@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Daftar Ruangan</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                            <li class="breadcrumb-item active">Daftar Ruangan</li>
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
                                <h4 class="card-title">Daftar Ruangan</h4>
                            </div>
                            <div class="col-md-6 text-end">
                                
                                <div class="mt-3"></div>
                            </div>
                        </div>
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                            <table class="table table-bordered table-hover table-striped" id="data-datatable">
                                <x-judul-tabel-aset>
                                    <x-isi-judul-tabel-aset namakolom="No" />
                                    <x-isi-judul-tabel-aset namakolom="Fakultas/Unit Rektorat" />
                                    <x-isi-judul-tabel-aset namakolom="Kode" />
                                    <x-isi-judul-tabel-aset namakolom="Nama Ruangan" />
                                    <x-isi-judul-tabel-aset namakolom="NIP" />
                                    <x-isi-judul-tabel-aset namakolom="Nama Penanggung Jawab Ruangan" />
                                </x-judul-tabel-aset>
                            </table>
                        </div>
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
        ajax: "{{ url('ruangan-aset') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'a_nm_unit', name: 'a_nm_unit' },
            { data: 'a_kd_ar', name: 'a_kd_ar' },
            { data: 'a_nm_ar', name: 'a_nm_ar' },
            { data: 'a_nip_pj_ar', name: 'a_nip_pj_ar' },
            { data: 'a_nm_pj_ar', name: 'a_nm_pj_ar' }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endsection