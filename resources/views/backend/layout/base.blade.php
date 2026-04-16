<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ Helper::apk()->title }} | {{ request()->user()->nama_lengkap }}</title>
  <meta name="description" content="Dashboard admin Laravel dengan Sneat Bootstrap 5" />
  <meta name="keywords" content="dashboard, laravel, bootstrap 5">

  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('storage/images/logo/' . Helper::apk()->logo) }}" type="image/x-icon">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Icons & Core CSS -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">

  <!-- Plugins CSS -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css') }}">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Full Width Fix -->
  <style>
    /* Override max-width bawaan Sneat agar layout full lebar */
    .layout-wrapper,
    .layout-container,
    .layout-page,
    .content-wrapper,
    .container,
    .container-xxl,
    .container-fluid {
      max-width: 100% !important;
      width: 100% !important;
    }

    .container,
    .container-xxl,
    .container-fluid {
      padding-left: 1rem;
      padding-right: 1rem;
    }

    table.dataTable {
      width: 100% !important;
    }

    body {
      background: linear-gradient(to bottom, #0a48b3, 	#0a48b3 15%, transparent 0%);
      background-color: #f5f5f5;
      min-height: 100vh;
      display: flex;
    }

    .navbar-nav .bx-menu {
      color: white !important;
    }

    :root {
      --btn-primary-bg: 	#0a48b3;
      --btn-primary-bg-hover: #2c65c7;
    }

    .btn-primary {
      background-color: var(--btn-primary-bg) !important;
      border-color: var(--btn-primary-bg) !important;
    }

    .btn.btn-primary:hover {
      background-color: var(--btn-primary-bg-hover) !important;
      border-color: var(--btn-primary-bg-hover) !important;
    }
  </style>

  <!-- Theme Config JS -->
  <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
  <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      @include('backend.layout.sidebar')
      @include('sweetalert::alert')

      <div class="layout-page">
        @include('backend.layout.navbar')

        <div class="content-wrapper">
          <div class="container-xxl flex-grow-1 container-p-y">
            @yield('content')
          </div>

          @include('backend.layout.footer')
          <div class="content-backdrop fade"></div>
        </div>
      </div>

    </div>
  </div>

  <div class="layout-overlay layout-menu-toggle"></div>

  <!-- Core JS -->
  <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

  <!-- DataTables -->
  <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#datatable').DataTable();
    });
  </script>

  <!-- Plugins JS -->
  <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/bloodhound/bloodhound.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>

  <!-- Custom JS -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
  <script src="{{ asset('assets/js/dashboards-ecommerce.js') }}"></script>
  <script src="{{ asset('assets/js/plugin.js') }}"></script>
  <script src="{{ asset('assets/js/forms-selects.js') }}"></script>
  <script src="{{ asset('assets/js/forms-tagify.js') }}"></script>
  <script src="{{ asset('assets/js/forms-typeahead.js') }}"></script>
  <script src="{{ asset('assets/js/forms-file-upload.js') }}"></script>
  <script src="{{ asset('assets/js/form-layouts.js') }}"></script>
  <script src="{{ asset('assets/js/forms-editors.js') }}"></script>
  <script src="{{ asset('assets/js/form-basic-inputs.js') }}"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @yield('js')
</body>
</html>
