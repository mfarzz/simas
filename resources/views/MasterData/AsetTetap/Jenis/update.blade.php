<x-form-update>   
    <x-input type="text" : judulform="Nama Aset Tetap Jenis" : placeholder="Entrikan Nama Aset Tetap Jenis" : name="nama" : jenis="warning" />
    <x-input type="date" : judulform="Tanggal Beli" : placeholder="Entrikan Tanggal Beli" : name="tgl_beli" : jenis="warning" />
    <x-input type="text" : judulform="Harga Beli" : placeholder="Entrikan Harga Beli" : name="harga" : jenis="warning" />
    <x-input type="text" : judulform="Tarif" : placeholder="Entrikan Tarif" : name="tarif" : jenis="warning" />
    <x-input type="text" : judulform="Barcode" : placeholder="Entrikan Barcode" : name="barcode" : jenis="warning" />
    <x-input type="text" : judulform="QR Code" : placeholder="Entrikan QR Code" : name="qrcode" : jenis="warning" />
    <x-input-text type="text" : judulform="Keterangan" : placeholder="Entrikan Keterangan" : name="ket" : jenis="warning" />
    <x-input-select judulform="Lokasi" : name="lokasi" : jenis="warning">
        <option value=''>Silahkan Pilih Lokasi</option>
        @foreach($daftar_lokasi as $baris)
        <option value={{ $baris->id_rl }}> {{ $baris->nm_rl }}</option>
        @endforeach
    </x-input-select>
    <x-input-select judulform="Status" : name="status" : jenis="warning">
        <option value=''>Silahkan Pilih Status</option>
        @foreach($daftar_status as $baris)
        <option value={{ $baris->id_rs }}> {{ $baris->nm_rs }}</option>
        @endforeach
    </x-input-select>
</x-form-update>