<x-guest-layout>
    <div class="container h-p100">
		<div class="row align-items-center justify-content-md-center h-p100">	
			
			<div class="col-12">
				<div class="row justify-content-center no-gutters">
					<div class="col-lg-5 col-md-5 col-12">
						<div class="bg-white rounded30 shadow-lg">
							<div class="content-top-agile p-20 pb-0">
								<h2 class="text-primary">SIMAS</h2>
								<p class="mb-0">Universitas Andalas</p>							
							</div>
							<div class="p-40">
                                <x-jet-validation-errors class="mb-4" /> 
								<form method="POST" action="{{ route('login') }}">
                                    @csrf
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
											</div>
											<input type="text" name="username" value="{{ old('username') }}" class="form-control pl-15 bg-transparent" placeholder="Username">
										</div>
									</div>
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text  bg-transparent"><i class="ti-lock"></i></span>
											</div>
											<input type="password" name="password" class="form-control pl-15 bg-transparent" placeholder="Password">
										</div>
									</div>
									<div class="form-group">
										<div class="input-group mb-3">
											<div class="input-group-prepend">
												<span class="input-group-text  bg-transparent"><i class="ti-shield"></i></span>
											</div>
											<select name="tahun_anggaran" class="form-control">
												@php
													$tahun_sekarang = now()->year;
													$tahun_mulai = 2022;
												@endphp

												@for ($tahun = $tahun_sekarang; $tahun >= $tahun_mulai; $tahun--)
													<option value="{{ $tahun }}">{{ $tahun }}</option>
												@endfor
											</select>
										</div>
									</div>
									  <div class="row">
										<div class="col-6">
										  <div class="checkbox">
										  </div>
										</div>
										<!-- /.col -->
										<div class="col-6">
										 <div class="fog-pwd text-right">
											
										  </div>
										</div>
										<!-- /.col -->
										<div class="col-12 text-center">
										  <button type="submit" class="btn btn-danger mt-10">SIGN IN</button>
										</div>
										<!-- /.col -->
									  </div>
								</form>	
								<div class="text-center">
									<p class="mt-15 mb-0">Jika ada kendala silahkan hubungi <a href="#" class="text-warning ml-5">Unand</a></p>
								</div>	
							</div>						
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</x-guest-layout>
