@if(request()->user() && request()->user()->role == 2)
<nav class="d-md-none fixed-bottom bg-white border-top" style="z-index:1020;">
  <div class="d-flex justify-content-around py-2">
    <a href="{{ route('dashboard') }}" class="text-center text-decoration-none text-dark">
      <i class="bx bx-home bx-lg"></i>
      <div style="font-size:12px">Dashboard</div>
    </a>

    <a href="{{ route('tenaga') }}" class="text-center text-decoration-none text-dark">
      <i class="bx bx-group bx-lg"></i>
      <div style="font-size:12px">Informasi<br>Guru/Pegawai</div>
    </a>

    <a href="{{ route('pembayaran') }}" class="text-center text-decoration-none text-dark">
      <i class="bx bx-wallet bx-lg"></i>
      <div style="font-size:12px">Pembayaran<br>SK Yayasan</div>
    </a>

    <a href="{{ route('upload-sk.index') }}" class="text-center text-decoration-none text-dark">
      <i class="bx bx-file bx-lg"></i>
      <div style="font-size:12px">File<br>SK Yayasan</div>
    </a>

    <a href="{{ route('profile') }}" class="text-center text-decoration-none text-dark">
      <i class="bx bx-user bx-lg"></i>
      <div style="font-size:12px">Profile</div>
    </a>
  </div>
  <style>
    /* make some room for fixed bottom so content isn't hidden */
    @media (max-width: 767.98px) {
      .container-xxl { padding-bottom: 64px; }
    }
  </style>
</nav>
@endif
