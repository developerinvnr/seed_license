    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
    <i class="ri-arrow-up-line"></i>
    </button>
    <div id="preloader">
       <div id="status">
          <div class="spinner-border text-white avatar-sm" role="status">
             <span class="visually-hidden">Loading...</span>
          </div>
       </div>
    </div>
    <div class="customizer-setting d-none d-md-block">
       <div class="btn-primary rounded-pill shadow-lg btn btn-icon btn-lg p-2" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas"  style="display: none;">
          <i class="mdi mdi-spin mdi-cog-outline fs-22"></i>
       </div>
    </div>

{{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/simplebar/simplebar.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/node-waves/waves.min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/feather-icons/feather.min.js"></script>
<script src="{{ URL::to('/') }}/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/prismjs/prism.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/dropzone/dropzone-min.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script src="{{ URL::to('/') }}/assets/js/app.js"></script>
{{-- <script src="{{ URL::to('/') }}/assets/js/custom.js"></script> --}}
<script>
   
   window.userPermissions = @json(Cache::get("user_permissions_{{ auth()->user()->role_id }}", []));
</script>
@stack('custom-js')
</body>