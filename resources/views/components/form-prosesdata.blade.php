<!--  Large modal example -->
<div wire:ignore.self class="modal fade prosesdataModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="myLargeModalLabel">Form Proses Data</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if(session()->has('message-proses-delete'))
              <x-alert-gagal-proses/>
            @endif
            <form enctype="multipart/form-data">
              <input type="hidden" wire:model="tabel_id">
              {{ $slot }}
            </form>
            <div class="modal-footer">
              <button type="button" wire:click="resetInputFields()" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
              <button type="button" wire:click.prevent="prosesajukan()" class="btn btn-danger" >Iya</button>
            </div>
          </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->