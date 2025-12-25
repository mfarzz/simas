<style>
.sidebar-footer {
  position: fixed;
  z-index: 10;
  bottom: 0;
  left: 0;
  transition: .2s ease-out;
  width: 250px;
  background-color: #ffffff;
  border-top: 1px solid rgba(23, 43, 76, 0); }
  .sidebar-footer a {
    padding: 12px;
    width: 33.333337%;
    float: left;
    text-align: center;
    font-size: 18px; }
</style>
<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li>
                    <a href="/dashboard" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">Dashboard</span>
                    </a>                   
                </li>
                @if(auth()->user()->role_id == 1)
                <li class="menu-title" key="t-menu">Super Admin</li> 
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-food-menu"></i>
                        <span key="t-layouts">Master Barang</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/master-kategori-aset" key="t-light-sidebar">Kategori Barang</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-food-menu"></i>
                        <span key="t-layouts">Lokasi</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/master-lokasi-fakultas-aset" key="t-light-sidebar">Fakultas</a></li>
                        <li><a href="/master-lokasi-rektorat-aset" key="t-light-sidebar">Rektorat</a></li>
                    </ul>
                </li>
                @elseif(auth()->user()->role_id == 4)
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-food-menu"></i>
                        <span key="t-layouts">Master Data</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/ruangan-aset" key="t-light-sidebar">Ruangan</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-cart-alt"></i>
                        <span key="t-layouts">Perolehan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/perolehan-pembelian-khusus-aset" key="t-light-sidebar">Pembelian</a></li>
                        <li><a href="/perolehan-hibah-khusus-aset" key="t-light-sidebar">Hibah</a></li>
                        <li><a href="/perolehan-transfermasuk-khusus-aset" key="t-light-sidebar">Transfer Masuk</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-wrench"></i>
                        <span key="t-layouts">Perubahan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/perubahan-kondisi-khusus-aset" key="t-light-sidebar">Perubahan Kondisi</a></li>
                        <li><a href="/perubahan-koreksi-khusus-aset" key="t-light-sidebar">Koreksi Nilai/Kuantitas</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-basket"></i>
                        <span key="t-layouts">Penghapusan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/penghapusan-hapus-khusus-aset" key="t-light-sidebar">Penghapusan</a></li>
                        <li><a href="/penghapusan-hibahkeluar-khusus-aset" key="t-light-sidebar">Hibah (Keluar)</a></li>
                        <li><a href="/penghapusan-transferkeluar-khusus-aset" key="t-light-sidebar">Transfer (Keluar)</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-bar-chart"></i>
                        <span key="t-layouts">Laporan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/lap-trans-aset" key="t-light-sidebar">Jenis Transaksi</a></li>
                        <li><a href="/lap-kondisibrg-aset" key="t-light-sidebar">Kondisi Barang</a></li>
                    </ul>
                </li>

                @elseif(auth()->user()->role_id == 5)
                <li class="menu-title" key="t-menu">Operator Rektorat</li> 
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-food-menu"></i>
                        <span key="t-layouts">Master Data</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/ruangan-rektorat-aset" key="t-light-sidebar">Ruangan</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-cart-alt"></i>
                        <span key="t-layouts">Perolehan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/perolehan-pembelian-opr-khusus-aset" key="t-light-sidebar">Pembelian</a></li>
                        <li><a href="/perolehan-hibah-opr-khusus-aset" key="t-light-sidebar">Hibah</a></li>
                        <li><a href="/perolehan-transfermasuk-opr-khusus-aset" key="t-light-sidebar">Transfer Masuk</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-wrench"></i>
                        <span key="t-layouts">Perubahan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/perubahan-kondisi-opr-khusus-aset" key="t-light-sidebar">Perubahan Kondisi</a></li>
                        <li><a href="/perubahan-koreksi-opr-khusus-aset" key="t-light-sidebar">Koreksi Nilai/Kuantitas</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-basket"></i>
                        <span key="t-layouts">Penghapusan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/penghapusan-hapus-opr-khusus-aset" key="t-light-sidebar">Penghapusan</a></li>
                        <li><a href="/penghapusan-hibahkeluar-opr-khusus-aset" key="t-light-sidebar">Hibah (Keluar)</a></li>
                        <li><a href="/penghapusan-reklasifikasikeluar-opr-khusus-aset" key="t-light-sidebar">Reklasifikasi (Keluar)</a></li>
                        <li><a href="/penghapusan-transferkeluar-opr-khusus-aset" key="t-light-sidebar">Transfer (Keluar)</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bxs-briefcase"></i>
                        <span key="t-layouts">Penggunaan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/daftar-barang-ruangan-opr-aset" key="t-light-sidebar">Daftar Barang Ruangan</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-bar-chart"></i>
                        <span key="t-layouts">Laporan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/lap-trans-opr-aset" key="t-light-sidebar">Jenis Transaksi</a></li>
                        <li><a href="/lap-kondisibrg-opr-aset" key="t-light-sidebar">Kondisi Barang</a></li>
                    </ul>
                </li>
                @elseif(auth()->user()->role_id == 7)
                <li class="menu-title" key="t-menu">Operator Fakultas</li> 
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-food-menu"></i>
                        <span key="t-layouts">Master Data</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/ruangan-fakultas-aset" key="t-light-sidebar">Ruangan</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-cart-alt"></i>
                        <span key="t-layouts">Perolehan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/perolehan-pembelian-opf-khusus-aset" key="t-light-sidebar">Pembelian</a></li>
                        <li><a href="/perolehan-hibah-opf-khusus-aset" key="t-light-sidebar">Hibah</a></li>
                        <li><a href="/perolehan-transfermasuk-opf-khusus-aset" key="t-light-sidebar">Transfer Masuk</a></li>
                        <li><a href="/perolehan-penyelesaiankdp-opf-khusus-aset" key="t-light-sidebar">Penyelesaian Bangunan Dengan KDP</a></li>
                        <li><a href="/perolehan-penyelesaianlangsung-opf-khusus-aset" key="t-light-sidebar">Penyelesaian Bangunan Langsung</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-wrench"></i>
                        <span key="t-layouts">Perubahan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/perubahan-kondisi-opf-khusus-aset" key="t-light-sidebar">Perubahan Kondisi</a></li>
                        <li><a href="/perubahan-koreksi-opf-khusus-aset" key="t-light-sidebar">Koreksi Nilai/Kuantitas</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-basket"></i>
                        <span key="t-layouts">Penghapusan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/penghapusan-hapus-opf-khusus-aset" key="t-light-sidebar">Penghapusan</a></li>
                        <li><a href="/penghapusan-hibahkeluar-opf-khusus-aset" key="t-light-sidebar">Hibah (Keluar)</a></li>
                        <li><a href="/penghapusan-reklasifikasikeluar-opf-khusus-aset" key="t-light-sidebar">Reklasifikasi (Keluar)</a></li>
                        <li><a href="/penghapusan-transferkeluar-opf-khusus-aset" key="t-light-sidebar">Transfer (Keluar)</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bxs-briefcase"></i>
                        <span key="t-layouts">Penggunaan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/daftar-barang-ruangan-opf-aset" key="t-light-sidebar">Daftar Barang Ruangan</a></li>
                    </ul>
                </li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-bar-chart"></i>
                        <span key="t-layouts">Laporan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">                        
                        <li><a href="/lap-trans-opf-aset" key="t-light-sidebar">Jenis Transaksi</a></li>
                        <li><a href="/lap-kondisibrg-opf-aset" key="t-light-sidebar">Kondisi Barang</a></li>
                    </ul>
                </li>
                @endif
            </ul>
            @if(auth()->user()->pengguna == 1 or auth()->user()->pengguna == 3)
            <div class="sidebar-footer">
                <form action="/beranda-inventaris" method="post">
                  @csrf
                  <input type="hidden" name="pil_aplikasi" value="inventaris">
                    <div class="row">
                    <div class="col-12 text-center">
                      <button type="submit" class="btn btn-success mt-10"><span class="icon-Settings-2"></span>Masuk Sistem Informasi Persediaan</button>
                    </div>
                    </div>
                </form>	
            </div>
            @endif
        </div>
        <!-- Sidebar -->     
        
        
    </div>
    
</div>
<!-- Left Sidebar End -->