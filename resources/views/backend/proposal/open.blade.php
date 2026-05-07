@extends('backend.layout.base')

@section('content')
<div class="row">
    <div class="col-xl">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="collapse show" id="informasisiswa">
                    <div class="card-body">
                    <form action="/proposal/openProses" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="text" name="id" value="{{ $proposal->id }}" hidden>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th  width="300px" class="mb-0" style="font-size: 20px">{{ $title }}</th>
                                    <th  width="1000px"></th>
                                </tr>   
                                <tr>
                                    <td>Asal Madrasah/Sekolah</td>
                                    <td>: @if ($proposal->kelas_id == 1)
                                        MI YAPPI Badongan
                                    @elseif ($proposal->kelas_id == 10)
                                        MI YAPPI Baleharjo
                                    @elseif ($proposal->kelas_id == 11)
                                        MI YAPPI Balong
                                    @elseif ($proposal->kelas_id == 12)
                                        MI YAPPI Banjaran
                                    @elseif ($proposal->kelas_id == 13)
                                        MI YAPPI Bansari
                                    @elseif ($proposal->kelas_id == 14)
                                        MI YAPPI Banyusoco
                                    @elseif ($proposal->kelas_id == 15)
                                        MI YAPPI Batusari
                                    @elseif ($proposal->kelas_id == 16)
                                        MI YAPPI Cekel
                                    @elseif ($proposal->kelas_id == 17)
                                        MI YAPPI Doga
                                    @elseif ($proposal->kelas_id == 18)
                                        MI YAPPI Dondong
                                    @elseif ($proposal->kelas_id == 19)
                                        MI YAPPI Gedad I
                                    @elseif ($proposal->kelas_id == 20)
                                        MI YAPPI Gedad II
                                    @elseif ($proposal->kelas_id == 21)
                                        MI YAPPI Gubukrubuh
                                    @elseif ($proposal->kelas_id == 22)
                                        MI YAPPI Kalangan
                                    @elseif ($proposal->kelas_id == 23)
                                        MI YAPPI Kalongan
                                    @elseif ($proposal->kelas_id == 24)
                                        MI YAPPI Karang
                                    @elseif ($proposal->kelas_id == 26)
                                        MI YAPPI Karangtritis
                                    @elseif ($proposal->kelas_id == 27)
                                        MI YAPPI Karangwetan
                                    @elseif ($proposal->kelas_id == 28)
                                        MI YAPPI Kedungwanglu
                                    @elseif ($proposal->kelas_id == 29)
                                        MI YAPPI Klepu
                                    @elseif ($proposal->kelas_id == 30)
                                        MI YAPPI Mulusan
                                    @elseif ($proposal->kelas_id == 31)
                                        MI YAPPI Natah
                                    @elseif ($proposal->kelas_id == 32)
                                        MI YAPPI Ngembes
                                    @elseif ($proposal->kelas_id == 33)
                                        MI YAPPI Nglebeng
                                    @elseif ($proposal->kelas_id == 34)
                                        MI YAPPI Ngleri
                                    @elseif ($proposal->kelas_id == 35)
                                        MI YAPPI Ngrancang
                                    @elseif ($proposal->kelas_id == 36)
                                        MI YAPPI Ngunut
                                    @elseif ($proposal->kelas_id == 37)
                                        MI YAPPI Ngrati
                                    @elseif ($proposal->kelas_id == 38)
                                        MI YAPPI Nologaten
                                    @elseif ($proposal->kelas_id == 39)
                                        MI YAPPI Payak
                                    @elseif ($proposal->kelas_id == 40)
                                        MI YAPPI Peyuyon
                                    @elseif ($proposal->kelas_id == 41)
                                        MI YAPPI Pijenan
                                    @elseif ($proposal->kelas_id == 42)
                                        MI YAPPI Plalar
                                    @elseif ($proposal->kelas_id == 43)
                                        MI YAPPI Pucung
                                    @elseif ($proposal->kelas_id == 44)
                                        MI YAPPI Purwo
                                    @elseif ($proposal->kelas_id == 45)
                                        MI YAPPI Putat
                                    @elseif ($proposal->kelas_id == 46)
                                        MI YAPPI Randukuning
                                    @elseif ($proposal->kelas_id == 47)
                                        MI YAPPI Rejosari
                                    @elseif ($proposal->kelas_id == 48)
                                        MI YAPPI Ringintumpang
                                    @elseif ($proposal->kelas_id == 49)
                                        MI YAPPI Sawahan
                                    @elseif ($proposal->kelas_id == 50)
                                        MI YAPPI Semoyo
                                    @elseif ($proposal->kelas_id == 51)
                                        MI YAPPI Sendang
                                    @elseif ($proposal->kelas_id == 52)
                                        MI YAPPI Tambakromo
                                    @elseif ($proposal->kelas_id == 53)
                                        MI YAPPI Tanjung
                                    @elseif ($proposal->kelas_id == 54)
                                        MI YAPPI Tegalweru
                                    @elseif ($proposal->kelas_id == 55)
                                        MI YAPPI Tekik
                                    @elseif ($proposal->kelas_id == 57)
                                        MI YAPPI Tobong
                                    @elseif ($proposal->kelas_id == 58)
                                        MI YAPPI Wiyoko
                                    @elseif ($proposal->kelas_id == 60)
                                        MI Maarif Mulo 
                                    @elseif ($proposal->kelas_id == 62)
                                        MI Maarif Wareng
                                    @elseif ($proposal->kelas_id == 63)
                                        MI Wasathiyah
                                    @elseif ($proposal->kelas_id == 65)
                                        MTs YAPPI Dengok
                                    @elseif ($proposal->kelas_id == 66)
                                        MTs YAPPI Jetis
                                    @elseif ($proposal->kelas_id == 67)
                                        MTs YAPPI Kenteng
                                    @elseif ($proposal->kelas_id == 68)
                                        MTs YAPPI Mulusan
                                    @elseif ($proposal->kelas_id == 70)
                                        MTs YAPPI Sumberjo
                                    @elseif ($proposal->kelas_id == 71)
                                        MTs Jamul Muawanah
                                    @elseif ($proposal->kelas_id == 72)
                                        SMP Persiapan Semanu
                                    @elseif ($proposal->kelas_id == 74)
                                        SMP Pembangunan I Karangmojo
                                    @elseif ($proposal->kelas_id == 75)
                                        SMP Pembangunan Ponjong
                                    @elseif ($proposal->kelas_id == 76)
                                        SMP Pembangunan Semin
                                    @endif</td>
                                </tr>
                                <tr>
                                    <td>Jenis Proposal</td>
                                    <td>: {{ $proposal->jenis_proposal }}</td>
                                </tr>
                                <tr>
                                    <td>File Proposal PDF</td>
                                    <td>: <a href="{{ route('proposal.file', $proposal->id) }}" class="btn btn-success" target="_blank">View</a></td>
                                </tr>
                                <tr>
                                    <td>Nominal diajukan</td>
                                    <td>: {{ $proposal->nominal }}</td>
                                </tr>
                                <tr>
                                    <td>Nama Bank</td>
                                    <td>: {{ $proposal->nama_bank}}</td>
                                </tr>
                                <tr>
                                    <td>Nomor Rekening</td>
                                    <td>: {{ $proposal->no_rekening }}</td>
                                </tr>
                                <tr>
                                    <td>Atas Nama Rekening</td>
                                    <td>: {{ $proposal->an_rekening }}</td>
                                </tr>
                                <tr>
                                    <td>Nominal Bantuan</td>
                                    <td><input type="text" class="form-control" id="nominal_acc" name="nominal_acc"
                                            placeholder="Masukan Nominal Acc" required/></td>
                                </tr>
                                <tr>
                                    <td>File ACC</td>
                                    <td><input type="file" class="form-control" id="approve_proposal" name="approve_proposal"
                                            placeholder="Upload File Acc" required/></td>
                                </tr>
                                <tr>
                                    <td>Catatan</td>
                                    <td><input type="text" class="form-control" id="catatan" name="catatan"
                                            placeholder="Masukan Catatan" required/></td>
                                </tr>
                        </tbody>
                    </table>
                    <div class="col-md-12">
                        <br>
                        <button type="submit" class="btn btn-primary">Aprove</button>
                        <a href="/proposal" type="button" class="btn btn-danger">Kembali</a>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var rupiah = document.getElementById("nominal_acc");
    rupiah.addEventListener("keyup", function(e) {
        // tambahkan 'Rp.' pada saat form di ketik
        // gunakan fungsi formatRupiah() untuk mengubah angka yang di ketik menjadi format angka
        rupiah.value = formatRupiah(this.value, "Rp. ");
    });

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, "").toString(),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
    }
</script>
@endsection
