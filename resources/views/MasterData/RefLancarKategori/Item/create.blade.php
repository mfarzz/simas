<x-form-input>    
    <x-input type="text" : judulform="Kode Item" : placeholder="Entrikan Kode Item" : name="kode" : jenis="warning" />
    <x-input type="text" : judulform="Nama Item" : placeholder="Entrikan Nama Item" : name="nama" : jenis="warning" />
    <x-input-select judulform="Satuan" : name="idSatuan" : jenis="warning">
        <option value=''>Silahkan Pilih Satuan</option>
        @foreach($daftar_satuan as $baris)
        <option value={{ $baris->id_js }}> {{ $baris->nm_js }}</option>
        @endforeach
    </x-input-select> 
    <x-input type="text" : judulform="Barcode" : placeholder="Entrikan Barcode" : name="barcode" : jenis="warning" />
</x-form-input>