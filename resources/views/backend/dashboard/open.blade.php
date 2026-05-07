@extends('backend.layout.base')

@section('content')
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="user-profile-header-banner" style="margin-bottom: 0%;">
                <img src="{{ asset('') }}storage/images/users/{{ $siswa->image }}"
                    style="width: 100%; height: 40%;" alt="Banner image" class="rounded-top">
            </div>
            <div class="card-body">
                <div class="card shadow mb-4 border-bottom-success" id="infosiswa" value="0">
                    <a href="#" class="d-block card-header py-3"
                        data-toggle="collapse" role="button" aria-expanded="true"
                        aria-controls="collapseCardExample"
                        style="background-color: #007F3E; border: 1px solid #007F3E;">
                        <h6 class="m-0 font-weight-bold text-white">DATA MADRASAH/SEKOLAH</h6>
                    </a>
                    <div class="collapse show" id="informasisiswa">
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    <tr><td>Nama Madrasah/Sekolah</td><td>: {{ $siswa->nama_lengkap }}</td></tr>
                                    <tr><td>NPSN</td><td>: {{ $siswa->nis }}</td></tr>
                                    <tr><td>Email</td><td>: {{ $siswa->email }}</td></tr>
                                    <tr><td>Status Akreditasi</td><td>: {{ $siswa->akreditasi }}</td></tr>
                                    <tr><td>Masa Akreditasi</td><td>: {{ $siswa->masaakreditasi }}</td></tr>
                                    <tr><td>Status Tanah</td><td>: {{ $siswa->statustanah }}</td></tr>
                                    <tr><td>Luas Tanah</td><td>: {{ $siswa->luastanah }}</td></tr>
                                    <tr><td>Kepemilikan Sertifikat Tanah</td><td>: {{ $siswa->sertifikat }}</td></tr>
                                    <tr><td>Kepemilikan BHPNU</td><td>: {{ $siswa->phbnu }}</td></tr>
                                    <tr><td>Alamat Madrasah/Sekolah</td><td>: {{ $siswa->alamat }}</td></tr>
                                    <tr><td>Jumlah Siswa (2024/2025)</td><td>: {{ $siswa->jumlahsiswa }} Siswa</td></tr>
                                    <tr><td>Jumlah GTY Sertifikasi Inpassing</td><td>: {{ $gty_sertifikasi_inpassing }} Tenaga Pendidik</td></tr>
                                    <tr><td>Jumlah GTY Sertifikasi Non Inpassing</td><td>: {{ $gty_sertifikasi_noninpassing }} Tenaga Pendidik</td></tr>
                                    <tr><td>Jumlah GTY Non Sertifikasi</td><td>: {{ $gty_nonsertifikasi }} Tenaga Pendidik</td></tr>
                                    <tr><td>Jumlah PNS Sertifikasi</td><td>: {{ $pns }} Tenaga Pendidik</td></tr>
                                    <tr><td>Jumlah PNS Non Sertifikasi</td><td>: {{ $pns_nonsertifikasi }} Tenaga Pendidik</td></tr>
                                    <tr><td>Jumlah GTT</td><td>: {{ $gtt }} Tenaga Pendidik</td></tr>
                                    <tr><td>Jumlah PTY</td><td>: {{ $pty }} Tenaga Pendidik</td></tr>
                                    <tr><td>Jumlah PTT</td><td>: {{ $ptt }} Tenaga Pendidik</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    @foreach($ketugasanCounts as $data)
                        <div class="col-md-3 mb-4">
                            <div class="card text-white h-100" style="background-color: #007F3E; border: 1px solid #007F3E;">
                                <div class="card-body">
                                    <h5 class="card-title text-white">{{ $data->ketugasan }}</h5>
                                    <p class="card-text">{{ $data->jumlah }} Orang</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tenaga Pendidik -->
                <div class="card shadow mb-4 border-bottom-success" id="infosiswa" value="0">
                    <a href="#" class="d-block card-header py-3"
                        data-toggle="collapse" role="button" aria-expanded="true"
                        aria-controls="collapseCardExample"
                        style="background-color: #007F3E; border: 1px solid #007F3E;">
                        <h6 class="m-0 font-weight-bold text-white">DATA GURU & PEGAWAI</h6>
                    </a>
                    <div class="container mt-4">
                        <table id="datatable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th width="30px">Image</th>
                                    <th>Nama Lengkap</th>
                                    <th>Status Kepegawaian</th>
                                    <th>Periode SK</th>
                                    <th>Ketugasan</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach ($tenagapendidik as $tp)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>
                                            @if ($tp->image != null)
                                                <img src="{{ asset('') }}storage/images/users/{{ $tp->image }}"
                                                    style="width: 40px; height: 50px; border-radius: 50%" alt="Gambar Kosong">
                                            @else
                                                <img src="{{ asset('') }}storage/images/users/users.png"
                                                    style="width: 40px; height: 40px; border-radius: 50%" alt="Gambar Kosong">
                                            @endif
                                        </td>
                                        <td>{{ $tp->nama_lengkap }}</td>
                                        <td>
                                            @if ($tp->jurusan_id == 1) Guru Tetap Yayasan
                                            @elseif ($tp->jurusan_id == 2) GTY Sertifikasi inpassing
                                            @elseif ($tp->jurusan_id == 3) GTY Sertifikasi Non Inpassing
                                            @elseif ($tp->jurusan_id == 4) Guru Tidak Tetap
                                            @elseif ($tp->jurusan_id == 5) Pegawai Negeri Sipil
                                            @elseif ($tp->jurusan_id == 6) Pegawai Tetap Yayasan
                                            @elseif ($tp->jurusan_id == 7) Pegawai Tidak Tetap
                                            @elseif ($tp->jurusan_id == 8) PNS Non Sertifikasi
                                            @endif
                                        </td>
                                        <td>
                                            @if ($tp->periode == 1) Januari
                                            @elseif ($tp->periode == 2) Juli
                                            @elseif ($tp->periode == 3) Kepala Madrasah/Sekolah
                                            @elseif ($tp->periode == null) Belum Valid
                                            @endif
                                        </td>
                                        <td>
                                            @switch($tp->ketugasan)
                                                @case(1) Mengajar Guru Kelas @break
                                                @case(2) Mengajar Guru Fikih @break
                                                @case(3) Mengajar PAI @break
                                                @case(4) Mengajar Mapel Bahasa Arab @break
                                                @case(5) Mengajar Mapel Akidah Akhlak @break
                                                @case(6) Mengajar Mapel Qu'an Hadis @break
                                                @case(7) Mengajar Mapel Matematika @break
                                                @case(8) Mengajar Mapel Bahasa Indonesia @break
                                                @case(9) Mengajar Mapel SKI @break
                                                @case(10) Mengajar PJOK @break
                                                @case(11) Mengajar Bahasa Jawa @break
                                                @case(12) Mengajar Mapel Bahasa Inggris @break
                                                @case(13) Mengajar Mapel IPA @break
                                                @case(14) Mengajar Mapel IPS @break
                                                @case(15) Mengajar Mapel PKN @break
                                                @case(16) Mengajar Mapel SBK @break
                                                @case(17) Tenaga Administrasi @break
                                                @case(18) Kepala Madrasah/Sekolah @break
                                                @case(19) Penjaga Sekolah/Madrasah @break
                                                @case(20) Mengajar TIK/Prakarya @break
                                                @case(21) Mengajar Guru BK @break
                                                @case(22) Mengajar Ke NU an @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="/siswa/open/{{ $tp->id }}" type="button" class="btn btn-danger">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if (request()->user()->role == 1)
                                                <!-- Tombol delete jika dibutuhkan -->
                                            @endif
                                        </td>
                                        <div class="modal fade" id="delete{{ $tp->id }}" tabindex="-1" role="dialog"
                                            aria-labelledby="deletemodal" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Hapus Guru/Pegawai</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Anda yakin ingin menghapus {{ $tp->nama_lengkap }}?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href="{{ url('/siswa/delete', $tp->id) }}" class="btn btn-primary">Hapus</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <a href="/profile_sekolah" type="button" class="btn btn-danger">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @if (request()->user()->role == 1)
