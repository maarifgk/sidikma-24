# Penyelesaian pengaturan presensi MI YAPPI PIJENAN

## Temuan dari dua tangkapan layar

1. Halaman masih bertuliskan **Batas Minimal Akurasi GPS** dan **Presensi ditolak jika nilai akurasi GPS di bawah batas ini**. Ini adalah tampilan sebelum koreksi. Tampilan yang terlihat belum menggunakan template baru; file PHP yang aktif di hosting juga perlu diverifikasi, karena tangkapan layar saja tidak menunjukkan versi service yang berjalan.
2. Titik guru pada laporan adalah `-8.018018, 110.4854835`. Tiga titik batas yang terbaca pada pengaturan berbeda sekitar setengah kilometer:

| Titik yang terlihat | Latitude | Longitude | Jarak dari titik guru |
| --- | --- | --- | --- |
| 1 | -8.0191554 | 110.4807661 | 534,6 meter |
| 2 | -8.0190863 | 110.4809593 | 512,1 meter |
| 3 | -8.0193546 | 110.4810961 | 505,4 meter |

Jarak dihitung dengan rumus haversine dan radius bumi 6.371.000 meter. Titik keempat tidak terlihat, sehingga angka di atas adalah jarak ke tiga titik yang terbaca, bukan klaim jarak persis ke seluruh polygon. Tangkapan layar laporan sendiri sudah menunjukkan server menolak posisi sebagai di luar area.

3. Beberapa penanda peta tampil sebagai gambar rusak. Aset Leaflet lokal tidak memiliki gambar retina dan bayangan yang biasanya digunakan penanda bawaan. Paket terbaru menggantinya dengan penanda angka yang tidak membutuhkan gambar tersebut.

## Hasil verifikasi tautan Maps dari pengguna

