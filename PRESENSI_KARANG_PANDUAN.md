# Tindak lanjut presensi MI KARANG — 10 September 2026

Akun yang dilaporkan:

- `kaspankaspan447@gmail.com`
- `ariefkurniawanindy@gmail.com`
- `parastadhani8@gmail.com` — dilaporkan gagal presensi masuk dengan penolakan lokasi yang sama.

Pengguna mengonfirmasi ketiga guru berada di MI KARANG. Hubungan sekolah pada akun di database hosting belum dapat diverifikasi karena workspace belum memiliki koneksi database hosting. Koordinat, ketidakpastian GPS, dan polygon MI KARANG belum diberikan. Koordinat laporan EVA/MI PIJENAN tidak digunakan untuk menyimpulkan penyebab di MI KARANG. Perbaikan kode berlaku untuk semua akun; penambahan email ini merupakan pencatatan kasus, bukan pengecualian validasi lokasi atau perubahan data akun.

## Perbaikan yang sudah diuji

Ditemukan dan direproduksi kesalahan berikut: presensi datang ditolak karena lokasi di luar area, kemudian presensi pulang berhasil, tetapi waktu datang yang ditolak ikut terbaca sebagai sudah tercatat. Akibatnya percobaan datang berikutnya terhalang oleh pesan sudah tercatat. Pembacaan waktu sekarang memisahkan percobaan yang ditolak dari waktu yang berhasil disimpan. Catatan lama sebelum format datang/pulang tetap didukung.

Kesalahan tersebut belum terbukti sebagai penyebab penolakan lokasi pada ketiga akun MI KARANG. Perbaikan ini mengatasi pencatatan percobaan ulang; penentuan sebab lokasi tetap membutuhkan data dari HP dan pengaturan sekolah.

Lulus **23 tes PHP, 274 assertions**, termasuk penolakan datang yang diikuti pulang berhasil, mencoba datang kembali, pulang ditolak lalu berhasil, batas ketidakpastian 30 meter, tepi polygon, dan diagnosis akun tanpa penulisan data.

## Paket dan pemasangan

Paket `tmp/presensi-karang-20260910.zip` menyertakan perbaikan sebelumnya dan perubahan pembacaan waktu pada `app/Models/Attendance.php`. Tidak memerlukan migrasi baru. Tabel presensi dan kolom datang/pulang aplikasi yang sudah berjalan harus tersedia.

Salin file mengikuti struktur proyek Laravel yang dipakai domain SIDIKMA. Pada hosting yang memisahkan `public` menjadi `public_html`, pastikan file `public/js` masuk folder `js` yang disajikan domain. Jalankan dari folder Laravel **di hosting**:

```sh
php artisan view:clear
php artisan presensi:diagnose-user "kaspankaspan447@gmail.com"
php artisan presensi:diagnose-user "ariefkurniawanindy@gmail.com"
php artisan presensi:diagnose-user "parastadhani8@gmail.com"
```

Perintah diagnosis hanya membaca akun yang diminta, sekolahnya, pengaturan, dan maksimal lima baris presensi terbaru. Menjalankannya di workspace ini belum dapat membaca data hosting. Jangan membuat akun atau database lokal pengganti untuk dianggap sebagai hasil pemeriksaan produksi.

Jika perintah belum ditemukan, pastikan `app/Console/Commands/DiagnoseAttendanceUser.php` ikut dipasang. Jika deployment menggunakan Composer classmap authoritative, bangun ulang autoload sesuai proses deployment hosting. Muat ulang PHP melalui pengaturan hosting jika OPcache masih menjalankan kode lama.

## Memeriksa dari HP guru

1. Login dengan akun masing-masing, muat ulang halaman Presensi, lalu pastikan sekolah yang ditampilkan adalah MI KARANG.
2. Saat berada di MI KARANG, tekan **Periksa GPS**. Pemeriksaan ini tidak mencatat kehadiran.
3. Catat koordinat HP, ketidakpastian GPS, waktu pemeriksaan, dan jarak di luar batas. Jika tombol belum ada, halaman hosting belum menampilkan paket yang menyertakan pemeriksaan GPS.
4. Jika berada di dalam area, tekan Datang/Pulang; aplikasi mengambil posisi baru dan tetap memvalidasinya di server. Pastikan muncul konfirmasi berhasil.
5. Jika masih di luar area, kirim hasil pemeriksaan dan screenshot seluruh polygon MI KARANG kepada pengelola aplikasi. Periksa apakah koordinat itu sesuai posisi fisik guru di sekolah sebelum mengubah polygon.

Hasil perintah hosting `sekolah.nama_kelas` harus dicocokkan dengan sekolah yang dikonfirmasi pengguna. Bila berbeda, periksa hubungan sekolah pada akun melalui pengelolaan guru. Jika sesuai, gunakan lokasi terbaru dan polygon lengkap untuk membedakan masalah pembacaan lokasi HP dari batas sekolah yang salah.

Kode `outside_geofence` berarti koordinat berada di luar polygon. Kode `invalid_accuracy` berarti ketidakpastian lokasi melampaui batas. Mengubah batas ketidakpastian GPS tidak memindahkan koordinat HP atau memperbaiki polygon. Pesan lama pada riwayat dapat tetap tersimpan setelah pembaruan kode.

Keberhasilan presensi ketiga akun di hosting belum diverifikasi. Tidak ada perubahan sekolah, polygon, atau status kehadiran produksi yang dilakukan dari workspace ini.
