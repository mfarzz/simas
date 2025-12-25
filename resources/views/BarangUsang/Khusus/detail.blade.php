<x-form-detail>

    <x-input type="text" : judulform="Nama Barang" : placeholder="Entrikan Nama Barang" : name="nm_item" : jenis="warning" />

    <div class="table-rep-plugin">
        <div class="table-responsive mb-0" data-pattern="priority-columns">
            <table id="tech-companies-1" class="table table-striped">
                <thead>
                <x-judul-tabel>
                    <x-isi-judul-tabel namakolom="No" />
                    <x-isi-judul-tabel namakolom="Tanggal Beli" />
                    <x-isi-judul-tabel namakolom="Harga Beli" />
                    <x-isi-judul-tabel namakolom="Jumlah Dikeluarkan" />
                    <x-isi-judul-tabel namakolom="Total Nilai" />
                </x-judul-tabel>
                </thead>
                <tbody>
                @foreach ($databarangusang as $valuebu)
                    <tr>
                        <td align='center' style='border:1px solid #90AFC5; color:black'>{{ ++$no }}</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>{{ \Carbon\Carbon::parse($valuebu->tglbeli_bm)->format('d M Y')}}</td> 
                        <td style=' border:1px solid #90AFC5 ; color:black'>@currency($valuebu->hrg_bm)</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>{{ $valuebu->jmlh_bud }}</td>
                        <td style=' border:1px solid #90AFC5 ; color:black'>@currency($valuebu->hrg_bm * $valuebu->jmlh_bud)</td>
                    </tr>                                    
                @endforeach
                @if($no=="0")                                    
                <tr>
                    <td colspan="5" align='center' style='border:1px solid #90AFC5; color:black'>Data tidak ditemukan</td>
                </tr>                                    
                @endif
                </tbody>
            </table>
            {{ $data->links() }}
        </div>
    </div>
</x-form-detail>