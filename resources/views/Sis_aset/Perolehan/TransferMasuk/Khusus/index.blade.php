@extends('layouts.app_sis_aset')
@section('konten')     
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Transfer Masuk Barang</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Perolehan</a></li>
                            <li class="breadcrumb-item active">Transfer Masuk Barang</li>
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
                                <h4 class="card-title">Daftar Transfer Masuk Barang</h4>
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
        ajax: "{{ url('perolehan-transfermasuk-khusus-aset') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'a_nm_al', name: 'a_nm_al' },
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
                    '<a href="perolehan-transfermasuk-khusus-aset-barang/' + row.a_id_ap_en + '"  data-bs-toggle="tooltip" data-bs-placement="left" title="Rincian Barang" class="btn btn-rounded btn-sm btn-primary"><i class="fa fa-book fa-sm"></i></a> ';
                    return buttons;
                }
            },
        ],
        order: [[0, 'desc']]
    });
}); 
</script>
@endsection