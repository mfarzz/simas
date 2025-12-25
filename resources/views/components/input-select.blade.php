<div class="row mb-4">
    <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">{{ $judulform }}</label>
    <div class="col-sm-8">
        <select wire:model="{{ $name }}" class="form-control">
            {{ $slot }}
        </select>
    </div>
    @error($name) <span class="text-danger error"> Tidak Boleh Kosong </span>@enderror
</div>