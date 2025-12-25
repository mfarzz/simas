<x-guest-layout>    
                <div class="col-xxl-3 col-lg-4 col-md-5">
                    <div class="auth-full-page-content d-flex p-sm-5 p-4">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100">
                                <div class="mb-4 mb-md-5 text-center">
                                    <a href="index.html" class="d-block auth-logo">
                                        <img src="assets/images/logo-sm.svg" alt="" height="28"> <span class="logo-txt">Sistem Informasi Alumni</span>
                                    </a>
                                </div>
                                <div class="auth-content my-auto">
                                    <div class="text-center">
                                        <div class="avatar-lg mx-auto">
                                            <div class="avatar-title rounded-circle bg-light">
                                                <i class="bx bx-mail-send h2 mb-0 text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="p-2 mt-4">
                                            <h4>Akun Anda Sudah Aktif !</h4>
                                            <p class="text-muted">Terima kasih sudah menyelesaikan proses aktivasi akun. Sekarang akun anda sudah aktif, silahkan login ke sistem informasi alumni.</p>
                                            <div class="mt-4">
                                                <form method="POST" class="mt-4 pt-2" action="{{ route('login') }}">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label">Username</label>
                                                        <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Enter username">
                                                    </div>
                                                    <div class="mb-3">
                                                        <div class="d-flex align-items-start">
                                                            <div class="flex-grow-1">
                                                                <label class="form-label">Password</label>
                                                            </div>
                                                            <div class="flex-shrink-0">
                                                                <div class="">
                                                                    <a href="#" class="text-muted">Forgot password?</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="input-group auth-pass-inputgroup">
                                                            <input type="password" name="password" class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                                                            <button class="btn btn-light shadow-none ms-0" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <button class="btn btn-primary w-100 waves-effect waves-light" type="submit">Log In</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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
                <!-- end col -->                
</x-guest-layout>