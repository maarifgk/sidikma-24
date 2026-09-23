# Pemeriksaan presensi ditolak di MI YAPPI PIJENAN

Tangkapan layar 9 September 2026 menunjukkan titik `-8.018018, 110.4854835` dengan alasan `Anda berada di luar area sekolah`. Ini adalah penolakan polygon, berbeda dari pembanding akurasi yang diperbaiki sebelumnya. Angka ketidakpastian GPS dan polygon yang aktif saat kejadian tidak terlihat pada tangkapan layar, sehingga belum dapat dipastikan apakah posisi perangkat meleset atau batas sekolah salah/terlalu sempit.

## Cara menentukan penyebab

Setelah memasang paket ini, buka **Presensi → Laporan → baris penolakan → Cek area** pada kolom Lokasi Masuk. Halaman pengaturan sekolah menampilkan titik percobaan, lingkaran ketidakpastian GPS jika tersedia, dan polygon sekolah. Hasil membandingkan titik dengan polygon yang sedang ditampilkan, bukan salinan polygon pada tanggal kejadian.

| Hasil pemeriksaan | Langkah berikutnya |
| --- | --- |
| Titik sesuai posisi guru yang berada di sekolah, tetapi di luar bidang polygon | Perbaiki titik batas secara berurutan mengelilingi lahan sekolah yang sebenarnya, kemudian Simpan Pengaturan. |
| Titik GPS meleset dari tempat guru berdiri | Aktifkan lokasi presisi untuk browser dan layanan akurasi lokasi. Coba di tempat terbuka dalam sekolah, lalu periksa kembali posisi. |
| Titik tepat pada garis atau sudut polygon | Koreksi batas pada paket ini memperlakukan titik tersebut sebagai di dalam area. |
| Titik berada di dalam polygon sekarang tetapi catatan lama ditolak | Periksa apakah polygon telah berubah atau hosting masih menjalankan kode lama. Catatan lama tidak otomatis berubah menjadi hadir. |
| Banyak guru di lokasi fisik yang sama mengalami penolakan serupa | Dahulukan pemeriksaan polygon dan kesesuaian sekolah pada akun; belum cukup bukti untuk menyatakan semua HP rusak. |

Bandingkan juga posisi di Google Maps pada HP yang digunakan. Jika titik Maps meleset atau lingkarannya lebar, lakukan [langkah perbaikan lokasi dari Google](https://support.google.com/maps/answer/2839911?co=GENIE.Platform%3DAndroid&hl=en-uk). Aktifkan **Setelan → Lokasi → Layanan Lokasi → Akurasi Lokasi → Tingkatkan Akurasi Lokasi**, serta izin lokasi presisi browser, mengikuti [panduan Android](https://support.google.com/android/answer/15157297?hl=en). Nama menu dapat berbeda menurut perangkat.

Pengaturan akurasi 30 meter adalah maksimum ketidakpastian GPS, **bukan radius izin presensi**. Tombol **Lokasi Saya** pada pengaturan hanya memusatkan peta; tombol itu tidak membuat polygon. Tetap tentukan minimal tiga titik mengelilingi area sekolah, lalu simpan. Jangan memindahkan batas ke titik penolakan tanpa memastikan batas lahan yang benar.

## Perubahan aplikasi

- Titik tepat pada garis/sudut polygon diterima secara konsisten, dengan toleransi pembulatan koordinat 1 cm. Tidak ada tambahan radius 30 meter di luar area.
- Polygon yang tidak membentuk bidang atau memiliki koordinat tidak valid ditolak.
- Browser menunggu lokasi membaik hingga 30 detik saat sampel awal berada di luar area. Bila tetap di luar, titik terbaik yang memenuhi batas akurasi dikirim ke server untuk penolakan dan pencatatan.
- Penolakan baru menyebut perkiraan jarak ke batas terdekat dan ketidakpastian GPS. Jarak menggunakan pendekatan bidang lokal untuk area seukuran sekolah.
- Kolom lokasi laporan memperlihatkan ketidakpastian GPS serta tombol **Cek area**.
- Waktu percobaan yang ditolak tidak lagi ditampilkan sebagai Jam Masuk/Jam Pulang yang berhasil. Waktu percobaan tetap tersimpan.

## Pemasangan

Paket **tmp/presensi-area-20260909.zip** sudah mencakup koreksi akurasi sebelumnya. Tempatkan file sesuai struktur root Laravel aktif:

1. `app/Services/AttendanceValidationService.php`
2. `app/Models/Attendance.php`
3. `public/js/attendance-location.js`
4. `public/js/attendance-geofence.js`
5. `resources/views/backend/mobile_role2/presensi.blade.php`
6. `resources/views/backend/presensi/settings.blade.php`
7. `resources/views/backend/presensi/report.blade.php`
8. `resources/views/backend/presensi/location.blade.php`

Jalankan `php artisan view:clear` di root Laravel, lalu buka ulang halaman. Jika folder publik hosting terpisah, tempatkan kedua file JavaScript juga di folder `js` yang melayani URL aplikasi. Muat ulang PHP melalui panel hosting jika OPcache masih menyajikan versi lama. Tidak diperlukan migrasi database.

Pengaturan polygon produksi MI PIJENAN belum tersedia di workspace, sehingga paket tidak mengubah koordinat sekolah atau mengubah penolakan lama menjadi hadir. Batas yang benar harus diperiksa melalui langkah di atas sebelum diubah.

## Pengujian lokal

16 tes PHP lulus (212 assertion) dan 13 tes JavaScript lulus. Tes mencakup sisi/sudut polygon, titik di luar beberapa meter maupun sentimeter, polygon cekung/tidak valid, pemulihan GPS, penolakan setelah waktu tunggu, pengaturan 30 meter, percobaan ulang, serta laporan lokasi. Batas sekolah dalam tes adalah data sintetis, bukan polygon MI PIJENAN yang sebenarnya.

```sh
php vendor/phpunit/phpunit/phpunit --filter 'AttendanceValidationTest|AttendanceAccuracyTest|AttendanceGeofenceTest' --do-not-cache-result
node --test tests/attendance-location.test.cjs tests/attendance-geofence.test.cjs
```
