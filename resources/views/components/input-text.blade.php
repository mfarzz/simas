<div class="row mb-4">
    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">{{ $judulform }}</label>
    <div wire:ignore class="col-sm-8">
      <textarea id="note4" data-note="@this" class="form-control" rows="2"  wire:model="{{ $name }}"></textarea>
    </div>
    @error($name) <span class="text-danger error"> Data tidak boleh kosong </span>@enderror
</div>