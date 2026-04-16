@extends('backend.mobile_role2.layout')

@section('content')
    <section class="hero card">
        <div class="hero-row">
            <div>
                <div class="eyebrow">Pembayaran</div>
                <div class="title">Detail Pembayaran</div>
                <p class="subtitle">Selesaikan pembayaran dengan memilih metode di bawah.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card detail-card">
            @foreach ($payment as $p)
                <form action="/paymentAddProses" method="POST" id="payment-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">
                    <input type="hidden" name="result_type" id="result-type" value="">
                    <input type="hidden" name="result_data" id="result-data" value="">

                    <input type="hidden" name="tagihan_id" id="tagihan_id" value="{{ $p->id }}">
                    <input type="hidden" name="user_id" id="user_id" value="{{ $p->user_id }}">
                    <input type="hidden" name="kelas_id" id="kelas_id" value="{{ $p->kelas_id }}">
                    <input type="hidden" name="nis" id="nis" value="{{ $p->nis }}">
                    <input type="hidden" name="email" id="email" value="{{ $p->email }}">
                    <input type="hidden" name="no_tlp" id="no_tlp" value="{{ $p->no_tlp }}">

                    <div class="detail-row">
                        <div class="label">Pembayaran</div>
                        <div class="value">{{ $p->pembayaran }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="label">Tahun</div>
                        <div class="value">{{ $p->tahun ?? '-' }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="label">Nominal</div>
                        <div class="value">Rp{{ number_format($p->nilai) }}</div>
                    </div>

                    <div class="detail-row">
                        <div class="label">Status Tagihan</div>
                        <div class="value">{{ $p->status ?? '-' }}</div>
                    </div>

                    <div style="margin-top:12px">
                        <label class="label" for="metode_pembayaran" style="display:block;margin-bottom:6px;color:var(--muted);font-size:12px">Metode Pembayaran</label>
                        <select id="metode_pembayaran" class="form-control" name="metode_pembayaran" required style="width:100%;padding:10px;border-radius:10px;border:1px solid var(--border);">
                            <option value="">Pilih Metode Pembayaran</option>
                            @if (request()->user()->role != 1)
                                <option value="Online">Online</option>
                            @else
                                <option value="Manual">Manual</option>
                            @endif
                        </select>
                    </div>

                    <div style="margin-top:18px; display:flex; gap:10px;">
                        <button type="button" id="pay-button" class="action" style="flex:1">Bayar</button>
                        <a href="{{ route('mobile.role2.pembayaran') }}" class="action secondary" style="flex:1;justify-content:center">Kembali</a>
                    </div>
                </form>
            @endforeach
        </div>
    </section>

    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ \App\Providers\Helper::apk()->clientKey }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').addEventListener('click', function (event) {
            var metode = document.getElementById('metode_pembayaran').value;
            if (metode === 'Online') {
                event.preventDefault();
                this.setAttribute('disabled', 'disabled');

                var tokenData = {
                    _token: document.getElementById('_token').value,
                    nama_lengkap: document.getElementById('nama_lengkap') ? document.getElementById('nama_lengkap').value : '',
                    pembayaran: document.querySelector('[name="pembayaran"]') ? document.querySelector('[name="pembayaran"]').value : document.querySelector('.value').innerText,
                    tahun: document.getElementById('tahun') ? document.getElementById('tahun').value : '',
                    total: document.querySelector('.value') ? document.querySelector('.value').innerText.replace(/[^0-9]/g, '') : '',
                    email: document.getElementById('email').value,
                    no_tlp: document.getElementById('no_tlp').value
                };

                // fallback: post to token endpoint similarly to desktop
                $.ajax({
                    method: "POST",
                    url: '/getTokenPayment',
                    cache: false,
                    data: tokenData,
                    success: function (data) {
                        var resultType = document.getElementById('result-type');
                        var resultData = document.getElementById('result-data');

                        function changeResult(type, data) {
                            document.getElementById('result-type').value = type;
                            document.getElementById('result-data').value = JSON.stringify(data);
                        }

                        snap.pay(data, {
                            onSuccess: function(result) {
                                changeResult('success', result);
                                document.getElementById('payment-form').submit();
                            },
                            onPending: function(result) {
                                changeResult('pending', result);
                                document.getElementById('payment-form').submit();
                            },
                            onError: function(result) {
                                changeResult('error', result);
                                document.getElementById('payment-form').submit();
                            }
                        });
                    },
                    error: function () {
                        alert('Gagal membuat token pembayaran. Silakan coba lagi.');
                        document.getElementById('pay-button').removeAttribute('disabled');
                    }
                });
            } else {
                document.getElementById('payment-form').submit();
            }
        });
    </script>
@endsection
