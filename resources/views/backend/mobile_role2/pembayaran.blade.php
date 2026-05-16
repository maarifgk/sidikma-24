@extends('backend.mobile_role2.layout')

@section('content')
    <section class="hero card">
        <div class="hero-row">
            <div>
                <div class="eyebrow">Pembayaran SK Yayasan</div>
                <div class="title">Tagihan Terkait SK</div>
                {{-- <p class="subtitle">Filter mengikuti tagihan user login dan jenis pembayaran yang mengandung `SK` atau `Yayasan`.</p> --}}
            </div>
        </div>
    </section>

    <section class="section">
        <div class="grid-2">
            <div class="card metric">
                <div class="label">Total Tagihan</div>
                <div class="value">{{ $summary['total'] }}</div>
                <div class="hint">Tagihan SK Yayasan</div>
            </div>
            <div class="card metric">
                <div class="label">Nominal</div>
                <div class="value">Rp{{ number_format($summary['nominal']) }}</div>
                <div class="hint">Akumulasi nilai tagihan</div>
            </div>
            <div class="card metric">
                <div class="label">Lunas</div>
                <div class="value">{{ $summary['lunas'] }}</div>
                <div class="hint">Sudah dibayarkan</div>
            </div>
            <div class="card metric">
                <div class="label">Pending</div>
                <div class="value">{{ $summary['pending'] }}</div>
                <div class="hint">Menunggu penyelesaian</div>
            </div>
        </div>
    </section>

    @if (!empty($hasUnpaidIuran) && $hasUnpaidIuran)
        <section class="section">
            <div class="card" style="border:1px solid rgba(185, 28, 28, 0.18); background:linear-gradient(180deg, rgba(254, 242, 242, 1) 0%, rgba(255, 255, 255, 1) 100%);">
                <div style="padding:16px 18px;">
                    <div style="font-size:12px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#b91c1c;">Pembayaran Dikunci</div>
                    <div style="margin-top:6px; color:#7f1d1d;">{{ $paymentBlockedMessage }}</div>
                </div>
            </div>
        </section>
    @endif

    <section class="section">
        <div class="section-head">
            <h3>Daftar Pembayaran</h3>
            <span>{{ $profile->nama_kelas ?? '-' }}</span>
        </div>
        <div class="card list-card">
            @forelse ($payments as $payment)
                <div class="list-item" style="align-items: center;">
                    <div>
                        <div class="item-title">{{ $payment->pembayaran }}</div>
                        <div class="item-subtitle">{{ $payment->tahun ?? '-' }} • Rp{{ number_format($payment->nilai) }}</div>
                        <div class="item-meta">{{ $payment->metode_pembayaran ?? 'Belum ada metode pembayaran' }}</div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                        <span class="badge {{ $payment->status_payment === 'Lunas' ? 'success' : ($payment->status_payment === 'Pending' ? 'warning' : 'danger') }}">
                            {{ $payment->status_payment ?? 'Belum Bayar' }}
                        </span>
                        @if ($payment->status_payment === 'Pending' && $payment->pdf_url)
                            <a href="{{ $payment->pdf_url }}" target="_blank" class="action secondary">Invoice</a>
                        @elseif ($payment->status_payment === 'Lunas')
                            <a href="{{ url('/lainyaPdf/' . $payment->id) }}" target="_blank" class="action secondary">PDF</a>
                        @else
                            @if(!empty($hasUnpaidIuran) && $hasUnpaidIuran)
                                <a href="#" class="action disabled" data-message="{{ $paymentBlockedMessage }}">Bayar</a>
                            @else
                                <a href="{{ url('/mobile/role-2/pembayaran/payment/' . $payment->id) }}" class="action">Bayar</a>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">Belum ada pembayaran SK Yayasan untuk akun ini.</div>
            @endforelse
        </div>
    </section>

    {{-- Tampilkan session error (server-side redirect) --}}
    @if(session('error'))
        <script>
            // Tampilkan alert setelah load jika server-side mengembalikan error
            document.addEventListener('DOMContentLoaded', function () {
                alert(@json(session('error')));
            });
        </script>
    @endif

    <script>
        // Tangkap klik pada tombol Bayar yang disabled dan tampilkan keterangan
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('a.action.disabled').forEach(function (el) {
                el.addEventListener('click', function (e) {
                    e.preventDefault();
                    var msg = el.getAttribute('data-message') || 'Terdapat tagihan iuran yang belum lunas.';
                    // Gunakan alert sederhana agar kompatibel di mobile webview
                    alert(msg);
                });
            });
        });
    </script>
@endsection
