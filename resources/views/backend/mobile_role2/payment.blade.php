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
                    {{-- Hidden fields expected by SnapController/getTokenPayment --}}
                    <input type="hidden" name="nama_lengkap" id="nama_lengkap" value="{{ $p->nama_lengkap }}">
                    <input type="hidden" name="pembayaran" id="pembayaran" value="{{ $p->pembayaran }}">
                    <input type="hidden" name="tahun" id="tahun" value="{{ $p->tahun }}">
                    <input type="hidden" name="nilai" id="nilai" value="Rp{{ number_format($p->nilai) }}">

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
                        <button type="button" id="pay-button" class="action" style="flex:1" disabled aria-disabled="true">Bayar</button>
                        <a href="{{ route('mobile.role2.pembayaran') }}" class="action secondary" style="flex:1;justify-content:center">Kembali</a>
                    </div>
                </form>
            @endforeach
        </div>
    </section>

    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ \App\Providers\Helper::apk()->clientKey }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript">
        // Disable pay button until a payment method is selected
        var metodeSelect = document.getElementById('metode_pembayaran');
        var payButton = document.getElementById('pay-button');

        function updatePayButton() {
            if (!metodeSelect) return;
            if (metodeSelect.value && metodeSelect.value !== '') {
                payButton.removeAttribute('disabled');
                payButton.setAttribute('aria-disabled', 'false');
            } else {
                payButton.setAttribute('disabled', 'disabled');
                payButton.setAttribute('aria-disabled', 'true');
            }
        }

        if (metodeSelect) {
            metodeSelect.addEventListener('change', updatePayButton);
            // initialize state
            updatePayButton();
        }

        payButton.addEventListener('click', function (event) {
            var metode = document.getElementById('metode_pembayaran').value;
            if (metode === 'Online') {
                event.preventDefault();
                this.setAttribute('disabled', 'disabled');

                var tokenData = {
                    _token: document.getElementById('_token').value,
                    nama_lengkap: document.getElementById('nama_lengkap') ? document.getElementById('nama_lengkap').value : '',
                    pembayaran: document.getElementById('pembayaran') ? document.getElementById('pembayaran').value : '',
                    tahun: document.getElementById('tahun') ? document.getElementById('tahun').value : '',
                    total: document.getElementById('nilai') ? document.getElementById('nilai').value.replace(/[^0-9]/g, '') : '',
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
                    error: function (jqXHR) {
                        var msg = 'Gagal membuat token pembayaran. Silakan coba lagi.';
                        try {
                            var body = jqXHR.responseJSON || JSON.parse(jqXHR.responseText || '{}');
                            if (body && body.error) msg = body.error;
                        } catch (e) {
                            // ignore parse errors
                        }
                        alert(msg);
                        document.getElementById('pay-button').removeAttribute('disabled');
                    }
                });
            } else {
                document.getElementById('payment-form').submit();
            }
        });
    </script>
@endsection
