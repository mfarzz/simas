<x-form-prosesdata>
    Apakah anda yakin akan melanjutkan proses permintaan untuk <b>{{ $nama }}</b> ?
    <x-input-select judulform="Status Data" : name="status_ajuan" : jenis="warning">
        <option value=''>Silahkan Pilih Status</option>
        @foreach($daftar_status as $baris)        
        <option value={{ $baris->id_rspu }}> {{ $baris->nm_rsp }} {{ $baris->nm_rspu }} {{ $baris->nama_rp }}</option>
        @endforeach
    </x-input-select>
    <x-input-text judulform="Keterangan" : placeholder="Keterangan" : name="keterangan" : jenis="warning" />
</x-form-prosesdata>