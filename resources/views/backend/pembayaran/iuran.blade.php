@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                </div>

                <div class="card-body">
                    <form action="/iuranAddProses" method="POST" id="payment-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="result_type" id="result-type">
                        <input type="hidden" name="result_data" id="result-data">

                        @foreach ($payment as $p)
                            <input type="hidden" name="tagihan_id" value="{{ $p->id }}">
                            <input type="hidden" name="user_id" value="{{ $p->user_id }}">
                            <input type="hidden" name="kelas_id" value="{{ $p->kelas_id }}">
                            <input type="hidden" name="nis" value="{{ $p->nis }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                            value="{{ htmlspecialchars($p->nama_lengkap) }}" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Pembayaran</label>
                                        <input type="text" class="form-control" id="pembayaran" name="pembayaran"
                                            value="{{ htmlspecialchars($p->pembayaran) }}" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tahun</label>
                                        <input type="text" class="form-control" id="tahun" name="tahun"
                                            value="{{ $p->tahun }}" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Nilai</label>
                                        <input type="text" class="form-control" id="nilai" name="nilai"
                                            value="Rp. {{ number_format($p->nilai, 0, ',', '.') }}" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <input type="text" class="form-control" id="status" name="status"
                                            value="{{ $p->status }}" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Metode Pembayaran</label>
                                        <select id="metode_pembayaran" class="form-control" name="metode_pembayaran" required>
                                            <option value="">Pilih Metode Pembayaran</option>
                                            @if (request()->user()->role != 1)
                                                <option value="Online">Online</option>
                                            @else
                                                <option value="Manual">Manual</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="text-center mt-3">
                            <button type="submit" id="pay-button" class="btn btn-primary">Bayar</button>
                            <a href="/pembayaran/search?kelas_id={{ $p->kelas_id }}&nis={{ $p->nis }}" class="btn btn-success">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ Helper::apk()->clientKey }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#pay-button').click(function(event) {
                let metode = $('#metode_pembayaran').val();
                let nilai = $('#nilai').val().replace(/[Rp.]/g, '').trim(); // Menghapus format Rp.

                if (metode === "Online") {
                    event.preventDefault();
                    $(this).attr("disabled", "disabled");

                    $.ajax({
                        method: "POST",
                        url: '/getTokenPayment',
                        cache: false,
                        data: {
                            _token: $('#_token').val(),
                            nama_lengkap: $('#nama_lengkap').val(),
                            pembayaran: $('#pembayaran').val(),
                            tahun: $('#tahun').val(),
                            total: parseInt(nilai.replace(",", "")), // Pastikan angka bersih
                        },
                        success: function(data) {
                            console.log('Token Midtrans:', data);
                            handlePaymentResponse(data);
                        },
                        error: function(err) {
                            alert('Gagal mendapatkan token pembayaran.');
                            console.error(err);
                            $("#pay-button").removeAttr("disabled");
                        }
                    });
                } else {
                    $("#payment-form").submit();
                }
            });
        });

        function handlePaymentResponse(token) {
            snap.pay(token, {
                onSuccess: function(result) {
                    submitPayment('success', result);
                },
                onPending: function(result) {
                    submitPayment('pending', result);
                },
                onError: function(result) {
                    submitPayment('error', result);
                }
            });
        }

        function submitPayment(type, data) {
            $("#result-type").val(type);
            $("#result-data").val(JSON.stringify(data));
            $("#payment-form").submit();
        }
    </script>
@endsection
