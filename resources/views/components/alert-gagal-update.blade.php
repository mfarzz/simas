<div class="position-fixed top-50 start-50 translate-middle p-3" style="z-index: 1005">
    <div id="liveToast" class="toast align-items-center text-white bg-{{Session::get('class') }} border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
        <div class="toast-body">
            {{Session::get('message-gagal-update') }}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>