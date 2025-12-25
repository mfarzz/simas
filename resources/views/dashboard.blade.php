<x-guest-layout>
    <div class="container h-p100">
		<div class="row align-items-center justify-content-md-center h-p100">
			@if(auth()->user()->pengguna == 1 or auth()->user()->pengguna == 3)
			<div class="col-6">
				<div class="row justify-content-center g-0">
					<div class="col-lg-8 col-md-8 col-12">
						<div class="bg-white rounded10 shadow-lg">
							<div class="content-top-agile p-20">
								<img src="{!! asset('assets/images/header-e-persediaan.png') !!}" alt="logo">		
							</div>
							<div class="p-20">
								<form action="/beranda-inventaris" method="post">
									@csrf
									<input type="hidden" name="pil_aplikasi" value="inventaris">
									  <div class="row">
										<div class="col-12 text-center">
										  <button type="submit" class="btn btn-info mt-10">Masuk</button>
										</div>
									  </div>
								</form>		
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif

			@if(auth()->user()->pengguna == 2 or auth()->user()->pengguna == 3)
            <div class="col-6">
				<div class="row justify-content-center g-0">
					<div class="col-lg-8 col-md-8 col-12">
						<div class="bg-white rounded10 shadow-lg">
							<div class="content-top-agile p-5">
								<img src="{!! asset('assets/images/header-e-aset.png') !!}" alt="logo">							
							</div>
							<div class="p-20">
								<form action="/beranda-aset" method="post">
									@csrf
									<input type="hidden" name="pil_aplikasi" value="aset">
									  <div class="row">
										<div class="col-12 text-center">
										  <button type="submit" class="btn btn-info mt-10">Masuk</button>
										</div>
									  </div>
								</form>		
							</div>
						</div>
					</div>
				</div>
			</div>
			@endif
						
		</div>
	</div>	
</x-guest-layout>

