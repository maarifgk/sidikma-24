@extends('backend.layout.base')

@section('content')
    @include('backend.administrasi._theme')
    @php
        $userRole = (int) request()->user()->role;
        $isReviewer = in_array($userRole, [1, 4], true);
        $rows = $userRole === 3 ? $perm_proposal : $proposal;
        $requirements = ['Surat permohonan/proposal dalam format PDF', 'Jenis permohonan bantuan atau proposal', 'Nominal bantuan yang diajukan', 'Nama bank, nomor rekening, dan pemilik rekening'];
        $info = json_decode(Helper::apk()->info_proposal ?? '', true) ?: [];
        $note = $info['nb'] ?? "Jika terdapat kendala atau error silakan hubungi admin LP. Ma'arif NU PCNU Gunungkidul.";
    @endphp

    <div class="proposal-page">
        <section class="proposal-hero">
            <div><span class="proposal-eyebrow"><i class="fa-solid fa-file-signature"></i> Administrasi</span><h1>Usulan SK</h1><p>Kelola pengajuan, kelengkapan berkas, dan proses persetujuan dalam satu tampilan.</p></div>
            @if (in_array($userRole, [1, 3], true))<a href="{{ route('proposal.add') }}" class="btn proposal-primary"><i class="fa-solid fa-plus me-1"></i> Ajukan Usulan</a>@endif
        </section>

        <section class="proposal-requirements">
            <div class="proposal-section-heading"><div><span class="proposal-section-label">Sebelum mengajukan</span><h2>Kelengkapan syarat</h2></div>@if ($userRole === 1)<a href="{{ route('proposal.info.edit') }}" class="btn btn-sm btn-light"><i class="fa-solid fa-pen me-1"></i> Edit info</a>@endif</div>
            <div class="proposal-checklist">@foreach ($requirements as $index => $requirement)<div class="proposal-check-item"><span>{{ $index + 1 }}</span><p>{{ $requirement }}</p></div>@endforeach</div>
            <div class="proposal-note"><i class="fa-solid fa-circle-info"></i><span>{!! nl2br(e($note)) !!}</span>@if (!empty($info['link_proposal']))<a href="{{ $info['link_proposal'] }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success">Buka template</a>@endif</div>
        </section>

        <section class="proposal-list-card">
            <div class="proposal-section-heading"><div><span class="proposal-section-label">Data pengajuan</span><h2>{{ $isReviewer ? 'Daftar usulan masuk' : 'Riwayat usulan' }}</h2></div><span class="proposal-count">{{ $rows->count() }} usulan</span></div>
            <div class="table-responsive"><table id="proposal-datatable" class="table proposal-table align-middle"><thead><tr><th>Pengusul</th><th>Jenis & nominal</th><th>Status</th><th>Dokumen</th><th>Catatan</th><th class="text-end">Aksi</th></tr></thead><tbody>
                @forelse ($rows as $row)
                    @php $status = strtolower($row->status ?? ''); $statusClass = str_contains($status, 'tidak') ? 'is-danger' : (str_contains($status, 'setuju') && !str_contains($status, 'tidak') ? 'is-success' : 'is-warning'); @endphp
                    <tr><td><div class="proposal-school">{{ $row->nama_kelas ?? 'Madrasah tidak ditemukan' }}</div><small>#{{ $row->id }}</small></td><td><div class="proposal-type">{{ $row->jenis_proposal ?: '-' }}</div><strong class="proposal-amount">{{ $row->nominal ?: '-' }}</strong></td><td><span class="proposal-status {{ $statusClass }}"><i class="fa-solid fa-circle"></i>{{ $row->status ?: 'Belum diproses' }}</span></td><td class="proposal-docs"><a href="{{ route('proposal.file', $row->id) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-regular fa-file-pdf me-1"></i> Proposal</a>@if ($row->approve_proposal)<a href="{{ route('proposal.file.approved', $row->id) }}" target="_blank" class="btn btn-sm btn-outline-success">Disetujui</a>@endif</td><td class="proposal-comment">{{ $row->keterangan_ditolak ?: ($row->catatan ?: '-') }}</td><td class="text-end proposal-actions">@if ($isReviewer && $row->status === 'Dalam Peninjauan')<a href="{{ route('proposal.open', $row->id) }}" class="btn btn-sm btn-success">Proses</a><button type="button" class="btn btn-sm btn-outline-danger btn-tolak" data-id="{{ $row->id }}">Tolak</button>@endif @if ($userRole === 1)<a href="{{ route('proposal.delete', $row->id) }}" class="btn btn-sm btn-outline-secondary" onclick="return confirm('Hapus usulan ini?')"><i class="fa-solid fa-trash"></i></a>@endif</td></tr>
                @empty
                    <tr><td colspan="6" class="proposal-empty"><i class="fa-regular fa-folder-open"></i><strong>Belum ada usulan</strong><span>Data pengajuan akan tampil di sini.</span></td></tr>
                @endforelse
            </tbody></table></div>
        </section>
    </div>

    <style>
        .proposal-page{--green:#12643a;--blue:#1d6fa5;--ink:#183328;--muted:#718078;color:var(--ink)}.proposal-hero,.proposal-requirements,.proposal-list-card{border:1px solid rgba(18,100,58,.1);border-radius:20px;background:#fff;box-shadow:0 12px 28px rgba(21,53,40,.06)}.proposal-hero{display:flex;justify-content:space-between;align-items:center;gap:1rem;padding:1.35rem 1.5rem;margin-bottom:1rem;background:linear-gradient(120deg,#fff 35%,#eff8f4)}.proposal-eyebrow,.proposal-section-label{color:var(--green);font-size:.72rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase}.proposal-eyebrow{display:inline-flex;gap:.4rem;padding:.35rem .65rem;border-radius:999px;background:#e8f4f1}.proposal-hero h1{margin:.55rem 0 .2rem;font-size:1.65rem;font-weight:800}.proposal-hero p{margin:0;color:var(--muted);font-size:.88rem}.proposal-primary{border:0;border-radius:10px;padding:.7rem 1rem;color:#fff;background:linear-gradient(135deg,var(--green),var(--blue));font-weight:700;white-space:nowrap}.proposal-requirements,.proposal-list-card{padding:1rem 1.1rem;margin-bottom:1rem}.proposal-section-heading{display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:.85rem}.proposal-section-heading h2{margin:.2rem 0 0;font-size:1.05rem;font-weight:800}.proposal-checklist{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.55rem}.proposal-check-item{display:flex;gap:.55rem;align-items:flex-start;padding:.65rem .7rem;border:1px solid #e3eee9;border-radius:12px;background:#fbfefd}.proposal-check-item span{flex:0 0 1.4rem;display:grid;place-items:center;width:1.4rem;height:1.4rem;border-radius:50%;background:#e8f4f1;color:var(--green);font-size:.72rem;font-weight:800}.proposal-check-item p{margin:.03rem 0 0;font-size:.8rem;line-height:1.35}.proposal-note{display:flex;align-items:center;gap:.5rem;margin-top:.7rem;padding:.55rem .7rem;color:#735b16;background:#fff8e8;border-radius:10px;font-size:.78rem}.proposal-note i{color:#ca991d}.proposal-note span{flex:1}.proposal-count{color:var(--green);padding:.35rem .65rem;border-radius:999px;background:#e8f4f1;font-size:.76rem;font-weight:700}.proposal-table{min-width:900px;margin:0}.proposal-table th{padding:.65rem .7rem;color:#6e7e76;background:#f5faf8;border-bottom:1px solid #e3eee9;font-size:.7rem;letter-spacing:.06em;text-transform:uppercase;white-space:nowrap}.proposal-table td{padding:.72rem .7rem;border-bottom:1px solid #eef3f0;font-size:.8rem}.proposal-table tr:last-child td{border-bottom:0}.proposal-school,.proposal-type{max-width:190px;font-weight:700;line-height:1.35}.proposal-table small{color:#93a099}.proposal-amount{display:block;margin-top:.18rem;color:var(--green);font-size:.76rem}.proposal-status{display:inline-flex;align-items:center;gap:.35rem;padding:.32rem .55rem;border-radius:999px;font-size:.72rem;font-weight:700;white-space:nowrap}.proposal-status i{font-size:.42rem}.is-warning{color:#8b6812;background:#fff4d4}.is-success{color:#147145;background:#e4f6ec}.is-danger{color:#a33f3b;background:#fde9e8}.proposal-docs{white-space:nowrap}.proposal-docs .btn{margin:.1rem}.proposal-comment{max-width:170px;color:#728078}.proposal-actions{white-space:nowrap}.proposal-empty{padding:2.5rem!important;text-align:center;color:#7a8981}.proposal-empty i,.proposal-empty strong,.proposal-empty span{display:block}.proposal-empty i{margin-bottom:.5rem;font-size:1.5rem}.proposal-empty strong{color:var(--ink)}@media(max-width:900px){.proposal-checklist{grid-template-columns:repeat(2,minmax(0,1fr))}.proposal-hero{align-items:flex-start;flex-direction:column}.proposal-primary{width:100%;text-align:center}}@media(max-width:520px){.proposal-checklist{grid-template-columns:1fr}.proposal-note{align-items:flex-start;flex-direction:column}}
    </style>
@endsection

@section('js')
<script>
$(function () {
    $('#proposal-datatable').DataTable({ pageLength: 10, order: [], language: { search: 'Cari:', lengthMenu: '_MENU_ data', info: '_START_–_END_ dari _TOTAL_' } });
    $('.btn-tolak').on('click', function () {
        const id = $(this).data('id');
        Swal.fire({ title: 'Tolak usulan?', input: 'text', inputLabel: 'Alasan penolakan', inputPlaceholder: 'Masukkan alasan...', showCancelButton: true, confirmButtonText: 'Tolak', cancelButtonText: 'Batal', inputValidator: value => !value ? 'Alasan wajib diisi.' : undefined }).then(result => {
            if (!result.isConfirmed) return;
            fetch('{{ route('proposal.ubahStatus') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ id, status: 'Tidak disetujui', keterangan_ditolak: result.value }) }).then(response => { if (!response.ok) throw new Error('Gagal menyimpan status'); return response.json(); }).then(() => location.reload()).catch(() => Swal.fire('Gagal', 'Status usulan tidak dapat diperbarui.', 'error'));
        });
    });
});
</script>
@endsection
