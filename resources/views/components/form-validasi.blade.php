<div wire:ignore.self class="modal fade" id="formvalidasiModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Form Validasi Data</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
              <form enctype="multipart/form-data">
                  <input type="hidden" wire:model="tabel_id">
                  {{ $slot }}
              </form>
          </div>
          <div class="modal-footer">
              <button type="button" wire:click="resetInputFields()" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
              <button type="button" wire:click.prevent="proses_validasi()" class="btn btn-success" data-bs-dismiss="modal">Iya</button>
          </div>
          </div>
      </div>
  </div>