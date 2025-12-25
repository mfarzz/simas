<x-form-update>   
    @if(auth()->user()->role_id==5)
    <x-input-select judulform="Nama Kategori" : name="idKategori" : jenis="warning">
        <option value=''>Silahkan Pilih Kategori</option>
        @foreach($daftar_kategori as $baris)
        <option value={{ $baris->kd_kl }}> {{ $baris->kd_kl }} - {{ $baris->nm_kl }}</option>
        @endforeach
    </x-input-select>

    
    <x-input-select judulform="Sub Kategori" : name="idSubkategori" : jenis="warning">
        @if (!is_null($cariSubkategori))
            <option value=''>Silahkan Pilih Sub Kategori</option>
            @foreach($cariSubkategori as $baris)        
            <option value={{ $baris->kd_sskt }}> {{ $baris->kd_sskt }} - {{ $baris->nm_sskt }}</option>        
            @endforeach
        @else            
            @foreach($daftar_subkategori as $baris)   
            <option value={{ $baris->kd_sskt }}> {{ $baris->kd_sskt }} - {{ $baris->nm_sskt }}</option>                                    
            @endforeach
        @endif
    </x-input-select>     
    

    <x-input type="number" : judulform="Barcode" : placeholder="Entrikan Barcode" : name="barcode" : jenis="warning" />    
    <x-input-select judulform="Item" : name="idItem" : jenis="warning">
        @if (!is_null($cariItemkategori))
            <option value=''>Silahkan Pilih Item</option>
            @foreach($cariItemkategori as $baris)        
            <option value={{ $baris->kd_brg }}> {{ $baris->kd_brg }} - {{ $baris->nm_brg }}</option>        
            @endforeach
        @else
            @foreach($daftar_barang as $baris)   
            <option value={{ $baris->kd_brg }}> {{ $baris->kd_brg }} - {{ $baris->nm_brg }}</option>                                    
            @endforeach
        @endif
    </x-input-select>

    <x-input type="number" : judulform="Jumlah yang Diminta" : placeholder="Entrikan Jumlah yang Diminta" : name="jumlah_awal" : jenis="warning" />
    <x-input-hidden name="jumlah" : placeholder="Entrikan Perkiraan Harga" : jenis="warning" />
    @elseif(auth()->user()->role_id==6)
        <x-input-disabled type="number" : judulform="Jumlah yang Diminta" : placeholder="Entrikan Jumlah yang Diminta" : name="jumlah_awal" : jenis="warning" />
        <x-input type="number" : judulform="Jumlah yang Disetujui" : placeholder="Entrikan Jumlah yang Disetujui" : name="jumlah" : jenis="warning" />
        <x-input-select judulform="Status" : name="status_barang" : jenis="warning">
            <option value=''>Silahkan Pilih Status</option>    
            @foreach($tampil_status as $baris)        
            <option value={{ $baris->id_rspd }}> {{ $baris->nm_rspd }}</option>   
            @endforeach         
        </x-input-select>
        <x-input-text judulform="Keterangan" : placeholder="Keterangan" : name="keterangan" : jenis="warning" />
    @endif
</x-form-update>