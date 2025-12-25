<x-form-update>   
    <x-input-select judulform="Satuan" : name="idJenis" : jenis="warning">
        <option value=''>Silahkan Pilih Satuan</option>
        @foreach($daftar_jenis as $baris)
        <option value={{ $baris->id_js }}> {{ $baris->nm_js }}</option>
        @endforeach
    </x-input-select> 
    <x-input type="text" : judulform="Kode Barang" : placeholder="Entrikan Kode Barang" : name="kode" : jenis="warning" />
    <x-input type="text" : judulform="Nama Barang" : placeholder="Entrikan Nama Barang" : name="nama" : jenis="warning" />
</x-form-update>