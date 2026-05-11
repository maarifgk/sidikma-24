@extends('backend.layout.base')

@section('content')
@include('backend.administrasi._theme')
<div class="admin-module-page">
<div class="card table-responsive admin-module-panel">
    <div class="card-header admin-module-header mb-0">
        <div>
            <span class="admin-module-kicker"><i class="fa-solid fa-database"></i> Administrasi</span>
            <h5 class="admin-module-title">{{ $title }}</h5>
            <p class="admin-module-subtitle">Detail pembaruan data SIPINTER Ma'arif, dokumen pendukung, dan rekap data satuan pendidikan.</p>
        </div>
        @if(!$isDataExist) 
            <a href="/updatesipinter/add" type="button" class="btn btn-primary admin-module-cta">
                Input Data
            </a>
        @endif
    </div>
    <hr style="border: none; border-top: 1px solid #333; margin-top: 2px; margin-bottom: 2;">
@if (request()->user()->role == 3)
    @if(isset($sipinter) && count($sipinter) > 0)
        @foreach ($sipinter as $s) 
            <div class="admin-module-table-card">
                <p style="font-size: 16px; margin-bottom: 2; margin-top: 1px; font-weight: bold;">STATUS ASET DAN PENGELOLAAN SATUAN PENDIDIKAN</p>
                <table class="data-table" style="margin-bottom: 2; margin-top: 1px;"> 
                    <tr>
                        <td class="label">1. Nama Sekolah/Madrasah</td>
                        <td>: @if ($s->kelas == 1)
                            MI YAPPI Badongan
                        @elseif ($s->kelas == 10)
                            MI YAPPI Baleharjo
                        @elseif ($s->kelas == 11)
                            MI YAPPI Balong
                        @elseif ($s->kelas == 12)
                            MI YAPPI Banjaran
                        @elseif ($s->kelas == 13)
                            MI YAPPI Bansari
                        @elseif ($s->kelas == 14)
                            MI YAPPI Banyusoco
                        @elseif ($s->kelas == 15)
                            MI YAPPI Batusari
                        @elseif ($s->kelas == 16)
                            MI YAPPI Cekel
                        @elseif ($s->kelas == 17)
                            MI YAPPI Doga
                        @elseif ($s->kelas == 18)
                            MI YAPPI Dondong
                        @elseif ($s->kelas == 19)
                            MI YAPPI Gedad I
                        @elseif ($s->kelas == 20)
                            MI YAPPI Gedad II
                        @elseif ($s->kelas == 21)
                            MI YAPPI Gubukrubuh
                        @elseif ($s->kelas == 22)
                            MI YAPPI Kalangan
                        @elseif ($s->kelas == 23)
                            MI YAPPI Kalongan
                        @elseif ($s->kelas == 24)
                            MI YAPPI Karang
                        @elseif ($s->kelas == 26)
                            MI YAPPI Karangtritis
                        @elseif ($s->kelas == 27)
                            MI YAPPI Karangwetan
                        @elseif ($s->kelas == 28)
                            MI YAPPI Kedungwanglu
                        @elseif ($s->kelas == 29)
                            MI YAPPI Klepu
                        @elseif ($s->kelas == 30)
                            MI YAPPI Mulusan
                        @elseif ($s->kelas == 31)
                            MI YAPPI Natah
                        @elseif ($s->kelas == 32)
                            MI YAPPI Ngembes
                        @elseif ($s->kelas == 33)
                            MI YAPPI Nglebeng
                        @elseif ($s->kelas == 34)
                            MI YAPPI Ngleri
                        @elseif ($s->kelas == 35)
                            MI YAPPI Ngrancang
                        @elseif ($s->kelas == 36)
                            MI YAPPI Ngunut
                        @elseif ($s->kelas == 37)
                            MI YAPPI Ngrati
                        @elseif ($s->kelas == 38)
                            MI YAPPI Nologaten
                        @elseif ($s->kelas == 39)
                            MI YAPPI Payak
                        @elseif ($s->kelas == 40)
                            MI YAPPI Peyuyon
                        @elseif ($s->kelas == 41)
                            MI YAPPI Pijenan
                        @elseif ($s->kelas == 42)
                            MI YAPPI Plalar
                        @elseif ($s->kelas == 43)
                            MI YAPPI Pucung
                        @elseif ($s->kelas == 44)
                            MI YAPPI Purwo
                        @elseif ($s->kelas == 45)
                            MI YAPPI Putat
                        @elseif ($s->kelas == 46)
                            MI YAPPI Randukuning
                        @elseif ($s->kelas == 47)
                            MI YAPPI Rejosari
                        @elseif ($s->kelas == 48)
                            MI YAPPI Ringintumpang
                        @elseif ($s->kelas == 49)
                            MI YAPPI Sawahan
                        @elseif ($s->kelas == 50)
                            MI YAPPI Semoyo
                        @elseif ($s->kelas == 51)
                            MI YAPPI Sendang
                        @elseif ($s->kelas == 52)
                            MI YAPPI Tambakromo
                        @elseif ($s->kelas == 53)
                            MI YAPPI Tanjung
                        @elseif ($s->kelas == 54)
                            MI YAPPI Tegalweru
                        @elseif ($s->kelas == 55)
                            MI YAPPI Tekik
                        @elseif ($s->kelas == 57)
                            MI YAPPI Tobong
                        @elseif ($s->kelas == 58)
                            MI YAPPI Wiyoko
                        @elseif ($s->kelas == 60)
                            MI Maarif Mulo 
                        @elseif ($s->kelas == 62)
                            MI Maarif Wareng
                        @elseif ($s->kelas == 63)
                            MI Wasathiyah
                        @elseif ($s->kelas == 65)
                            MTs YAPPI Dengok
                        @elseif ($s->kelas == 66)
                            MTs YAPPI Jetis
                        @elseif ($s->kelas == 67)
                            MTs YAPPI Kenteng
                        @elseif ($s->kelas == 68)
                            MTs YAPPI Mulusan
                        @elseif ($s->kelas == 70)
                            MTs YAPPI Sumberjo
                        @elseif ($s->kelas == 71)
                            MTs Jamul Muawanah
                        @elseif ($s->kelas == 72)
                            SMP Persiapan Semanu
                        @elseif ($s->kelas == 74)
                            SMP Pembangunan I Karangmojo
                        @elseif ($s->kelas == 75)
                            SMP Pembangunan Ponjong
                        @elseif ($s->kelas == 76)
                            SMP Pembangunan Semin
                        @endif</td></td>
                    </tr>
                    <tr>
                        <td class="label">2. NPSN</td>
                        <td>: {{ $s->npsn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">3. Alamat</td>
                        <td>: {{ $s->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">4. Satuan Pendidikan tersebut didirikan di atas tanah</td>
                        <td>: {{ $s->tanah ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">5. Status tanah tempat dibangun Satuan Pendidikan tersebut berupa tanah</td>
                        <td>: {{ $s->status_tanah ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">6. Satuan Pendidikan tersebut berada di bawah pengelolaan</td>
                        <td>: {{ $s->pengelolaakta ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">7. Dalam penyelenggaraannya, Satuan Pendidikan tersebut menggunakan akta notaris</td>
                        <td>: {{ $s->akta ?? '-' }}</td>
                    </tr>
                </table>
                <p></p>
            </div>
            <hr style="border: none; border-top: 1px solid #333; margin-top: 2px; margin-bottom: 2;">
        @endforeach
    @else
        <p style="text-align: center; font-size: 18px; color: red;">Data tidak tersedia, Silahkan Input Data dan Dokumen!</p>
    @endif

    <div class="admin-module-table-card" style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
    
        <!-- Kolom Dokumen Pemutakhiran Data -->
        <div style="width: 65%;">
            <p style="font-size: 16px; margin-bottom: 10px; font-weight: bold;">
                DOKUMEN PEMUTAAKHIRAN DATA SATPEN LPMNU SE-INDONESIA
            </p>
            <table class="data-table" style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                @foreach ($sipinter as $s) 
                <tr>
                    <td class="label">
                        1. Surat Permohonan Bergabung Sebagai Satuan Pendidikan Dibawah Naungan LP Ma’arif NU
                        <br>
                        @if($s->updatesipinter) 
                            <a href="{{ asset('storage/dokumen/updatesipinter/' . $s->updatesipinter) }}" 
                               class="btn btn-primary"
                               target="_blank"
                               style="display: inline-block; font-size: 14px; padding: 5px 10px; margin-top: 5px;">
                                View File
                            </a>
                        @else
                            <span style="color: red; font-size: 14px; display: inline-block; margin-top: 5px; padding: 3px 8px; background-color: #f8d7da; border-radius: 5px;">
                                File belum tersedia
                            </span>
                        @endif
                    </td>
                </tr>
    
                <tr>
                    <td class="label">
                        2. Surat Keterangan Aset dan Pengelolaan Satuan Pendidikan
                        <br>
                        @if($s->pcnu) 
                            <a href="{{ asset('storage/dokumen/pcnu/' . $s->pcnu) }}" 
                               class="btn btn-primary"
                               target="_blank"
                               style="display: inline-block; font-size: 14px; padding: 5px 10px; margin-top: 5px;">
                                View File
                            </a>
                        @else
                            <span style="color: red; font-size: 14px; display: inline-block; margin-top: 5px; padding: 3px 8px; background-color: #f8d7da; border-radius: 5px;">
                                File belum tersedia
                            </span>
                        @endif
                    </td>
                </tr>
    
                <tr>
                    <td class="label">
                        3. Surat Rekomendasi Satuan Pendidikan (PWNU)
                        <br>
                        @if($s->pwnu) 
                            <a href="{{ asset('storage/dokumen/pwnu/' . $s->pwnu) }}" 
                               class="btn btn-primary"
                               target="_blank"
                               style="display: inline-block; font-size: 14px; padding: 5px 10px; margin-top: 5px;">
                                View File
                            </a>
                        @else
                            <span style="color: red; font-size: 14px; display: inline-block; margin-top: 5px; padding: 3px 8px; background-color: #f8d7da; border-radius: 5px;">
                                File belum tersedia
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
            <b>KETERANGAN:</b>
                <P>Jika terdapat kesalahan dalam menginput data atau dokumen, silahkan menghubungi admin LP. Ma'arif NU PCNU Gunungkidul.</P>
        </div>
    
        <!-- Kolom Status Registrasi -->
        <div style="width: 30%; text-align: center; border-left: 2px solid #ddd; padding-left: 20px; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 200px;">
            <p style="font-size: 16px; margin-bottom: 10px; font-weight: bold;">
                STATUS REGISTRASI DI APLIKASI SIPINTER
            </p>        
            <div>
                @foreach ($sipinter as $s) 
                @if(empty($s->pwnu)) 
                    <span style="display: inline-block; background-color: #ff4d4d; color: white; padding: 8px 15px; border-radius: 5px; font-size: 14px; font-weight: bold; margin-bottom: 10px;">
                        DOKUMEN BELUM LENGKAP
                    </span>
                    <p style="font-size: 16px; color: #dc3545; margin: 0;">
                        Segera Input Data untuk Registrasi di Aplikasi SIPINTER!
                    </p>
                @else
                    <span style="display: inline-block; background-color: #28a745; color: white; padding: 8px 15px; border-radius: 5px; font-size: 14px; font-weight: bold; margin-bottom: 10px;">
                        DOKUMEN SUDAH LENGKAP
                    </span>
                    <p style="font-size: 16px; color: #28a745; margin: 0;">
                        Segera Melakukan Registrasi di Aplikasi SIPINTER!
                    </p>
                @endif
                @endforeach
            </div>                     
        </div>
    </div>        
</div>
@endif
@if (in_array(request()->user()->role, [4, 1]))
<div class="admin-module-table-card">
    <table id="datatable" class="table table-striped ">
        <thead>
            <tr>
                <th>No</th>
                <th>Asal Madrasah</th>
                <th>File Permohonan</th>
                <th>File Aset</th>
                <th>File Rekomendasi</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp
            @foreach ($updatesipinter as $t)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td width="auto">@if ($t->kelas == 1)
                        MI YAPPI Badongan
                    @elseif ($t->kelas == 10)
                        MI YAPPI Baleharjo
                    @elseif ($t->kelas == 11)
                        MI YAPPI Balong
                    @elseif ($t->kelas == 12)
                        MI YAPPI Banjaran
                    @elseif ($t->kelas == 13)
                        MI YAPPI Bansari
                    @elseif ($t->kelas == 14)
                        MI YAPPI Banyusoco
                    @elseif ($t->kelas == 15)
                        MI YAPPI Batusari
                    @elseif ($t->kelas == 16)
                        MI YAPPI Cekel
                    @elseif ($t->kelas == 17)
                        MI YAPPI Doga
                    @elseif ($t->kelas == 18)
                        MI YAPPI Dondong
                    @elseif ($t->kelas == 19)
                        MI YAPPI Gedad I
                    @elseif ($t->kelas == 20)
                        MI YAPPI Gedad II
                    @elseif ($t->kelas == 21)
                        MI YAPPI Gubukrubuh
                    @elseif ($t->kelas == 22)
                        MI YAPPI Kalangan
                    @elseif ($t->kelas == 23)
                        MI YAPPI Kalongan
                    @elseif ($t->kelas == 24)
                        MI YAPPI Karang
                    @elseif ($t->kelas == 25)
                        MI YAPPI Karangpilang
                    @elseif ($t->kelas == 26)
                        MI YAPPI Karangtritis
                    @elseif ($t->kelas == 27)
                        MI YAPPI Karangwetan
                    @elseif ($t->kelas == 28)
                        MI YAPPI Kedungwanglu
                    @elseif ($t->kelas == 29)
                        MI YAPPI Klepu
                    @elseif ($t->kelas == 30)
                        MI YAPPI Mulusan
                    @elseif ($t->kelas == 31)
                        MI YAPPI Natah
                    @elseif ($t->kelas == 32)
                        MI YAPPI Ngembes
                    @elseif ($t->kelas == 33)
                        MI YAPPI Nglebeng
                    @elseif ($t->kelas == 34)
                        MI YAPPI Ngleri
                    @elseif ($t->kelas == 35)
                        MI YAPPI Ngrancang
                    @elseif ($t->kelas == 36)
                        MI YAPPI Ngunut
                    @elseif ($t->kelas == 37)
                        MI YAPPI Ngrati
                    @elseif ($t->kelas == 38)
                        MI YAPPI Nologaten
                    @elseif ($t->kelas == 39)
                        MI YAPPI Payak
                    @elseif ($t->kelas == 40)
                        MI YAPPI Peyuyon
                    @elseif ($t->kelas == 41)
                        MI YAPPI Pijenan
                    @elseif ($t->kelas == 42)
                        MI YAPPI Plalar
                    @elseif ($t->kelas == 43)
                        MI YAPPI Pucung
                    @elseif ($t->kelas == 44)
                        MI YAPPI Purwo
                    @elseif ($t->kelas == 45)
                        MI YAPPI Putat
                    @elseif ($t->kelas == 46)
                        MI YAPPI Randukuning
                    @elseif ($t->kelas == 47)
                        MI YAPPI Rejosari
                    @elseif ($t->kelas == 48)
                        MI YAPPI Ringintumpang
                    @elseif ($t->kelas == 49)
                        MI YAPPI Sawahan
                    @elseif ($t->kelas == 50)
                        MI YAPPI Semoyo
                    @elseif ($t->kelas == 51)
                        MI YAPPI Sendang
                    @elseif ($t->kelas == 52)
                        MI YAPPI Tambakromo
                    @elseif ($t->kelas == 53)
                        MI YAPPI Tanjung
                    @elseif ($t->kelas == 54)
                        MI YAPPI Tegalweru
                    @elseif ($t->kelas == 55)
                        MI YAPPI Tekik
                    @elseif ($t->kelas == 57)
                        MI YAPPI Tobong
                    @elseif ($t->kelas == 58)
                        MI YAPPI Wiyoko
                    @elseif ($t->kelas == 60)
                        MI Maarif Mulo 
                    @elseif ($t->kelas == 62)
                        MI Maarif Wareng
                    @elseif ($t->kelas == 63)
                        MI Wasathiyah
                    @elseif ($t->kelas == 65)
                        MTs YAPPI Dengok
                    @elseif ($t->kelas == 66)
                        MTs YAPPI Jetis
                    @elseif ($t->kelas == 67)
                        MTs YAPPI Kenteng
                    @elseif ($t->kelas == 68)
                        MTs YAPPI Mulusan
                    @elseif ($t->kelas == 70)
                        MTs YAPPI Sumberjo
                    @elseif ($t->kelas == 71)
                        MTs Jamul Muawanah
                    @elseif ($t->kelas == 72)
                        SMP Persiapan Semanu
                    @elseif ($t->kelas == 74)
                        SMP Pembangunan I Karangmojo
                    @elseif ($t->kelas == 75)
                        SMP Pembangunan Ponjong
                    @elseif ($t->kelas == 76)
                        SMP Pembangunan Semin
                    @endif</td>
                    <td>
                        <a href="{{ asset('') }}storage/dokumen/updatesipinter/{{ $t->updatesipinter }}" class="btn btn-primary" view="">View</a>
                    </td>
                    <td>
                        @if ($t->pcnu)
                        <a href="{{ asset('') }}storage/dokumen/pcnu/{{ $t->pcnu}}" class="btn btn-primary" view="">View</a>
                        @endif
                    </td>
                    <td>
                        @if ($t->pwnu)
                        <a href="{{ asset('') }}storage/dokumen/pwnu/{{ $t->pwnu}}" class="btn btn-primary" view="">View</a>
                        @endif
                    </td>
                    <td>
                        <a href="/updatesipinter/edit/{{ $t->id }}" type="button" class="btn btn-success">Up</a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#delete{{ $t->id }}">Del</button>
                    </td>
                    <div class="modal fade" id="delete{{ $t->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="deletemodal" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addNewDonaturLabel">Hapus Data Usulan
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Anda yakin ingin data sipinter {{ $t->kelas }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <a href="{{ url('/updatesipinter/delete', $t->id) }} "
                                            class="btn btn-primary">Hapus</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
</div>
@endsection
