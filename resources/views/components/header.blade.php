<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start">
        <a href="#" class="waves-effect waves-light nav-link d-none d-md-inline-block mx-10 push-btn bg-transparent" data-toggle="push-menu" role="button">
          <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>
        </a>	
        <!-- Logo -->
        <a href="/beranda-inventaris" class="logo">
          <!-- logo-->
          <div class="logo-lg">
              <span class="light-logo"><img src="{!! asset('assets/images/header-e-persediaan.png') !!}" alt="logo"></span>              
          </div>
        </a>	        
    </div>  
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <div class="app-menu">
        <ul class="header-megamenu nav">
          <li class="btn-group nav-item d-md-none">
            <a href="#" class="waves-effect waves-light nav-link push-btn" data-toggle="push-menu" role="button">
              <span class="icon-Align-left"><span class="path1"></span><span class="path2"></span><span class="path3"></span></span>
              </a>
          </li>
          <div class="box bs-3 border-success rounded pull-up">
            <div class="box-body">	
              @if(auth()->user()->role_id == 1) Admin 
              @elseif(auth()->user()->role_id == 2) Kepala Seksi Perlengkapan
              @elseif(auth()->user()->role_id == 4) Subdit Pengelolaan Aset
              @elseif(auth()->user()->role_id == 5) Operator
              @elseif(auth()->user()->role_id == 6) Pimpinan
              @elseif(auth()->user()->role_id == 7) Operator
              @elseif(auth()->user()->role_id == 8) Pimpinan
              @elseif(auth()->user()->role_id == 10) Operator
              @elseif(auth()->user()->role_id == 11) Pimpinan
              @endif
              {{ session()->get('posisi') }}
              (Tahun Anggaran <b>{{ session('tahun_anggaran') }}</b>)
            </div>					
          </div>					
        </ul> 
      </div>
        
      <div class="navbar-custom-menu r-side">
        <ul class="nav navbar-nav">	
            <li class="btn-group nav-item d-lg-inline-flex d-none">
                <a href="#" data-provide="fullscreen" class="waves-effect waves-light nav-link full-screen" title="Full Screen">
                    <i class="icon-Expand-arrows"><span class="path1"></span><span class="path2"></span></i>
                </a>
            </li>	  
          
          <!-- User Account-->
          <li class="dropdown user user-menu">
            <a href="#" class="waves-effect waves-light dropdown-toggle" data-bs-toggle="dropdown" title="User">
                <i class="icon-User"><span class="path1"></span><span class="path2"></span></i>
            </a>
            <ul class="dropdown-menu animated flipInX">
              <li class="user-body">
                 <a class="dropdown-item" href="/profile"><i class="ti-user text-muted me-2"></i> Profile</a>
                 <div class="dropdown-divider"></div>
                 <form method="POST" action="{{ route('logout') }}">
                  @csrf    
                  <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                  this.closest('form').submit();"><i class="dropdown-ite mdi mdi-logout font-size-16 align-middle me-1"></i> Logout</a>  
              </form>
              </li>
            </ul>
          </li>	                    
        </ul>
      </div>
    </nav>
  </header>