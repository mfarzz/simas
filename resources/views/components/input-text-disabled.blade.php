<div class="row mb-4">
    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">{{ $judulform }}</label>
    <div class="col-sm-8">
      <textarea class="form-control" rows="2" placeholder="{{ $placeholder }}" wire:model="{{ $name }}" disabled></textarea>
    </div>
    @error($name) <span class="text-danger error"> Cek kembali data inputan</span>@enderror
</div>