<x-form-input>    
    <x-input-select judulform="Nama Barang" : name="barang" : jenis="warning">
        <option value=''>Silahkan Pilih Barang</option>
        @foreach($daftar_barang as $baris)
        <option value={{ $baris->id_masaselan }}> {{ $baris->nm_masaselan }}</option>
        @endforeach
    </x-input-select>
    <x-input type="number" : judulform="Jumlah yang Diterima" : placeholder="Entrikan Jumlah yang Diterima" : name="jumlah" : jenis="warning" />
</x-form-input>