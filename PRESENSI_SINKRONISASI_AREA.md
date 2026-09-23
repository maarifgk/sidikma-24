# Pembaruan presensi: kesesuaian polygon HP dan server

Tanggal: 11 September 2026.

Laporan terbaru berasal dari MI KARANG: `tatikmaryati24@gmail.com`, `sitipurwaningsih78@gmail.com`, dan `sudar3958@gmail.com`. Pengguna menyatakan posisi sudah berada di dalam polygon tetapi presensi ditolak. Pembaruan berlaku untuk seluruh guru berdasarkan sekolah pada akun yang sedang login.

## Temuan dan perubahan

Halaman guru sebelumnya memakai polygon yang dimuat saat halaman dibuka, sedangkan pengiriman presensi memakai pengaturan server saat itu. Perubahan batas atau sekolah selama halaman tetap terbuka dapat membuat kedua pemeriksaan memakai area berbeda. Di halaman admin, titik juga dapat terlihat di dalam rancangan batas yang belum disimpan, termasuk rancangan yang dipulihkan setelah validasi formulir gagal.

Perubahan pada paket ini:

1. Sebelum **Periksa GPS** dan pengiriman presensi, browser mengambil polygon serta batas ketidakpastian terbaru milik sekolah akun melalui endpoint yang memerlukan login guru. Permintaan ini tidak memakai cache dan tidak mencatat kehadiran.
2. Pengiriman menyertakan versi pengaturan yang terikat pada akun, sekolah, polygon, dan batas ketidakpastian. Jika berbeda dari versi server, server mengembalikan `location_settings_changed` tanpa mencatat penolakan presensi. Browser memuat pengaturan dan mengambil GPS baru, lalu mencoba kembali satu kali. Perubahan berulang ditampilkan kepada guru.
3. Jika akun login berganti, halaman meminta dimuat ulang sebelum melanjutkan. Pengaturan sekolah dari parameter URL atau polygon kiriman browser tidak menjadi dasar penerimaan.
4. Peta admin menampilkan pemberitahuan ketika polygon yang diperiksa belum disimpan. Perbandingan titik pada rancangan tersebut diberi keterangan agar tidak dianggap sebagai area aktif server.
5. Perbaikan terdahulu tetap disertakan: batas ketidakpastian GPS adalah maksimum, titik pada tepi polygon diterima, pencarian lokasi hingga 30 detik, informasi koordinat/jarak, dan perbaikan pencatatan percobaan ulang.

Titik di dalam polygon tersimpan dan akurasi yang memenuhi batas lolos pemeriksaan lokasi. Persyaratan presensi lain, seperti fitur aktif, selfie jika diwajibkan, dan deteksi lokasi palsu, tetap berlaku. Data polygon, koordinat percobaan terbaru, dan keberhasilan akun produksi tersebut belum dapat diperiksa dari workspace yang belum terhubung ke database hosting. Temuan di atas telah diuji sebagai penyebab perbedaan pemeriksaan, tetapi belum dapat dipastikan sebagai penyebab setiap laporan pengguna.

## Paket yang dipasang

Gunakan `tmp/presensi-sinkronisasi-20260911.zip`. Berkas aplikasi di dalamnya:

- `routes/web.php`
- `app/Http/Controllers/AttendanceController.php`
- `app/Services/AttendanceValidationService.php`
- `app/Models/Attendance.php`
- `app/Console/Commands/DiagnoseAttendanceUser.php`
- `public/js/attendance-location.js`
- `public/js/attendance-geofence.js`
- `resources/views/backend/mobile_role2/presensi.blade.php`
- `resources/views/backend/presensi/settings.blade.php`
- `resources/views/backend/presensi/report.blade.php`
- `resources/views/backend/presensi/location.blade.php`

Berkas berasal dari workspace saat ini, termasuk perubahan sebelumnya pada berkas yang sama. Pasang seluruh paket sebagai satu pembaruan; endpoint baru, controller, service, dan tampilan harus sesuai versinya. Pada hosting yang memakai `public_html`, tempatkan isi `public/js` pada folder `js` yang disajikan domain SIDIKMA. Tidak ada migrasi database baru dalam pembaruan ini.

Jalankan di direktori Laravel **pada hosting**:

```sh
php artisan route:clear
php artisan view:clear
```

Muat ulang proses PHP melalui pengaturan hosting jika OPcache masih memakai kode lama. Muat ulang halaman guru dan admin yang telah terbuka. Clearing cache di workspace lokal tidak mengubah cache hosting.

## Pemeriksaan setelah pemasangan

1. Login sebagai salah satu guru MI KARANG, buka Presensi, lalu tekan **Periksa GPS**. Bagian Lokasi HP Saya harus menampilkan sekolah dari pengaturan server.
2. Jika pengaturan gagal dimuat, pastikan endpoint `/mobile/role-2/presensi/lokasi` tersedia saat login guru, mengembalikan JSON `success: true`, dan `school_name` sesuai sekolah akun. Endpoint ini tidak dapat digunakan tanpa login guru. Periksa pemasangan route/controller/service dan cache hosting jika endpoint tidak tersedia.
3. Di admin MI KARANG, pastikan polygon sesuai batas sekolah, lalu tekan **Simpan Pengaturan** jika ada perubahan. Pemberitahuan perubahan belum disimpan harus hilang setelah halaman hasil penyimpanan dimuat.
4. Guru yang berada di area sekolah mencoba Datang. Perhatikan koordinat hasil percobaan ini dan konfirmasi berhasil dari server. Hasil Periksa GPS sebelumnya tidak menjamin posisi yang dihasilkan HP pada percobaan berikutnya sama.

Jika masih ditolak, jalankan pemeriksaan satu akun pada terminal hosting:

```sh
php artisan presensi:diagnose-user "tatikmaryati24@gmail.com"
php artisan presensi:diagnose-user "sitipurwaningsih78@gmail.com"
php artisan presensi:diagnose-user "sudar3958@gmail.com"
```

Bandingkan sekolah akun, kode penolakan, koordinat terbaru, dan polygon tersimpan. Perintah hanya membaca data dan tidak mengubah akun atau kehadiran. Riwayat penolakan lama tetap menyimpan pesan lama; gunakan percobaan terbaru untuk memeriksa hasil pemasangan.

## Pengujian lokal

Lulus **28 tes PHP (306 assertions)** dan **28 tes JavaScript**. Pengujian mencakup polygon berubah selama pengambilan GPS, pengaturan terbaru tiap sekolah, pergantian akun, percobaan ulang yang terbatas, kegagalan memuat pengaturan, kesesuaian titik tepi/di dalam/di luar, dan rancangan polygon yang belum disimpan setelah validasi formulir gagal. Akun dan polygon pengujian merupakan data sintetis, bukan data produksi MI KARANG.
