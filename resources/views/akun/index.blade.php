@extends('layouts.app')   
@section('konten') 
<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h3 class="page-title"></h3>
            <div class="d-inline-block align-items-center">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>        
    </div>
</div>
<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Profile</h4>
                            
                    </div>
                </div>
            </div>
            <div class="card border border-primary">
                <div class="card-header bg-transparent border-primary">
                    <h5 class="my-0 text-primary"><i class="mdi mdi-align-vertical-center me-3"></i></button>  Detail Akun </h5>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                    <form id="pristine-valid-example" method="POST" enctype="multipart/form-data" action="/profile">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <img class="img-thumbnail" alt="200x200" width="100%" src="{{ asset('storage/akunprofile/'.$data->profile_photo_path.'') }}" data-holder-rendered="true">
                                
                            </div>
                            <div class="col-md-8">
                                @if (session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>                                    
                                @endif
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Username</label>
                                    <div class="col-sm-8">
                                    <input type="text" class="form-control" id="horizontal-firstname-input" placeholder="Silahkan entrikan data" value="{{ $data->username }}" disabled>
                                    </div>                            
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Nama</label>
                                    <div class="col-sm-8">
                                    <input type="text" class="form-control" id="horizontal-firstname-input" placeholder="Silahkan entrikan data" value="{{ $data->name }}" disabled>
                                    </div>                                                                
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Password Lama</label>
                                    <div class="col-sm-8">
                                    <input type="password" name="password_lama" class="form-control" id="horizontal-firstname-input" placeholder="Silahkan entrikan data password lama" value="{{ old('password_lama') }}">
                                    @error('password_lama')                                        
                                        <div class="alert alert-danger" role="alert">{{ $message }}</div>        
                                    @enderror
                                    </div> 
                                                               
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Password Baru</label>
                                    <div class="col-sm-8">
                                    <input type="password" name="password_baru" class="form-control" id="horizontal-firstname-input" placeholder="Silahkan entrikan data password baru" value="{{ old('password_baru') }}">
                                    @error('password_baru')                                        
                                        <div class="alert alert-danger" role="alert">{{ $message }}</div>        
                                    @enderror
                                    </div>                            
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Konfirmasi Password Baru</label>
                                    <div class="col-sm-8">
                                    <input type="password" name="konfirmasi_password_baru" class="form-control" id="horizontal-firstname-input" placeholder="Silahkan entrikan data konfirmasi password baru" value="{{ old('konfirmasi_password_baru') }}">
                                    @error('konfirmasi_password_baru')                                        
                                        <div class="alert alert-danger" role="alert">{{ $message }}</div>        
                                    @enderror
                                    </div>                            
                                </div>
                                
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Foto</label>
                                    <div class="col-sm-8">
                                    <input type="file" name="dokumen" class="form-control" id="horizontal-firstname-input" placeholder="Silahkan entrikan data konfirmasi password baru" value="{{ old('konfirmasi_password_baru') }}">
                                    @error('dokumen')                                        
                                        <div class="alert alert-danger" role="alert">  File yang diizinkan untuk upload hanyalah jpg atau jpeg, dan maksimal ukuran filenya 2 MB</div>
                                    @enderror
                                    </div>                            
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection