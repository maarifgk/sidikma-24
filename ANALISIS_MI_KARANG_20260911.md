# Analisis koordinat presensi MI KARANG — 11 September 2026

Sumber: screenshot pengaturan polygon dan laporan penolakan yang diberikan pengguna. Batas ketidakpastian GPS sekolah dikonfirmasi **50 meter**. Angka ketidakpastian yang dikirim HP untuk masing-masing percobaan belum tersedia.

## Batas yang diketahui

| Titik | Latitude | Longitude |
|---|---:|---:|
| 1 | -8.0450035 | 110.5040693 |
| 2 | -8.0456409 | 110.5039540 |
| 3 | -8.0459065 | 110.5044315 |
| 4 | Belum diberikan | Belum diberikan |

Gambar memperlihatkan polygon empat sudut. Garis Titik 1–Titik 2 merupakan sisi barat pada gambar. Karena koordinat Titik 4 belum tersedia, analisis numerik berikut **hanya terhadap sisi barat**, bukan keputusan point-in-polygon untuk seluruh bidang. Tidak dibuat titik batas pengganti atau polygon segitiga dari data yang belum lengkap.

## Hasil perhitungan

| Nama | Jam masuk yang tampil pada laporan | Latitude | Longitude | Posisi terhadap sisi barat T1–T2 | Jarak ke sisi tersebut |
|---|---|---:|---:|---|---:|
| SUDARMANTO | 08:17:11 | -8.0453019 | 110.5043420 | Sebelah timur | 35,404 m |
| SEPTA PARASTA DHANI | 08:12:35 | -8.0451864 | 110.5039138 | Sebelah barat/luar sisi | 13,267 m |
| SITI PURWANINGSIH | 08:12:25 | -8.0450077 | 110.5040370 | Sebelah barat/luar sisi | 3,418 m |
| TATIK MARYATI | 07:40:23 | -8.0453055 | 110.5043422 | Sebelah timur | 35,496 m |

Status keempat baris dalam screenshot adalah DITOLAK; jam yang ditampilkan bukan bukti kehadiran diterima. Sudarmanto dan Tatik terpisah sekitar **0,401 meter** berdasarkan koordinat laporan. Tampilan bidang biru mendukung dugaan keduanya berada di dalam, tetapi pengujian seluruh polygon tetap membutuhkan Titik 4.

Jarak dihitung ke segmen, bukan ke titik sudut, menggunakan proyeksi lokal dengan radius bumi 6.371.000 meter. Perhitungan JavaScript dicocokkan dengan metode `distanceToPolygon` pada service PHP untuk segmen T1–T2 dan memberikan hasil pembulatan yang sama. Jarak ini bukan angka ketidakpastian GPS perangkat.

## Kaitannya dengan penolakan

1. Untuk Septa dan Siti, posisi terbaca di barat/luar sisi polygon yang terlihat. Jika mereka berdiri di lahan sekolah yang belum tercakup, sisi polygon perlu dicocokkan dengan batas lahan sebenarnya. Jika koordinat bergeser dari tempat mereka berdiri, diperlukan pemeriksaan pembacaan lokasi HP. Screenshot belum membuktikan HP rusak.
2. Untuk Sudarmanto dan Tatik, belum ada dasar dari sisi barat untuk menyebut lokasinya di luar. Pesan sebelumnya yang dikonfirmasi pengguna, **"Akurasi lokasi belum memenuhi batas minimal presensi."**, cocok dengan aturan lama `$accuracy < $minimumGpsAccuracy`. Pada pengaturan 50 meter, aturan lama menolak nilai GPS 5, 30, atau 49 meter. Guru di dalam area tetap dapat ditolak oleh aturan akurasi tersebut.
3. Batas ketidakpastian 50 meter tidak memperluas polygon sejauh 50 meter. Pemeriksaan akurasi dan keanggotaan area adalah dua kondisi yang berbeda.

## Pemeriksaan kode lokal

Validator lokal dengan batas 50 meter menerima nilai GPS **5, 30, 49, dan 50 meter**, serta menolak **51 meter**. Pemeriksaan akurasi ini memakai polygon dan titik sintetis yang jelas di dalam, tanpa membaca akun atau database hosting. Hasil tersebut membuktikan perilaku kode lokal, bukan pemasangan PHP di hosting.

Untuk mengonfirmasi seluruh lokasi dan penyebab setiap baris, diperlukan koordinat Titik 4 serta `gps_accuracy` dan kode/alasan penolakan per percobaan. Jika pesan minimum masih muncul pada percobaan baru, ikuti [panduan validasi server](PRESENSI_AKURASI_SERVER.md) agar file PHP yang benar digunakan oleh domain presensi. Periksa data penolakan terbaru setelah pemasangan, bukan hanya pesan lama dalam riwayat.

Data perhitungan tersedia di `tmp/analisis-mi-karang-20260911.json`. Belum ada perubahan polygon atau akun produksi dari analisis ini.
