@extends('backend.layout.base')

@section('content')
    @php
        $schoolIdentity = strtoupper(trim(($siswa->nama_lengkap ?? '') . ' ' . ($siswa->kelas_id ?? '')));
        $kelasId = (int) ($siswa->kelas_id ?? 0);
        $isMiSchool = preg_match('/\bMI\b/u', strtoupper($siswa->nama_lengkap ?? '')) === 1 || ($kelasId >= 1 && $kelasId <= 63);
        $isMtsOrSmpSchool = preg_match('/\b(MTS|SMP)\b/u', strtoupper($siswa->nama_lengkap ?? '')) === 1 || $kelasId > 63;
        $schoolPhoto = !empty($admin->image)
            ? asset('storage/images/users/' . $admin->image)
            : asset('storage/images/logo/logo sidikma gk.png');
    @endphp

    <style>
        .admin-edit-page {
            --admin-primary: #12643a;
            --admin-primary-dark: #0d4f30;
            --admin-blue: #1d6fa5;
            --admin-blue-dark: #114d76;
            --admin-soft: #e8f4f1;
            --admin-soft-blue: #edf6fc;
            --admin-border: rgba(18, 100, 58, 0.12);
            --admin-text: #163024;
            --admin-muted: #6f7f77;
            position: relative;
        }

        .admin-edit-page::before,
        .admin-edit-page::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
            z-index: 0;
        }

        .admin-edit-page::before {
            width: 240px;
            height: 240px;
            top: -20px;
            right: 2%;
            background: rgba(29, 111, 165, 0.08);
        }

        .admin-edit-page::after {
            width: 180px;
            height: 180px;
            bottom: 12px;
            left: 1%;
            background: rgba(18, 100, 58, 0.08);
        }

        .admin-edit-page > * {
            position: relative;
            z-index: 1;
        }

        .admin-hero-card,
        .admin-form-card,
        .admin-section-card {
            border: 0;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 40px rgba(21, 53, 40, 0.08);
        }

        .admin-hero-card {
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(29, 111, 165, 0.08), transparent 28%),
                radial-gradient(circle at bottom left, rgba(18, 100, 58, 0.08), transparent 22%),
                linear-gradient(180deg, #ffffff 0%, #fbfefd 100%);
        }

        .admin-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.45rem 0.78rem;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--admin-soft) 0%, var(--admin-soft-blue) 100%);
            color: var(--admin-primary);
            font-size: 0.77rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .admin-hero-title {
            margin: 0.95rem 0 0.35rem;
            color: var(--admin-text);
            font-size: clamp(1.8rem, 2.4vw, 2.55rem);
            line-height: 1.15;
        }

        .admin-hero-subtitle {
            margin: 0;
            color: var(--admin-muted);
            font-size: 0.98rem;
            line-height: 1.7;
        }

        .admin-hero-media {
            display: flex;
            justify-content: center;
        }

        .admin-hero-photo {
            width: min(100%, 260px);
            aspect-ratio: 16 / 10;
            border-radius: 24px;
            object-fit: cover;
            border: 1px solid rgba(18, 100, 58, 0.1);
            background: #fff;
            box-shadow: 0 16px 32px rgba(21, 53, 40, 0.08);
        }

        .admin-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.9rem;
            margin-top: 1.2rem;
        }

        .admin-summary-item {
            padding: 1rem 1rem 0.95rem;
            border-radius: 20px;
            border: 1px solid var(--admin-border);
            background: rgba(255, 255, 255, 0.86);
        }

        .admin-summary-item small {
            display: block;
            margin-bottom: 0.35rem;
            color: var(--admin-muted);
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
        }

        .admin-summary-item strong {
            color: var(--admin-text);
            font-size: 1rem;
            overflow-wrap: anywhere;
        }

        .admin-form-card {
            padding: 1.35rem;
        }

        .admin-section-card {
            padding: 1.2rem;
            height: 100%;
        }

        .admin-section-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-bottom: 0.9rem;
            border-bottom: 1px solid rgba(18, 100, 58, 0.08);
        }

        .admin-section-head h5 {
            margin: 0;
            color: var(--admin-text);
            font-size: 1.05rem;
            font-weight: 700;
        }

        .admin-section-head p {
            margin: 0.35rem 0 0;
            color: var(--admin-muted);
            font-size: 0.88rem;
            line-height: 1.6;
        }

        .admin-section-icon {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            background: linear-gradient(135deg, var(--admin-soft) 0%, var(--admin-soft-blue) 100%);
            color: var(--admin-blue-dark);
            font-size: 1rem;
        }

        .admin-edit-page .form-label {
            margin-bottom: 0.45rem;
            color: var(--admin-text);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .admin-edit-page .form-control,
        .admin-edit-page .form-select {
            border-radius: 16px;
            border-color: rgba(18, 100, 58, 0.14);
            padding: 0.82rem 0.95rem;
            color: var(--admin-text);
            box-shadow: none;
        }

        .admin-edit-page textarea.form-control {
            min-height: 122px;
            resize: vertical;
        }

        .admin-edit-page .form-control:focus,
        .admin-edit-page .form-select:focus {
            border-color: rgba(18, 100, 58, 0.36);
            box-shadow: 0 0 0 4px rgba(18, 100, 58, 0.08);
        }

        .admin-edit-page .form-text {
            color: var(--admin-muted);
            font-size: 0.8rem;
        }

        .admin-grid-tight {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 0.35rem;
        }

        .admin-class-group {
            display: none;
        }

        .admin-class-group.is-visible {
            display: block;
        }

        .admin-total-card {
            margin-top: 0.4rem;
            padding: 1rem;
            border-radius: 18px;
            border: 1px dashed rgba(18, 100, 58, 0.2);
            background: linear-gradient(135deg, rgba(232, 244, 241, 0.78) 0%, rgba(237, 246, 252, 0.78) 100%);
        }

        .admin-total-card strong {
            display: block;
            color: var(--admin-text);
            font-size: 1.3rem;
            line-height: 1.2;
        }

        .admin-total-card span {
            color: var(--admin-muted);
            font-size: 0.84rem;
        }

        .admin-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.4rem;
        }

        .admin-actions .btn {
            border-radius: 16px;
            padding: 0.82rem 1.2rem;
            font-weight: 700;
        }

        .admin-actions .btn-primary {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-blue) 100%);
            border: 0;
            box-shadow: 0 14px 28px rgba(21, 53, 40, 0.12);
        }

        .admin-actions .btn-success {
            background: #fff;
            border: 1px solid rgba(18, 100, 58, 0.14);
            color: var(--admin-text);
        }

        .admin-actions .btn-success:hover {
            background: linear-gradient(135deg, var(--admin-soft) 0%, var(--admin-soft-blue) 100%);
            color: var(--admin-text);
            border-color: rgba(18, 100, 58, 0.18);
        }

        @media (max-width: 991.98px) {
            .admin-summary-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .admin-form-card {
                padding: 1rem;
            }

            .admin-section-card {
                padding: 1rem;
            }

            .admin-hero-card .card-body {
                padding: 1.1rem !important;
            }

            .admin-actions {
                justify-content: stretch;
            }

            .admin-actions .btn {
                width: 100%;
            }
        }
    </style>

    <div class="admin-edit-page">
        <div class="card admin-hero-card mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="admin-kicker">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>{{ $title }}</span>
                        </span>
                        <h1 class="admin-hero-title">Kelola profil madrasah/sekolah dengan tampilan yang lebih rapi</h1>
                        <p class="admin-hero-subtitle">
                            Perbarui identitas lembaga, data kesiswaan, dan informasi sarpras dalam satu halaman yang
                            lebih terstruktur dan mudah dibaca.
                        </p>

                        <div class="admin-summary-grid">
                            <div class="admin-summary-item">
                                <small>Nama Lembaga</small>
                                <strong>{{ $siswa->nama_lengkap ?: 'Belum diisi' }}</strong>
                            </div>
                            <div class="admin-summary-item">
                                <small>NPSN</small>
                                <strong>{{ $siswa->nis ?: '-' }}</strong>
                            </div>
                            <div class="admin-summary-item">
                                <small>Status Akun</small>
                                <strong>{{ $admin->status ?: '-' }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="admin-hero-media">
                            <img src="{{ $schoolPhoto }}" alt="Foto Madrasah/Sekolah" class="admin-hero-photo">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card admin-form-card">
            <form action="{{ route('admin.editProses') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $admin->id }}">

                @if (request()->user()->role != 2)
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="admin-section-card">
                                <div class="admin-section-head">
                                    <div>
                                        <h5>Data Madrasah/Sekolah</h5>
                                        <p>Informasi utama akun dan identitas lembaga.</p>
                                    </div>
                                    <span class="admin-section-icon">
                                        <i class="fa-solid fa-school"></i>
                                    </span>
                                </div>

                                <div class="row admin-grid-tight">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="nis">NPSN</label>
                                            <input type="text" class="form-control" id="nis" name="nis"
                                                value="{{ $siswa->nis }}" placeholder="Masukkan NPSN">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="nama_lengkap">Nama Madrasah/Sekolah</label>
                                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                                value="{{ $siswa->nama_lengkap }}" placeholder="Masukkan nama madrasah/sekolah">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="kelas_id">Asal Madrasah/Sekolah</label>
                                            <select class="form-select" name="kelas_id" id="kelas_id">
                                                <option value="">-- Pilih --</option>
                                                @foreach ($kelas as $k)
                                                    <option value="{{ $k->id }}" {{ $k->id == $siswa->kelas_id ? 'selected' : '' }}>
                                                        {{ $k->nama_kelas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="email">Email Madrasah/Sekolah</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ $siswa->email }}" placeholder="Masukkan email">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="no_tlp">Nomor Telepon</label>
                                            <input type="text" class="form-control" id="no_tlp" name="no_tlp"
                                                value="{{ $siswa->no_tlp }}" placeholder="Masukkan nomor telepon">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="password">Password Baru Aplikasi</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Kosongkan jika tidak ingin mengubah password">
                                            <div class="form-text">Password hanya diperbarui jika field ini diisi.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="role">Role</label>
                                            <select class="form-select" name="role" id="role" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="3" {{ (string) $admin->role === '3' ? 'selected' : '' }}>Akun Madrasah/Sekolah</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="status">Status</label>
                                            <select class="form-select" name="status" id="status" required>
                                                <option value="">-- Pilih --</option>
                                                @foreach ($status as $s)
                                                    <option value="{{ $s }}" {{ $s == $admin->status ? 'selected' : '' }}>
                                                        {{ $s }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="image">Foto Madrasah/Sekolah</label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                            <div class="form-text">Disarankan format landscape agar tampil lebih proporsional.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="alamat">Alamat Madrasah/Sekolah</label>
                                            <textarea class="form-control" id="alamat" name="alamat" placeholder="Masukkan alamat">{{ trim($siswa->alamat) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-7">
                            <div class="admin-section-card">
                                <div class="admin-section-head">
                                    <div>
                                        <h5>Data Kesiswaan</h5>
                                        <p>Jumlah siswa ditampilkan mengikuti jenjang MI atau MTs/SMP agar lebih fokus.</p>
                                    </div>
                                    <span class="admin-section-icon">
                                        <i class="fa-solid fa-users"></i>
                                    </span>
                                </div>

                                <div class="row admin-grid-tight">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="thn_pelajaran">Tahun Pelajaran</label>
                                            <input type="text" class="form-control" id="thn_pelajaran" name="thn_pelajaran"
                                                value="{{ $siswa->thn_pelajaran ?: '2024-2025' }}" placeholder="Contoh: 2024-2025" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="admin-class-group {{ $isMiSchool && !$isMtsOrSmpSchool ? 'is-visible' : '' }}" id="kelas-group-mi">
                                    <div class="row admin-grid-tight">
                                        @for ($i = 1; $i <= 6; $i++)
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="kelas{{ $i }}">Jumlah Siswa Kelas {{ $i }}</label>
                                                    <input type="number" min="0" class="form-control kelas-count kelas-mi"
                                                        id="kelas{{ $i }}" name="kelas{{ $i }}" value="{{ $siswa->{'kelas' . $i} ?? 0 }}"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="admin-class-group {{ $isMtsOrSmpSchool ? 'is-visible' : '' }}" id="kelas-group-mts">
                                    <div class="row admin-grid-tight">
                                        @for ($i = 7; $i <= 9; $i++)
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label" for="kelas{{ $i }}">Jumlah Siswa Kelas {{ $i }}</label>
                                                    <input type="number" min="0" class="form-control kelas-count kelas-mts"
                                                        id="kelas{{ $i }}" name="kelas{{ $i }}" value="{{ $siswa->{'kelas' . $i} ?? 0 }}"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                <div class="admin-total-card">
                                    <strong id="jumlahsiswa-preview">{{ $siswa->jumlahsiswa ?? 0 }} siswa</strong>
                                    <span>Total siswa akan dihitung otomatis dari kelas yang aktif.</span>
                                </div>
                                <input type="hidden" id="jumlahsiswa" name="jumlahsiswa" value="{{ $siswa->jumlahsiswa ?? 0 }}">
                            </div>
                        </div>

                        <div class="col-xl-5">
                            <div class="admin-section-card">
                                <div class="admin-section-head">
                                    <div>
                                        <h5>Data Sarpras</h5>
                                        <p>Lengkapi status akreditasi, tanah, dan dokumen kelembagaan.</p>
                                    </div>
                                    <span class="admin-section-icon">
                                        <i class="fa-solid fa-building-circle-check"></i>
                                    </span>
                                </div>

                                <div class="row admin-grid-tight">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="akreditasi">Status Akreditasi</label>
                                            <input type="text" class="form-control" id="akreditasi" name="akreditasi"
                                                value="{{ $siswa->akreditasi }}" placeholder="Masukkan status akreditasi" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="masaakreditasi">Masa Akreditasi</label>
                                            <input type="text" class="form-control" id="masaakreditasi" name="masaakreditasi"
                                                value="{{ $siswa->masaakreditasi }}" placeholder="Masukkan masa akreditasi" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="statustanah">Status Tanah</label>
                                            <input type="text" class="form-control" id="statustanah" name="statustanah"
                                                value="{{ $siswa->statustanah }}" placeholder="Masukkan status tanah" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="luastanah">Luas Tanah</label>
                                            <input type="text" class="form-control" id="luastanah" name="luastanah"
                                                value="{{ $siswa->luastanah }}" placeholder="Masukkan luas tanah" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="sertifikat">Kepemilikan Sertifikat</label>
                                            <select class="form-select" name="sertifikat" id="sertifikat" required>
                                                <option value="">-- Pilih --</option>
                                                @foreach ($k_sertifikat as $k)
                                                    <option value="{{ $k->keterangan }}" {{ $k->keterangan == $siswa->sertifikat ? 'selected' : '' }}>
                                                        {{ $k->keterangan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="atasnama">Atas Nama</label>
                                            <input type="text" class="form-control" id="atasnama" name="atasnama"
                                                value="{{ $siswa->atasnama }}" placeholder="Masukkan atas nama" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="phbnu">Kepemilikan PHBNU</label>
                                            <select class="form-select" name="phbnu" id="phbnu" required>
                                                <option value="">-- Pilih --</option>
                                                @foreach ($phbnu as $p)
                                                    <option value="{{ $p->keterangan }}" {{ $p->keterangan == $siswa->phbnu ? 'selected' : '' }}>
                                                        {{ $p->keterangan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="admin-actions">
                    <a href="{{ url()->previous() }}" class="btn btn-success">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const kelasIdSelect = document.getElementById('kelas_id');
            const schoolNameInput = document.getElementById('nama_lengkap');
            const miGroup = document.getElementById('kelas-group-mi');
            const mtsGroup = document.getElementById('kelas-group-mts');
            const totalField = document.getElementById('jumlahsiswa');
            const totalPreview = document.getElementById('jumlahsiswa-preview');
            const allClassInputs = document.querySelectorAll('.kelas-count');

            function getSchoolType() {
                const kelasId = parseInt(kelasIdSelect?.value || '0', 10);
                const schoolName = (schoolNameInput?.value || '').toUpperCase();

                if (schoolName.includes('MI') || (kelasId >= 1 && kelasId <= 63)) {
                    return 'mi';
                }

                if (schoolName.includes('MTS') || schoolName.includes('SMP') || kelasId > 63) {
                    return 'mts';
                }

                return 'mi';
            }

            function toggleClassGroups() {
                const type = getSchoolType();
                miGroup.classList.toggle('is-visible', type === 'mi');
                mtsGroup.classList.toggle('is-visible', type === 'mts');
                updateTotal();
            }

            function updateTotal() {
                const type = getSchoolType();
                let total = 0;

                allClassInputs.forEach((input) => {
                    const isMiInput = input.classList.contains('kelas-mi');
                    const isMtsInput = input.classList.contains('kelas-mts');

                    if ((type === 'mi' && isMiInput) || (type === 'mts' && isMtsInput)) {
                        total += parseInt(input.value || '0', 10) || 0;
                    }
                });

                totalField.value = total;
                totalPreview.textContent = `${total} siswa`;
            }

            allClassInputs.forEach((input) => input.addEventListener('input', updateTotal));
            kelasIdSelect?.addEventListener('change', toggleClassGroups);
            schoolNameInput?.addEventListener('input', toggleClassGroups);

            toggleClassGroups();
        });
    </script>
@endsection