@php
    // Contoh data invoice dengan perhitungan otomatis
    $invoices = $invoices ?? [
        [
            'invoice_number' => 'INV-001',
            'customer' => 'John Doe',
            'date' => '2025-02-23',
            'items' => [
                ['desc' => 'Product A', 'qty' => 2, 'price' => 50.00],
                ['desc' => 'Product B', 'qty' => 1, 'price' => 75.50],
            ],
        ],
        [
            'invoice_number' => 'INV-002',
            'customer' => 'Jane Smith',
            'date' => '2025-02-24',
            'items' => [
                ['desc' => 'Product C', 'qty' => 3, 'price' => 40.00],
                ['desc' => 'Product D', 'qty' => 2, 'price' => 60.00],
            ],
        ],
    ];
@endphp

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $i => $invoice)
                            @php
                                $total = 0;
                                foreach ($invoice['items'] as $item) {
                                    $total += $item['qty'] * $item['price']; // Hitung total invoice
                                }
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $invoice['invoice_number'] }}</td>
                                <td>{{ $invoice['customer'] }}</td>
                                <td>{{ $invoice['date'] }}</td>
                                <td class="text-end"><strong>${{ number_format($total, 2) }}</strong></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#invoice-{{ $i }}">
                                        View
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="p-0">
                                    <div id="invoice-{{ $i }}" class="collapse">
                                        <table class="table mb-0">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Price</th>
                                                    <th class="text-end">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($invoice['items'] as $item)
                                                    <tr>
                                                        <td>{{ $item['desc'] }}</td>
                                                        <td class="text-center">{{ $item['qty'] }}</td>
                                                        <td class="text-end">${{ number_format($item['price'], 2) }}</td>
                                                        <td class="text-end">${{ number_format($item['qty'] * $item['price'], 2) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="table-dark">
                                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                    <td class="text-end"><strong>${{ number_format($total, 2) }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
@endif --}}

@endsection
