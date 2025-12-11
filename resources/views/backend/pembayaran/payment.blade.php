@extends('backend.layout.base')

@section('content')
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="font-size: 40px">{{ $title }}</h5>
                </div>

                <div class="card-body">
                    <form action="/paymentAddProses" method="POST" id="payment-form" enctype="multipart/form-data">
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
                            user_id: $('input[name="user_id"]').val(),
                            tagihan_id: $('input[name="tagihan_id"]').val(),
                            kelas_id: $('input[name="kelas_id"]').val(),
                            nama_lengkap: $('#nama_lengkap').val(),
                            pembayaran: $('#pembayaran').val(),
                            tahun: $('#tahun').val(),
                            total: parseInt(nilai.replace(/\./g, '').replace(/,/g, '')), // Pastikan angka bersih
                        },
                        success: function(response) {
                            console.log('Midtrans Response:', response);
                            if (response.success && response.snap_token) {
                                // Store order_id untuk digunakan nanti
                                $("#result-data").data('order_id', response.order_id);
                                handlePaymentResponse(response.snap_token);
                            } else {
                                alert('Gagal mendapatkan token pembayaran: ' + (response.message || 'Unknown error'));
                                $("#pay-button").removeAttr("disabled");
                            }
                        },
                        error: function(err) {
                            console.error('Error:', err);
                            let errorMsg = 'Gagal mendapatkan token pembayaran.';
                            if (err.responseJSON && err.responseJSON.message) {
                                errorMsg = err.responseJSON.message;
                            }
                            alert(errorMsg);
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
                    console.log('Payment Success:', result);
                    submitPayment('success', result);
                    // Jangan langsung redirect, tunggu proses submit payment selesai
                    setTimeout(() => {
                        redirectToPreviousPage();
                    }, 2000);
                },
                onPending: function(result) {
                    console.log('Payment Pending:', result);
                    submitPayment('pending', result);
                    setTimeout(() => {
                        redirectToPreviousPage();
                    }, 2000);
                },
                onError: function(result) {
                    console.log('Payment Error:', result);
                    alert('Pembayaran gagal. Silahkan coba lagi.');
                    $("#pay-button").removeAttr("disabled");
                }
            });
        }

        function redirectToPreviousPage() {
            window.history.back(); // Mengarahkan kembali ke halaman sebelumnya
        }


        function submitPayment(type, data) {
            $("#result-type").val(type);
            $("#result-data").val(JSON.stringify(data));
            $("#payment-form").submit();
        }
    </script>
@endsection
