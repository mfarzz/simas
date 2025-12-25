<div class="row mb-4">
    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">{{ $judulform }}</label>
    <div wire:ignore class="col-sm-8">
      <textarea id="note3"  data-note2="@this" class="form-control" rows="2" placeholder="{{ $placeholder }}" wire:model="{!!  $name  !!}">{{ $attributes->wire('model') }}</textarea>
    </div>
    @error($name) <span class="text-danger error"> Cek kembali data inputan</span>@enderror
</div>