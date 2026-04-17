@extends('backend.layout.base')

@section('content')
<div class="row">
    <div class="col-mb">
        <div class="card mb-4">

            <!-- Header Card -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="font-size: 40px"><b>{{ $title }}</b></h5>
            </div>

            <div class="container-fluid px-4 mt-4">
                <!-- Daftar Harga Batik Maarif (Marketplace Style) -->
                <div class="row mb-4 g-3">
                    <div class="col-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <img src="{{ asset('storage/images/logo/Seragam MI.jpg') }}" class="card-img-top product-img" alt="Batik Siswa MI">
                            <div class="card-body text-center">
                                <h4 class="card-title mb-1">Batik Siswa MI</h4>
                                <div class="text-muted stok-display" data-produk="Batik Siswa MI" style="font-size: 14px;">
                                    Stok:
                                        @php
                                            $s = $stok->firstWhere('produk', 'Batik Siswa MI');
                                            $hargaVal = $s && isset($s->harga) ? $s->harga : 58500;
                                        @endphp
                                        {{ $s ? $s->stok : 0 }}
                                </div>
                                    <div class="fw-bold text-danger harga-display" data-produk="Batik Siswa MI" data-harga="{{ $hargaVal }}" style="font-size: 18px;">Rp {{ number_format($hargaVal) }} <small class="text-muted">/ meter</small></div>
                                @if(Auth::user()->role == 1)
                                    <button type="button" class="btn btn-sm btn-secondary mt-2 btn-edit-produk"
                                        data-produk="Batik Siswa MI"
                                        data-harga="58500">
                                        Edit
                                    </button>
                                @endif
                                <button type="button" class="btn btn-primary mt-2 btn-pesan-produk"
                                    data-produk="Batik Siswa MI"
                                    data-harga="58500">
                                    Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <img src="{{ asset('storage/images/logo/Seragam MTs-SMP.jpg') }}" class="card-img-top product-img" alt="Batik Siswa MTs/SMP">
                            <div class="card-body text-center">
                                <h4 class="card-title mb-1">Batik Siswa MTs/SMP</h4>
                                <div class="text-muted stok-display" data-produk="Batik Siswa MTs/SMP" style="font-size: 14px;">
                                    Stok:
                                        @php
                                            $s = $stok->firstWhere('produk', 'Batik Siswa MTs/SMP');
                                            $hargaVal = $s && isset($s->harga) ? $s->harga : 68250;
                                        @endphp
                                        {{ $s ? $s->stok : 0 }}
                                </div>
                                    <div class="fw-bold text-danger harga-display" data-produk="Batik Siswa MTs/SMP" data-harga="{{ $hargaVal }}" style="font-size: 18px;">Rp {{ number_format($hargaVal) }} <small class="text-muted">/ meter</small></div>
                                @if(Auth::user()->role == 1)
                                    <button type="button" class="btn btn-sm btn-secondary mt-2 btn-edit-produk"
                                        data-produk="Batik Siswa MTs/SMP"
                                        data-harga="68250">
                                        Edit
                                    </button>
                                @endif
                                <button type="button" class="btn btn-primary mt-2 btn-pesan-produk"
                                    data-produk="Batik Siswa MTs/SMP"
                                    data-harga="68250">
                                    Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>

                    @if(Auth::user()->role == 1)
                    <script>
                    // Edit stok & harga for admin
                    document.querySelectorAll('.btn-edit-produk').forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            const produk = btn.getAttribute('data-produk');
                            const harga = parseInt(btn.getAttribute('data-harga')) || 0;
                            // find current stok from stok variable rendered server-side
                            let stokEl = document.querySelector('.stok-display[data-produk="' + produk + '"]');
                            let currentStok = 0;
                            if (stokEl) {
                                const text = stokEl.textContent || stokEl.innerText;
                                const m = text.match(/\d+/);
                                currentStok = m ? parseInt(m[0]) : 0;
                            }

                            Swal.fire({
                                title: 'Edit Produk',
                                html: `
                                    <label>Produk</label>
                                    <input id="swal_produk" class="form-control" value="${produk}" readonly style="margin-bottom:8px;">
                                    <label>Harga per meter (angka)</label>
                                    <input id="swal_harga" type="number" class="form-control" value="${harga}" min="0" style="margin-bottom:8px;">
                                    <label>Stok (angka)</label>
                                    <input id="swal_stok" type="number" class="form-control" value="${currentStok}" min="0">
                                `,
                                showCancelButton: true,
                                confirmButtonText: 'Simpan',
                                preConfirm: () => {
                                    const hargaVal = parseInt(document.getElementById('swal_harga').value);
                                    const stokVal = parseInt(document.getElementById('swal_stok').value);
                                    if (isNaN(hargaVal) || hargaVal < 0) {
                                        Swal.showValidationMessage('Harga harus berupa angka >= 0');
                                        return false;
                                    }
                                    if (isNaN(stokVal) || stokVal < 0) {
                                        Swal.showValidationMessage('Stok harus berupa angka >= 0');
                                        return false;
                                    }
                                    return { harga: hargaVal, stok: stokVal };
                                }
                            }).then((res) => {
                                if (res.isConfirmed && res.value) {
                                    const body = {
                                        produk: produk,
                                        harga: res.value.harga,
                                        stok: res.value.stok
                                    };

                                    fetch('/batik-maarif/update-stok', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify(body)
                                    }).then(r => r.json()).then(data => {
                                        if (data.success) {
                                            // update UI
                                            document.querySelectorAll('.harga-display[data-produk="' + produk + '"]').forEach(function(el){
                                                el.textContent = 'Rp ' + Number(body.harga).toLocaleString();
                                                el.setAttribute('data-harga', body.harga);
                                            });
                                            document.querySelectorAll('.stok-display[data-produk="' + produk + '"]').forEach(function(el){
                                                el.innerHTML = 'Stok: ' + body.stok;
                                            });
                                            // update order buttons data-harga too
                                            document.querySelectorAll('.btn-pesan-produk[data-produk="' + produk + '"]').forEach(function(b){
                                                b.setAttribute('data-harga', body.harga);
                                            });
                                            Swal.fire('Berhasil', data.message, 'success');
                                        } else {
                                            Swal.fire('Gagal', data.message || 'Server error', 'error');
                                        }
                                    }).catch(err => {
                                        Swal.fire('Gagal', 'Terjadi kesalahan jaringan', 'error');
                                    });
                                }
                            });
                        });
                    });
                    </script>
                    @endif
                    <!-- Merged product: Batik Guru (per meter) -->
                    <div class="col-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <img src="{{ asset('storage/images/logo/Seragam Guru & Pegawai.jpg') }}" class="card-img-top product-img" alt="Batik Guru">
                            <div class="card-body text-center">
                                <h4 class="card-title mb-1">Batik Guru</h4>
                                @php
                                    // prefer a unified stok entry 'Batik Guru', fall back to summing older keys
                                    $s_unified = $stok->firstWhere('produk', 'Batik Guru');
                                    $s_2m = $stok->firstWhere('produk', 'Batik Guru 2 Meter');
                                    $s_25m = $stok->firstWhere('produk', 'Batik Guru 2,5 Meter');
                                    $stok_guru = $s_unified ? $s_unified->stok : (($s_2m ? $s_2m->stok : 0) + ($s_25m ? $s_25m->stok : 0));
                                    $hargaVal = $s_unified && isset($s_unified->harga) ? $s_unified->harga : ($s_2m && isset($s_2m->harga) ? $s_2m->harga : 94000);
                                @endphp
                                <div class="text-muted stok-display" data-produk="Batik Guru" style="font-size: 14px;">Stok: {{ $stok_guru }}</div>
                                <div class="fw-bold text-danger harga-display" data-produk="Batik Guru" data-harga="{{ $hargaVal }}" style="font-size: 18px;">Rp {{ number_format($hargaVal) }} <small class="text-muted">/ meter</small></div>
                                @if(Auth::user()->role == 1)
                                    <button type="button" class="btn btn-sm btn-secondary mt-2 btn-edit-produk" data-produk="Batik Guru" data-harga="{{ $hargaVal }}">Edit</button>
                                @endif
                                <button type="button" class="btn btn-primary mt-2 btn-pesan-produk" data-produk="Batik Guru" data-harga="{{ $hargaVal }}">Pesan Sekarang</button>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                .product-img {
                    width: 100%;
                    height: 200px; /* atur tinggi tetap */
                    object-fit: cover; /* biar tidak gepeng */
                    border-radius: 8px;
                }
                </style>

                <form id="formPesananProduk" action="{{ route('batik.maarif.store') }}" method="POST" style="display:none;">
                    @csrf
                    <!-- ✅ asal sekolah ambil dari Auth::user()->kelas_id -->
                    <input type="hidden" name="asal_sekolah" id="asalSekolahProduk" value="{{ Auth::user()->kelas_id }}">
                    <input type="hidden" name="produk" id="produkPesanan">
                    <input type="hidden" name="jumlah" id="jumlahPesananProduk">
                    <input type="hidden" name="harga" id="hargaPesananProduk">
                </form>

                <script>
                document.querySelectorAll('.btn-pesan-produk').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        const produk = btn.getAttribute('data-produk');
                        const harga = parseInt(btn.getAttribute('data-harga'));
                        Swal.fire({
                            title: 'Pesan Produk',
                            html: `
                                <div class="mb-2"><b>${produk}</b></div>
                                <div class="mb-2 text-danger fw-bold" style="font-size:18px;">Rp ${harga.toLocaleString()} <small class="text-muted">/ meter</small></div>
                                <div class="mb-2">
                                    <label>Masukan Jumlah Pesanan</label>
                                    <input type="number" id="jumlahPesanan" class="form-control" min="1" value="1" style="margin-top:5px;">
                                </div>
                            `,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Pesan Sekarang',
                            cancelButtonText: 'Batal',
                            heightAuto: false,
                            preConfirm: () => {
                                const jumlah = document.getElementById('jumlahPesanan').value;
                                if (!jumlah || jumlah < 1) {
                                    Swal.showValidationMessage('Jumlah pesanan harus diisi minimal 1');
                                    return false;
                                }
                                return jumlah;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('produkPesanan').value = produk;
                                document.getElementById('jumlahPesananProduk').value = result.value;
                                document.getElementById('hargaPesananProduk').value = harga;
                                document.getElementById('formPesananProduk').submit();
                            }
                        });
                    });
                });
                </script>
            </div>
            <!-- Table Data -->
            <div class="row">
                <div class="col-md-12 col-lg-12 mb-4 mb-md-0">
                    <div class="container-fluid px-4 mt-4">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th class="text-center">Asal Madrasah/Sekolah</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Siswa</th>
                                        <th class="text-center">Guru (meter)</th>
                                        <th class="text-center">Total Tagihan</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Penerima</th>
                                        @if(Auth::user()->role == 1)
                                            <th class="text-center">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($datasekolah as $rp)
                                        <tr>
                                            <td class="text-center">{{ $no++ }}</td>
                                            <td>{{ $rp->asal_sekolah }}</td>
                                            <td class="text-center">{{ $rp->created_at }}</td>
                                            <td class="text-center">{{ $rp->siswa }}</td>
                                            <td class="text-center">{{ ($rp->guru_2m ?? 0) + ($rp->guru_25m ?? 0) }}</td>
                                            <td class="text-center">Rp. {{ number_format($rp->total_tagihan) }}</td>
                                            <td class="text-center">{{ $rp->keterangan }}</td>
                                            <td class="text-center">{{ $rp->penerima }}</td>

                                            @if(Auth::user()->role == 1)
                                            <td class="text-center">
                                                {{-- ✅ tombol diterima hanya muncul kalau penerima masih kosong --}}
                                                @if(empty($rp->penerima))
                                                    <button class="btn btn-success btn-sm btn-diterima"
                                                        data-id="{{ $rp->id }}">
                                                        Diterima
                                                    </button>
                                                @endif

                                                {{-- tombol delete tetap muncul untuk admin --}}
                                                <form action="{{ route('batik.maarif.delete', $rp->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Yakin hapus data ini?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-diterima').forEach(function(btn) {
    btn.addEventListener('click', function() {
        let id = btn.getAttribute('data-id');

        Swal.fire({
            title: 'Konfirmasi Diterima',
            html: `
                <label>Nama Penerima:</label>
                <input type="text" id="namaPenerima" class="form-control" placeholder="Masukkan nama penerima">
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const nama = document.getElementById('namaPenerima').value;
                if (!nama) {
                    Swal.showValidationMessage('Nama penerima wajib diisi!');
                    return false;
                }
                return nama;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/batik-maarif/diterima/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        penerima: result.value
                    })
                }).then(res => res.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire('Berhasil!', data.message, 'success')
                              .then(() => location.reload());
                      } else {
                          Swal.fire('Gagal!', data.message, 'error');
                      }
                  });
            }
        });
    });
});
</script>

@endsection