Tautan [MI Yappi Pijenan di Google Maps](https://maps.app.goo.gl/6bJSrhqYeyWCyRo8A) berhasil dibuka dan mengarah ke tempat **MI Yappi Pijenan**, dengan koordinat pin **`-8.0192659, 110.4810757`**. Koordinat ini diambil dari lokasi tempat (`!3d`/`!4d`) pada URL tujuan, bukan titik tengah tampilan peta (`@...`).

| Perbandingan dengan pin sekolah | Jarak |
| --- | --- |
| Titik polygon 1 | 36,2 meter |
| Titik polygon 2 | 23,7 meter |
| Titik polygon 3 | 10,1 meter |
| Titik presensi guru yang ditolak | **504,8 meter** |

Tiga titik polygon yang terlihat berada di sekitar lokasi sekolah yang diberikan pengguna. Titik presensi yang ditolak justru terpaut sekitar setengah kilometer. **Jika guru benar-benar berada di sekolah saat menekan Datang, lokasi yang dikirim perangkat/browser meleset jauh.** Ini belum membuktikan kerusakan perangkat keras; izin lokasi, sumber lokasi dan kondisi sinyal perlu diperiksa. Polygon tidak perlu dipindahkan ke titik penolakan tersebut.

Pin Maps adalah referensi lokasi tempat, bukan batas lahan. Kesesuaian seluruh bidang polygon dengan ruang guru, gedung dan halaman tetap diperiksa di peta. Titik keempat tidak terlihat pada foto; tidak ada koordinat pengganti yang dikarang atau disimpan otomatis.

## Langkah yang perlu dilakukan

### 1. Pasang perbaikan pada aplikasi hosting yang aktif

Gunakan paket **tmp/presensi-pijenan-20260909.zip**. Paket ini mencakup seluruh koreksi sebelumnya serta perbaikan penanda peta dan pemeriksaan GPS admin. Salin sesuai struktur root Laravel aktif (folder yang berisi `artisan`):

- `app/Services/AttendanceValidationService.php`
- `app/Models/Attendance.php`
- `public/js/attendance-location.js`
- `public/js/attendance-geofence.js`
- `resources/views/backend/mobile_role2/presensi.blade.php`
- `resources/views/backend/presensi/settings.blade.php`
- `resources/views/backend/presensi/report.blade.php`
- `resources/views/backend/presensi/location.blade.php`

Jalankan:

```sh
php artisan view:clear
```

Jika hosting melayani folder publik terpisah, tempatkan kedua JavaScript di folder `js` publik yang benar. Buka ulang pengaturan. Tanda template baru sudah aktif: label **Batas Maksimum Ketidakpastian GPS**, tombol **Cek GPS Saya**, dan penanda batas berupa angka. Jika PHP lama masih berjalan, muat ulang PHP/OPcache melalui panel hosting.

### 2. Periksa GPS perangkat terhadap lokasi sekolah yang telah diketahui

Lakukan dari halaman atau gedung MI YAPPI PIJENAN. Gunakan HP dengan lokasi presisi aktif untuk membuka pengaturan dan tekan **Cek GPS Saya**. Aplikasi mencari lokasi hingga 30 detik, memperlihatkan posisi perangkat dan lingkaran ketidakpastiannya, lalu menampilkan posisi tersebut bersama polygon. Tombol ini tidak mengubah batas maupun menyimpan presensi.

Bandingkan lokasi berikut:

- [Pin sekolah dari tautan pengguna](https://www.google.com/maps?q=-8.0192659,110.4810757)
- [Lokasi titik batas 2 yang terlihat](https://www.google.com/maps?q=-8.0190863,110.4809593)
- [Lokasi presensi guru pada laporan](https://www.google.com/maps?q=-8.018018,110.4854835)

Untuk kasus laporan ini, dahulukan langkah berikut:

1. Saat berada di sekolah, buka Google Maps pada perangkat yang digunakan presensi dan tekan tombol **Lokasi Anda**. Bandingkan titik biru dengan pin sekolah di atas. Membuka pin sekolah saja tidak membuktikan GPS perangkat sudah tepat.
2. Pada Android, aktifkan **Lokasi**, **Tingkatkan Akurasi Lokasi**, serta izin **lokasi presisi** untuk browser. Aktifkan data seluler/Wi-Fi, cari sinyal yang lebih baik di tempat terbuka dalam sekolah, dan nonaktifkan penghemat baterai jika lokasi berpindah-pindah. Ikuti [panduan lokasi Google Maps](https://support.google.com/maps/answer/2839911?co=GENIE.Platform%3DAndroid&hl=id) dan [panduan Akurasi Lokasi Android](https://support.google.com/android/answer/15157297?hl=id).
3. Jika titik biru masih sekitar 500 meter dari sekolah, coba ulang lokasi dari HP lain pada tempat fisik yang sama. Jika hanya perangkat pertama yang meleset, fokuskan pemeriksaan pada perangkat tersebut. Tidak perlu mengganti polygon ke posisi yang meleset.
4. Jika titik biru sudah sesuai sekolah tetapi SIDIKMA masih membaca koordinat lama/jauh, periksa izin lokasi untuk browser/situs SIDIKMA, buka ulang halaman presensi, dan gunakan paket aplikasi yang sudah diperbarui. Bandingkan data presensi terbaru, bukan baris penolakan yang lama.
5. Bila titik presensi terbaru sudah dekat sekolah tetapi tetap di luar area, gunakan **Cek area** untuk memeriksa batas lahan di sekitar posisi tersebut. Batas 30 meter adalah akurasi GPS, bukan perluasan polygon.

### 3. Simpan batas yang benar dan uji satu guru

Jika polygon salah, geser penanda angka atau gambar ulang titik batas secara berurutan mengelilingi lahan yang benar. Pastikan ruang kelas, ruang guru, serta halaman yang memang dipakai untuk presensi berada di dalam bidang biru. Tekan **Simpan Pengaturan**, buka ulang halaman, lalu periksa bahwa batas yang disimpan tetap sesuai.

Angka **30 meter tetap dapat digunakan sebagai batas maksimum ketidakpastian GPS**. GPS 5 atau 10 meter diterima jika titik berada dalam area; GPS tepat pada garis/sudut area juga diterima. Angka tersebut bukan radius tambahan di luar area.

Jam masuk 07.00, jam pulang 13.00, dan toleransi terlambat 30 menit pada gambar tidak menyebabkan penolakan di luar area. Dengan jadwal tersebut, presensi datang pukul 10.18 yang memenuhi syarat lokasi akan berstatus **terlambat**.

Uji satu guru dari posisi yang telah diperiksa. Jika berhasil, lanjutkan guru lain. Jika masih ditolak, buka **Laporan Presensi → Cek area**; laporan terbaru menampilkan ketidakpastian GPS dan penolakan menyebut jarak ke batas terdekat.

## Status pekerjaan

Perbaikan selesai pada kode lokal dan paket siap dipasang. Tautan Maps pengguna sudah diverifikasi; pin sekolah tercatat di atas. Tidak ada akses database/hosting produksi pada sesi ini, sehingga polygon MI PIJENAN belum diubah di server. Hasil pemeriksaan tidak mendukung memindahkan polygon ke titik presensi yang berjarak 504,8 meter. Bentuk seluruh batas lahan dan titik keempat belum terverifikasi. Tidak ada perubahan kode aplikasi tambahan yang diperlukan hanya untuk membaca tautan Maps ini.

Pengujian mencakup perilaku tombol GPS (termasuk izin ditolak), penanda yang dapat digeser, pembandingan lokasi tanpa mengubah polygon, validasi akurasi 30 meter, batas polygon, serta alur datang/pulang dan percobaan ulang.

Hasil: **16 tes PHP lulus (212 assertion) dan 17 tes JavaScript lulus**. Perilaku halaman pengaturan diuji menggunakan skrip Blade yang benar-benar dirender oleh Laravel, dengan simulasi perangkat/peta tanpa database produksi.
