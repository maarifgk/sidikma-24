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

                            <input type="text" name="tagihan_id" id="tagihan_id" value="{{ $p->id }}" hidden>
                            <input type="text" name="user_id" id="user_id" value="{{ $p->user_id }}" hidden>
                            <input type="text" name="kelas_id" id="kelas_id" value="{{ $p->kelas_id }}" hidden>
                            <input type="text" name="nis" id="nis" value="{{ $p->nis }}" hidden>
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
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ Helper::apk()->clientKey }}"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script type="text/javascript">
        $('#pay-button').click(function(event) {
            if ($('#metode_pembayaran').val() == "Online") {
                event.preventDefault();
                $(this).attr("disabled", "disabled");
                // console.log($('#nilai').val().replace("Rp.", '').replace(",", '').replace(".", ''));
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
    </script>
@endsection
