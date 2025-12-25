<x-form-input>
    <x-input-select judulform="Nama Kategori" : name="idKategori" : jenis="warning">
        <option value=''>Silahkan Pilih Kategori</option>
        @foreach($daftar_kategori as $baris)
        <option value={{ $baris->kd_kl }}> {{ $baris->kd_kl }} - {{ $baris->nm_kl }}</option>
        @endforeach
    </x-input-select>
    @if (!is_null($cariSubkategori))
    <x-input-select judulform="Sub Kategori" : name="idSubkategori" : jenis="warning">
        <option value=''>Silahkan Pilih Sub Kategori</option>
        @foreach($cariSubkategori as $baris)        
        <option value={{ $baris->kd_sskt }}> {{ $baris->kd_sskt }} - {{ $baris->nm_sskt }}</option>        
        @endforeach
    </x-input-select>     
    @endif
    <x-input type="number" : judulform="Barcode" : placeholder="Entrikan Barcode" : name="barcode" : jenis="warning" />
    @if (!is_null($cariItemkategori))
    <x-input-select judulform="Item" : name="idItem" : jenis="warning">
        <option value=''>Silahkan Pilih Item</option>
        @foreach($cariItemkategori as $baris)        
        <option value={{ $baris->kd_brg }}> {{ $baris->kd_brg }} - {{ $baris->nm_brg }}</option>        
        @endforeach
    </x-input-select>     
    @endif     
    <x-input type="number" : judulform="Jumlah" : placeholder="Entrikan Jumlah" : name="jumlah" : jenis="warning" />
    <x-input type="date" : judulform="Tanggal Keluar" : placeholder="Entrikan Tanggal Keluar" : name="tgl_tentu" : jenis="warning" />
    <x-input type="text" : judulform="Keterangan" : placeholder="Entrikan Keterangan" : name="ket_bu" : jenis="warning" />
</x-form-input>