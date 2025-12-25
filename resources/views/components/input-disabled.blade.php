<div class="row mb-4">
    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">{{ $judulform }}</label>
    <div class="col-sm-8">
      <input type="{{ $type }}" class="form-control" id="horizontal-firstname-input" placeholder="{{ $placeholder }}"  wire:model="{{ $name }}" readonly>
    </div>
    @error($name) <span class="text-danger error"> Cek kembali data inputan</span>@enderror
</div>