@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                </div>
                @foreach ($payment as $p)
                    <div class="card-body">
                        <form action="/paymentAddProses" method="POST" id="payment-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">
                            <input type="hidden" name="result_type" id="result-type" value="">
                            <input type="hidden" name="result_data" id="result-data" value="">
                            <input type="hidden" name="installment" id="installment" value="0">
                            <input type="hidden" name="installment_term" id="installment_term_field" value="">

                            <input type="text" name="tagihan_id" id="tagihan_id" value="{{ $p->id }}" hidden>
                            <input type="text" name="user_id" id="user_id" value="{{ $p->user_id }}" hidden>
                            <input type="text" name="kelas_id" id="kelas_id" value="{{ $p->kelas_id }}" hidden>
                            <input type="text" name="nis" id="nis" value="{{ $p->nis }}" hidden>
                            <input type="text" name="email" id="email" value="{{ $p->email }}" hidden>
                            <input type="text" name="no_tlp" id="no_tlp" value="{{ $p->no_tlp }}" hidden>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="full_name">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                            value="{{ $p->nama_lengkap }}" readonly placeholder="Masukan Nama Lengkap"
                                            required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="full_name">Pembayaran</label>
                                        <input type="text" class="form-control" id="pembayaran" name="pembayaran"
                                            value="{{ $p->pembayaran }}" readonly placeholder="Masukan pembayaran"
                                            required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="full_name">Tahun</label>
                                        <input type="text" class="form-control" id="tahun" name="tahun"
                                            value="{{ $p->tahun }}" readonly placeholder="Masukan Tahun" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="full_name">Nilai</label>
                                        <input type="text" class="form-control" id="nilai" name="nilai"
                                            value="Rp. {{ number_format($p->nilai) }}" readonly placeholder="Masukan Nilai"
                                            required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="full_name">Status</label>
                                        <input type="text" class="form-control" id="status" name="status"
                                            value="{{ $p->status }}" readonly placeholder="Masukan Status" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="full_name">Metode Pembayaran</label>
                                        <select id="metode_pembayaran" class="form-control" name="metode_pembayaran"
                                            required>
                                            <option value="">Pilih Metode Pembayaran</option>
                                            @if (request()->user()->role != 1)
                                                <option value="Online">Online</option>
                                            @else
                                                <option value="Manual">Manual</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                @if (request()->user()->role == 3 && in_array($p->jenis_pembayaran, [14,26,19]))
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Cicilan (Installment)</label>
                                            <select id="installment_term" class="form-control" {{ isset($p->installment_locked) && $p->installment_locked ? 'disabled' : '' }}>
                                                <option value="">-- Pilih Cicilan --</option>
                                                <option value="2">2 Kali</option>
                                                <option value="3">3 Kali</option>
                                            </select>
                                            <small class="text-muted">Pilih 2x atau 3x cicilan. Jika kosong, bayar penuh.</small>
                                            <div id="installment_info" class="mt-2">
                                                @if(isset($p->installment_group) && $p->installment_group)
                                                    <div class="text-info">Cicilan aktif: {{ $p->installment_term }} kali. Sudah dibayar: {{ $p->installments_paid }}.</div>
                                                @endif
                                                <div id="installment_amount_preview" class="text-muted"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12 text-center">
                                    <br>
                                    <button type="submit" id="pay-button" class="btn btn-primary">Bayar</button>
                                    <a href="/pembayaran/search?&kelas_id={{ $p->kelas_id }}&nis={{ $p->nis }}"
                                        type="button" class="btn btn-success">Kembali</a>
                                </div>
                        </form>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
    <script type="text/javascript" src="https://app.midtrans.com/snap/snap.js"
        data-client-key="{{ Helper::apk()->clientKey }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript">
        $('#pay-button').click(function(event) {
            if ($('#metode_pembayaran').val() == "Online") {
                event.preventDefault();
                $(this).attr("disabled", "disabled");
                // console.log($('#nilai').val().replace("Rp.", '').replace(",", '').replace(".", ''));
                    // determine installment options if present
                    var installmentTerm = ($('#installment_term').length) ? $('#installment_term').val() : '';
                    var installmentFlag = installmentTerm ? 1 : 0;

                    // set hidden form fields so server gets installment info when form is submitted
                    $('#installment').val(installmentFlag);
                    $('#installment_term_field').val(installmentTerm);

                    $.ajax({
                        method: "POST",
                        url: '/getTokenPayment',
                        cache: false,
                        data: {
                            _token: $('#_token').val(),
                            nama_lengkap: $('#nama_lengkap').val(),
                            pembayaran: $('#pembayaran').val(),
                            tahun: $('#tahun').val(),
                            total: $('#nilai').val().replace("Rp.", '').replace(",", '').replace(".", ''),
                            email: $('#email').val(),
                            no_tlp: $('#no_tlp').val(),
                            installment: installmentFlag,
                            installment_term: installmentTerm

                        },
                    success: function(data) {
                        //location = data;
                        console.log('token = ' + data);

                        var resultType = document.getElementById('result-type');
                        var resultData = document.getElementById('result-data');

                        function changeResult(type, data) {
                            $("#result-type").val(type);
                            $("#result-data").val(JSON.stringify(data));
                            //resultType.innerHTML = type;
                            //resultData.innerHTML = JSON.stringify(data);
                        }
                        snap.pay(data, {

                            onSuccess: function(result) {
                                changeResult('success', result);
                                console.log(result.status_message);
                                console.log(result);
                                $("#payment-form").submit();
                            },
                            onPending: function(result) {
                                changeResult('pending', result);
                                console.log(result.status_message);
                                $("#payment-form").submit();
                            },
                            onError: function(result) {
                                changeResult('error', result);
                                console.log(result.status_message);
                                $("#payment-form").submit();
                            }
                        });
                    }
                });
            } else {
                $("#payment-form").submit();
            }
        });
        // preview installment amount when term selected
        $(document).ready(function() {
            function formatRupiah(number) {
                return 'Rp. ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            $('#installment_term').on('change', function() {
                var term = $(this).val();
                var raw = $('#nilai').val().replace(/Rp\.\s?/, '').replace(/\./g, '').replace(/,/g, '');
                var total = parseInt(raw) || 0;
                if (term && (term == '2' || term == '3')) {
                    var base = Math.floor(total / parseInt(term));
                    var last = total - (base * (term - 1));
                    var preview = '';
                    preview += 'Per pembayaran: ' + formatRupiah(base) + ' (kecuali pembayaran terakhir: ' + formatRupiah(last) + ')';
                    $('#installment_amount_preview').text(preview);
                } else {
                    $('#installment_amount_preview').text('');
                }
            });

            // if installment is locked on server, disable select and fill hidden field
            @if(isset($payment[0]) && isset($payment[0]->installment_group) && $payment[0]->installment_group)
                var locked = {{ $payment[0]->installment_locked ?? 0 }};
                var term = '{{ $payment[0]->installment_term ?? '' }}';
                if (term) {
                    $('#installment_term').val(term).trigger('change');
                }
                if (locked) {
                    $('#installment').val(1);
                    $('#installment_term_field').val(term);
                    $('#installment_term').prop('disabled', true);
                }
            @endif
        });
    </script>
@endsection
