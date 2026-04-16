@extends('backend.layout.base')

@section('content')
    <div class="card table-responsive">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="fw-bold">{{ $title }}</h3>
            @if (in_array(request()->user()->role, [3, 1]))
            <a href="/proposal/add" type="button" class="btn rounded-pill btn-primary justify-content-end"
                style="margin-left: 70%;">Ajukan</a>
                @endif
        </div>

        <div class="card-body">
            <div class="card shadow mb-4 border-bottom-success" id="infosiswa" value="0">
                <!-- Card Header - Accordion -->
                <a href="#" class="d-block card-header py-3"
                                data-toggle="collapse" role="button" aria-expanded="true" style="background-color: #007F3E;"
                                aria-controls="collapseCardExample">
                    <h6 class="m-0 font-weight-bold text-white">DATA PENGAJUAN PROPOSAL</h6>
                </a>
                <div class="collapse show" id="informasisiswa">
                    <div class="card-body">
                        @if (!empty(Helper::apk()->info_proposal))
                            @php $raw = Helper::apk()->info_proposal; $decoded = json_decode($raw, true); @endphp
                            @if (request()->user()->role == 1)
                                <div class="mb-2">
                                    <a href="/proposal/info/edit" class="btn btn-sm btn-warning">Edit</a>
                                </div>
                            @endif
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>1.</td>
                                        <td>{{ e($decoded['label_1'] ?? '') }}</td>
                                    </tr>
                                    <tr>
                                        <td>2.</td>
                                        <td>{{ e($decoded['label_2'] ?? '') }}</td>
                                    </tr>
                                    <tr>
                                        <td>3.</td>
                                        <td>{{ e($decoded['label_3'] ?? '') }}</td>
                                    </tr>
                                    <tr>
                                        <td>4.</td>
                                        <td>{{ e($decoded['label_4'] ?? '') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="m-0 font-weight-bold text-danger">NB:</td>
                                        <td>{!! nl2br(e($decoded['nb'] ?? '')) !!}
                                            @php $link = $decoded['link_proposal'] ?? null; @endphp
                                            @if (!empty($link))
                                                <div class="mt-2">
                                                    <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="btn btn-danger">Download / Info</a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            @if (request()->user()->role == 1)
                                <div class="mb-2">
                                    <a href="/proposal/info/edit" class="btn btn-sm btn-warning">Edit</a>
                                </div>
                            @endif
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td>1. Dokumen Surat Permohonan Bantuan/Proposal dalam bentuk File PDF</td>
                                    </tr>
                                    <tr>
                                        <td>2. Jenis Permohonan Bantuan/Proposal</td>
                                    </tr>
                                    <tr>
                                        <td>3. Nominal yang diajukan</td>
                                    </tr>
                                    <tr>
                                        <td>4. Nama Bank & Nomor Rekening</td>
                                    </tr>
                                    <tr>
                                        <td class="m-0 font-weight-bold text-danger">NB: Jika terdapat kendala atau error silahkan hubungi admin LP. Ma'arif NU PCNU Gunungkidul</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if (request()->user()->role == 3)
        <div class="container mt-4 ">
            <table id="datatable" class="table table-striped ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Asal Madrasah</th>
                        <th>Jenis Perm. Bantuan/Proposal</th>
                        <th>File</th>
                        <th>Nominal</th>
                        <th>Ket. Proses</th>
                        <th>Keterangan</th>
                        <th>File Aprove</th>
                        <th>Nominal diterima</th>
                        @if (in_array(request()->user()->role, [1, 4,]))
                        <th>Catatan</th>
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($perm_proposal as $t)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td> @if ($t->kelas_id == 1)
                                MI YAPPI Badongan
                            @elseif ($t->kelas_id == 10)
                                MI YAPPI Baleharjo
                            @elseif ($t->kelas_id == 11)
                                MI YAPPI Balong
                            @elseif ($t->kelas_id == 12)
                                MI YAPPI Banjaran
                            @elseif ($t->kelas_id == 13)
                                MI YAPPI Bansari
                            @elseif ($t->kelas_id == 14)
                                MI YAPPI Banyusoco
                            @elseif ($t->kelas_id == 15)
                                MI YAPPI Batusari
                            @elseif ($t->kelas_id == 16)
                                MI YAPPI Cekel
                            @elseif ($t->kelas_id == 17)
                                MI YAPPI Doga
                            @elseif ($t->kelas_id == 18)
                                MI YAPPI Dondong
                            @elseif ($t->kelas_id == 19)
                                MI YAPPI Gedad I
                            @elseif ($t->kelas_id == 20)
                                MI YAPPI Gedad II
                            @elseif ($t->kelas_id == 21)
                                MI YAPPI Gubukrubuh
                            @elseif ($t->kelas_id == 22)
                                MI YAPPI Kalangan
                            @elseif ($t->kelas_id == 23)
                                MI YAPPI Kalongan
                            @elseif ($t->kelas_id == 24)
                                MI YAPPI Karang
                            @elseif ($t->kelas_id == 26)
                                MI YAPPI Karangtritis
                            @elseif ($t->kelas_id == 27)
                                MI YAPPI Karangwetan
                            @elseif ($t->kelas_id == 28)
                                MI YAPPI Kedungwanglu
                            @elseif ($t->kelas_id == 29)
                                MI YAPPI Klepu
                            @elseif ($t->kelas_id == 30)
                                MI YAPPI Mulusan
                            @elseif ($t->kelas_id == 31)
                                MI YAPPI Natah
                            @elseif ($t->kelas_id == 32)
                                MI YAPPI Ngembes
                            @elseif ($t->kelas_id == 33)
                                MI YAPPI Nglebeng
                            @elseif ($t->kelas_id == 34)
                                MI YAPPI Ngleri
                            @elseif ($t->kelas_id == 35)
                                MI YAPPI Ngrancang
                            @elseif ($t->kelas_id == 36)
                                MI YAPPI Ngunut
                            @elseif ($t->kelas_id == 37)
                                MI YAPPI Ngrati
                            @elseif ($t->kelas_id == 38)
                                MI YAPPI Nologaten
                            @elseif ($t->kelas_id == 39)
                                MI YAPPI Payak
                            @elseif ($t->kelas_id == 40)
                                MI YAPPI Peyuyon
                            @elseif ($t->kelas_id == 41)
                                MI YAPPI Pijenan
                            @elseif ($t->kelas_id == 42)
                                MI YAPPI Plalar
                            @elseif ($t->kelas_id == 43)
                                MI YAPPI Pucung
                            @elseif ($t->kelas_id == 44)
                                MI YAPPI Purwo
                            @elseif ($t->kelas_id == 45)
                                MI YAPPI Putat
                            @elseif ($t->kelas_id == 46)
                                MI YAPPI Randukuning
                            @elseif ($t->kelas_id == 47)
                                MI YAPPI Rejosari
                            @elseif ($t->kelas_id == 48)
                                MI YAPPI Ringintumpang
                            @elseif ($t->kelas_id == 49)
                                MI YAPPI Sawahan
                            @elseif ($t->kelas_id == 50)
                                MI YAPPI Semoyo
                            @elseif ($t->kelas_id == 51)
                                MI YAPPI Sendang
                            @elseif ($t->kelas_id == 52)
                                MI YAPPI Tambakromo
                            @elseif ($t->kelas_id == 53)
                                MI YAPPI Tanjung
                            @elseif ($t->kelas_id == 54)
                                MI YAPPI Tegalweru
                            @elseif ($t->kelas_id == 55)
                                MI YAPPI Tekik
                            @elseif ($t->kelas_id == 57)
                                MI YAPPI Tobong
                            @elseif ($t->kelas_id == 58)
                                MI YAPPI Wiyoko
                            @elseif ($t->kelas_id == 60)
                                MI Maarif Mulo
                            @elseif ($t->kelas_id == 62)
                                MI Maarif Wareng
                            @elseif ($t->kelas_id == 63)
                                MI Wasathiyah
                            @elseif ($t->kelas_id == 65)
                                MTs YAPPI Dengok
                            @elseif ($t->kelas_id == 66)
                                MTs YAPPI Jetis
                            @elseif ($t->kelas_id == 67)
                                MTs YAPPI Kenteng
                            @elseif ($t->kelas_id == 68)
                                MTs YAPPI Mulusan
                            @elseif ($t->kelas_id == 70)
                                MTs YAPPI Sumberjo
                            @elseif ($t->kelas_id == 71)
                                MTs Jamul Muawanah
                            @elseif ($t->kelas_id == 72)
                                SMP Persiapan Semanu
                            @elseif ($t->kelas_id == 74)
                                SMP Pembangunan I Karangmojo
                            @elseif ($t->kelas_id == 75)
                                SMP Pembangunan Ponjong
                            @elseif ($t->kelas_id == 76)
                                SMP Pembangunan Semin
                            @endif</td>
                            <td width="auto">{{ $t->jenis_proposal }}</td>
                            <td>
                                <a href="{{ asset('') }}storage/dokumen/proposal/{{ $t->proposal }}" class="btn btn-primary" view=""><i class="fa-regular fa-file"></i></a>
                            </td>
                            <td width="auto">{{ $t->nominal }}</td>
                            <td width="auto">{{ $t->status }}</td>
                            <td width="auto">{{ $t->keterangan_ditolak }}</td>
                            <td width="auto">
                                @if ($t->approve_proposal)
                                <a href="{{ asset('') }}storage/dokumen/approve_proposal/{{ $t->approve_proposal }}" class="btn btn-primary" target="_blank"><i class="fa-regular fa-file"></i></a>
                                @endif
                            <td width="auto">{{ $t->nominal_acc }}</td>
                            @if (in_array(request()->user()->role, [1, 4,]))
                            <td width="auto">{{ $t->catatan }}</td>
                            <td width="200">
                                @if ($t->status == 'Dalam Peninjauan')
                                <a href="/proposal/open/{{ $t->id }}" type="button" class="btn btn-success">Proses</a>
                                @endif
                                {{-- <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#delete{{ $t->id }}"><i class="fa-solid fa-trash-can"></i></button> --}}
                            </td>
                            @endif
                            <div class="modal fade" id="delete{{ $t->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="deletemodal" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addNewDonaturLabel">Hapus Data Usulan
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda yakin ingin menghapus Data Pemohonan Bantuan/Proposal {{ $t->kelas_id }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <a href="{{ url('/proposal/delete', $t->id) }} "
                                                    class="btn btn-primary">Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if (in_array(request()->user()->role, [1, 4,]))
        <div class="container mt-4 ">
            <table id="datatable" class="table table-striped ">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Asal Madrasah</th>
                        <th>Jenis Perm. Bantuan/Proposal</th>
                        <th>File</th>
                        <th>Nominal</th>
                        <th>Ket. Proses</th>
                        <th>Keterangan</th>
                        <th>File Aprove</th>
                        <th>Nominal diterima</th>
                        @if (in_array(request()->user()->role, [1, 4,]))
                        <th>Catatan</th>
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @foreach ($proposal as $t)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td> @if ($t->kelas_id == 1)
                                MI YAPPI Badongan
                            @elseif ($t->kelas_id == 10)
                                MI YAPPI Baleharjo
                            @elseif ($t->kelas_id == 11)
                                MI YAPPI Balong
                            @elseif ($t->kelas_id == 12)
                                MI YAPPI Banjaran
                            @elseif ($t->kelas_id == 13)
                                MI YAPPI Bansari
                            @elseif ($t->kelas_id == 14)
                                MI YAPPI Banyusoco
                            @elseif ($t->kelas_id == 15)
                                MI YAPPI Batusari
                            @elseif ($t->kelas_id == 16)
                                MI YAPPI Cekel
                            @elseif ($t->kelas_id == 17)
                                MI YAPPI Doga
                            @elseif ($t->kelas_id == 18)
                                MI YAPPI Dondong
                            @elseif ($t->kelas_id == 19)
                                MI YAPPI Gedad I
                            @elseif ($t->kelas_id == 20)
                                MI YAPPI Gedad II
                            @elseif ($t->kelas_id == 21)
                                MI YAPPI Gubukrubuh
                            @elseif ($t->kelas_id == 22)
                                MI YAPPI Kalangan
                            @elseif ($t->kelas_id == 23)
                                MI YAPPI Kalongan
                            @elseif ($t->kelas_id == 24)
                                MI YAPPI Karang
                            @elseif ($t->kelas_id == 26)
                                MI YAPPI Karangtritis
                            @elseif ($t->kelas_id == 27)
                                MI YAPPI Karangwetan
                            @elseif ($t->kelas_id == 28)
                                MI YAPPI Kedungwanglu
                            @elseif ($t->kelas_id == 29)
                                MI YAPPI Klepu
                            @elseif ($t->kelas_id == 30)
                                MI YAPPI Mulusan
                            @elseif ($t->kelas_id == 31)
                                MI YAPPI Natah
                            @elseif ($t->kelas_id == 32)
                                MI YAPPI Ngembes
                            @elseif ($t->kelas_id == 33)
                                MI YAPPI Nglebeng
                            @elseif ($t->kelas_id == 34)
                                MI YAPPI Ngleri
                            @elseif ($t->kelas_id == 35)
                                MI YAPPI Ngrancang
                            @elseif ($t->kelas_id == 36)
                                MI YAPPI Ngunut
                            @elseif ($t->kelas_id == 37)
                                MI YAPPI Ngrati
                            @elseif ($t->kelas_id == 38)
                                MI YAPPI Nologaten
                            @elseif ($t->kelas_id == 39)
                                MI YAPPI Payak
                            @elseif ($t->kelas_id == 40)
                                MI YAPPI Peyuyon
                            @elseif ($t->kelas_id == 41)
                                MI YAPPI Pijenan
                            @elseif ($t->kelas_id == 42)
                                MI YAPPI Plalar
                            @elseif ($t->kelas_id == 43)
                                MI YAPPI Pucung
                            @elseif ($t->kelas_id == 44)
                                MI YAPPI Purwo
                            @elseif ($t->kelas_id == 45)
                                MI YAPPI Putat
                            @elseif ($t->kelas_id == 46)
                                MI YAPPI Randukuning
                            @elseif ($t->kelas_id == 47)
                                MI YAPPI Rejosari
                            @elseif ($t->kelas_id == 48)
                                MI YAPPI Ringintumpang
                            @elseif ($t->kelas_id == 49)
                                MI YAPPI Sawahan
                            @elseif ($t->kelas_id == 50)
                                MI YAPPI Semoyo
                            @elseif ($t->kelas_id == 51)
                                MI YAPPI Sendang
                            @elseif ($t->kelas_id == 52)
                                MI YAPPI Tambakromo
                            @elseif ($t->kelas_id == 53)
                                MI YAPPI Tanjung
                            @elseif ($t->kelas_id == 54)
                                MI YAPPI Tegalweru
                            @elseif ($t->kelas_id == 55)
                                MI YAPPI Tekik
                            @elseif ($t->kelas_id == 57)
                                MI YAPPI Tobong
                            @elseif ($t->kelas_id == 58)
                                MI YAPPI Wiyoko
                            @elseif ($t->kelas_id == 60)
                                MI Maarif Mulo
                            @elseif ($t->kelas_id == 62)
                                MI Maarif Wareng
                            @elseif ($t->kelas_id == 63)
                                MI Wasathiyah
                            @elseif ($t->kelas_id == 65)
                                MTs YAPPI Dengok
                            @elseif ($t->kelas_id == 66)
                                MTs YAPPI Jetis
                            @elseif ($t->kelas_id == 67)
                                MTs YAPPI Kenteng
                            @elseif ($t->kelas_id == 68)
                                MTs YAPPI Mulusan
                            @elseif ($t->kelas_id == 70)
                                MTs YAPPI Sumberjo
                            @elseif ($t->kelas_id == 71)
                                MTs Jamul Muawanah
                            @elseif ($t->kelas_id == 72)
                                SMP Persiapan Semanu
                            @elseif ($t->kelas_id == 74)
                                SMP Pembangunan I Karangmojo
                            @elseif ($t->kelas_id == 75)
                                SMP Pembangunan Ponjong
                            @elseif ($t->kelas_id == 76)
                                SMP Pembangunan Semin
                            @endif</td>
                            <td width="auto">{{ $t->jenis_proposal }}</td>
                            <td>
                                <a href="{{ asset('') }}storage/dokumen/proposal/{{ $t->proposal }}" class="btn btn-primary" view=""><i class="fa-regular fa-file"></i></a>
                            </td>
                            <td width="auto">{{ $t->nominal }}</td>
                            <td width="auto">{{ $t->status }}</td>
                            <td width="auto">{{ $t->keterangan_ditolak }}</td>
                            <td width="auto">
                                @if ($t->approve_proposal)
                                <a href="{{ asset('') }}storage/dokumen/approve_proposal/{{ $t->approve_proposal }}" class="btn btn-primary" target="_blank"><i class="fa-regular fa-file"></i></a>
                                @endif
                            <td width="auto">{{ $t->nominal_acc }}</td>
                            @if (in_array(request()->user()->role, [1, 4,]))
                            <td width="auto">{{ $t->catatan }}</td>
                            <td width="200">
                                @if ($t->status == 'Dalam Peninjauan')
                                <a href="/proposal/open/{{ $t->id }}" type="button" class="btn btn-success">Proses</a>
                                <button type="button" class="btn btn-danger btn-tolak" data-id="{{ $t->id }}">
                                    Tolak
                                </button>
                                @endif
                                @if (request()->user()->role == 1)
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#delete{{ $t->id }}"><i class="fa-solid fa-trash-can"></i></button>
                                @endif
                            </td>
                            @endif
                            <div class="modal fade" id="delete{{ $t->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="deletemodal" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addNewDonaturLabel">Hapus Data Usulan
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda yakin ingin menghapus Data Pemohonan Bantuan/Proposal {{ $t->kelas_id }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <a href="{{ url('/proposal/delete', $t->id) }} "
                                                    class="btn btn-primary">Hapus</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.btn-tolak');

            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    const proposalId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Keterangan Tidak Disetujui',
                        input: 'text',
                        inputLabel: 'Masukkan alasan tidak disetujui',
                        inputPlaceholder: 'Contoh: Proposal tidak lengkap',
                        showCancelButton: true,
                        confirmButtonText: 'Kirim',
                        cancelButtonText: 'Batal',
                        inputValidator: (value) => {
                            if (!value) return 'Keterangan wajib diisi!';
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Kirim data ke backend
                            fetch("{{ route('proposal.ubahStatus') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    id: proposalId,
                                    status: 'Tidak disetujui',
                                    keterangan_ditolak: result.value
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                Swal.fire('Berhasil!', 'Proposal disetujui.', 'success')
                                    .then(() => location.reload());
                            })
                            .catch(error => {
                                console.error('Fetch error:', error); // Tampilkan error detail di console
                                Swal.fire('Gagal', 'Terjadi kesalahan saat mengirim data.', 'error');
                            });
                        }
                    });
                });
            });
        });
    </script>

@endsection
