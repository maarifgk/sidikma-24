# Pemeriksaan presensi EVA RISMIYATI - 10 September 2026

Akun yang dilaporkan: `epharis88@gmail.com`, MI YAPPI PIJENAN. Pemilik aplikasi sudah mengonfirmasi bahwa paket sebelumnya dipasang di hosting. Workspace ini belum terhubung ke database hosting, sehingga sekolah pada akun, koordinat percobaan terbaru, dan keberhasilan presensi EVA belum dapat diverifikasi langsung.

## Temuan yang sudah ada

Koordinat pada laporan sebelumnya `-8.018018, 110.4854835` berjarak sekitar **504,8 meter dari pin MI Yappi Pijenan** pada [tautan Maps yang diberikan](https://maps.app.goo.gl/6bJSrhqYeyWCyRo8A), yaitu `-8.0192659, 110.4810757`. Ini jarak ke pin, bukan jarak ke batas lahan. Screenshot hanya memperlihatkan tiga dari empat titik polygon; batas lengkap perlu diperiksa pada aplikasi.

Jika EVA berada di sekolah saat koordinat itu dikirim, posisi yang diterima SIDIKMA meleset. Ini belum membuktikan kerusakan HP. Batas ketidakpastian GPS 30 meter berbeda dengan jarak posisi ke sekolah: angka akurasi kecil tetap dapat disertai lokasi yang salah. Mengubahnya menjadi 500 meter tidak mengoreksi koordinat ataupun polygon.

Pesan lama dalam riwayat tetap tersimpan meskipun kode sudah diperbarui. Jika **percobaan baru** masih memunculkan persis "Anda berada di luar area sekolah" tanpa angka jarak, periksa lokasi file hosting, cache tampilan, dan PHP OPcache. Kode validasi pada paket ini menyebut jarak di luar batas.

## Isi perubahan lanjutan

- Halaman presensi guru menampilkan sekolah akun dan tombol **Periksa GPS**. Hasilnya berisi koordinat HP, ketidakpastian, waktu pemeriksaan, dan posisi terhadap polygon sekolah yang tersimpan. Tautan Maps membuka koordinat hasil pemeriksaan tersebut.
- Pemeriksaan GPS tidak mengirim presensi. Saat menekan Datang/Pulang, aplikasi meminta lokasi baru; hasil pemeriksaan sebelumnya tidak dipakai ulang. Koordinat percobaan itu tetap terlihat jika presensi ditolak.
- Perintah `presensi:diagnose-user` membaca satu akun, sekolahnya, pengaturan saat ini, dan maksimal lima baris presensi terbaru. Tidak mengubah sekolah akun, polygon, ataupun status kehadiran; tidak menampilkan password atau token.
- Paket tetap menyertakan perbaikan sebelumnya: aturan maksimum ketidakpastian GPS, pencarian posisi hingga 30 detik, pemeriksaan tepi polygon, dan alat pemeriksaan area pada halaman admin.

## Memasang paket lanjutan

Paket: `tmp/presensi-eva-20260910.zip`. Salin file sesuai struktur proyek Laravel yang melayani domain SIDIKMA. Jika hosting memisahkan folder public menjadi `public_html`, file dalam `public/js` harus masuk folder `js` yang benar-benar disajikan oleh domain.

File aplikasi di dalam paket:

1. `app/Console/Commands/DiagnoseAttendanceUser.php`
2. `app/Services/AttendanceValidationService.php`
3. `app/Models/Attendance.php`
4. `public/js/attendance-location.js`
5. `public/js/attendance-geofence.js`
6. `resources/views/backend/mobile_role2/presensi.blade.php`
7. `resources/views/backend/presensi/settings.blade.php`
8. `resources/views/backend/presensi/report.blade.php`
9. `resources/views/backend/presensi/location.blade.php`

Tidak membutuhkan migrasi database baru. Paket ini mengasumsikan tabel presensi dan kolom datang/pulang aplikasi yang sudah berjalan tersedia.

Jalankan dari folder Laravel di terminal hosting:

```sh
php artisan view:clear
php artisan presensi:diagnose-user "epharis88@gmail.com"
```

Perintah diagnosis ditemukan otomatis oleh `app/Console/Kernel.php` yang sudah memuat folder Commands. Jika hosting memakai Composer dengan classmap authoritative dan perintah baru belum ditemukan, bangun ulang autoload melalui proses deployment hosting. Jika kode PHP lama masih dipakai, muat ulang PHP melalui pengaturan hosting. Menjalankan PHP CLI tidak menjamin cache OPcache proses web ikut dimuat ulang.

Muat ulang halaman presensi pada HP EVA. Pastikan **Lokasi HP Saya / Periksa GPS** muncul. Membuka halaman ini tidak mengubah riwayat penolakan yang sudah ada.

## Langkah pada HP EVA

1. Login dengan `epharis88@gmail.com`. Pada halaman presensi, pastikan nama sekolah tertulis **MI YAPPI PIJENAN**.
2. Saat berada di tempat terbuka dalam sekolah, tekan **Periksa GPS** dan tunggu hingga 30 detik. Catat koordinat, ketidakpastian GPS, waktu, dan jarak yang muncul.
3. Jika posisi terbaca di dalam area, tekan Datang/Pulang. Kehadiran baru tercatat setelah muncul konfirmasi berhasil dari server.
4. Jika posisi tetap di luar padahal sudah di sekolah, aktifkan lokasi presisi untuk aplikasi browser dan fitur akurasi lokasi HP; aktifkan Wi-Fi. Pada Chrome, periksa izin lokasi situs melalui pengaturan di sebelah alamat situs. Buka SIDIKMA langsung di browser, kemudian periksa GPS lagi. Panduan resmi: [izin lokasi Chrome](https://support.google.com/chrome/answer/142065?co=GENIE.Platform%3DAndroid&hl=id) dan [akurasi lokasi Android](https://support.google.com/android/answer/15157297?hl=id).
5. Bandingkan **titik biru posisi HP saat ini**, bukan pin nama sekolah, di Google Maps. Bila titik biru bergeser juga, pemulihan lokasi HP diperlukan. Bila titik biru tepat namun koordinat SIDIKMA meleset, fokuskan pemeriksaan pada izin lokasi browser dan hasil Periksa GPS. [Panduan posisi Google Maps](https://support.google.com/maps/answer/2839911?co=GENIE.Platform%3DAndroid&hl=id).

## Membaca hasil diagnosis akun

- `akun.email` harus sesuai, `akun.role` harus 2, dan `sekolah.nama_kelas` harus MI YAPPI PIJENAN. Jika berbeda, perbaiki hubungan sekolah melalui pengelolaan data guru setelah mencocokkan identitas; jangan menebak ID sekolah atau membuat pengecualian email.
- `pengaturan.polygon` adalah seluruh titik yang dipakai akun itu. Gunakan menu Pengaturan Presensi dan alat Cek Lokasi Presensi untuk membandingkan dengan batas sekolah sebenarnya.
- `aktivitas[].kode_penolakan` membedakan `outside_geofence` dari `invalid_accuracy`. Lokasi datang dan pulang dilaporkan terpisah.
- `di_dalam_polygon_akun_sekarang`, `jarak_di_luar_polygon_akun_sekarang_meter`, dan `akurasi_memenuhi_batas_sekarang` menggunakan **pengaturan sekarang**. Penolakan lama mungkin memakai pengaturan berbeda; percobaan ulang juga dapat menimpa data sebelumnya.
- Sekolah benar + GPS jauh di luar: pulihkan pembacaan lokasi HP/browser. GPS menunjukkan posisi fisik yang benar namun polygon mengecualikan tempat guru berada: admin perlu menyesuaikan polygon dengan batas sekolah sebenarnya. Jangan memindahkan polygon ke koordinat HP yang meleset.

Untuk menentukan koreksi akun atau pengaturan berikutnya, gunakan hasil perintah diagnosis dan hasil Periksa GPS dari percobaan terbaru. Pengujian lokal menggunakan akun dan polygon sintetis, bukan data produksi EVA.

## Validasi lokal

Lulus 20 tes PHP (248 assertions) dan 21 tes JavaScript. Pengujian mencakup batas akurasi 30 meter, penolakan posisi di luar, percobaan ulang, tepi polygon, pemisahan lokasi datang/pulang, pembacaan diagnosis tanpa penulisan database, serta tombol Periksa GPS yang tidak mencatat presensi dan tidak memakai ulang hasilnya untuk pengiriman berikutnya.
