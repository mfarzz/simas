<x-guest-layout>
    <div class="col-xxl-3 col-lg-4 col-md-5">        
        <div class="auth-full-page-content d-flex p-sm-5 p-4">
            <div class="w-100">
                <div class="d-flex flex-column h-100">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="text-center mb-5">                        
                                        <h4 class="text-uppercase">Maaf, akun anda belum aktif, silahkan aktifkan akun anda terlebih dahulu</h4>                                        
                                    </div>
                                </div>
                            </div>            
                        </div>
                        <!-- end container -->
                    
                    <div class="auth-content my-auto">
                        <div class="text-center">
                            <h5 class="mb-0">Aktivasi Akun</h5>
                            <p class="text-muted mt-2">Silahkan aktivasi akun anda</p>
                        </div>
                        <form method="POST" class="mt-4 pt-2" action="/kirim">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label">NIM / No BP</label>
                                <input type="text" class="form-control" name="nim" placeholder="Silahkan entrikan NIM / No BP" required>
                                <div class="invalid-feedback">
                                    Silahkan entrikan NIM / No BP
                                </div>  
                            </div>

                            <div class="mb-3">
                                <label for="useremail" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Silahkan entrikan email" required>  
                                <div class="invalid-feedback">
                                    Silahkan entrikan email
                                </div>      
                            </div>            
                            
                            <div class="mb-3">
                                <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Konfirmasi</button>
                            </div>
                        </form>

                        <div class="mt-5 text-center">
                            <p class="text-muted mb-0">Sudah punya akun ? <a href="/login"
                                    class="text-primary fw-semibold"> Login </a> </p>
                        </div>
                    </div>
                    <div class="mt-4 mt-md-5 text-center">
                        <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> STMIK Indonesia Padang</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- end auth full page content -->
    </div>
</x-guest-layout>