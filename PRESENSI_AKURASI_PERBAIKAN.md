# Perbaikan akurasi presensi — 9 September 2026

Keluhan: guru MI PIJENAN, MI KARANG, dan beberapa sekolah lain gagal presensi dengan pesan `Akurasi lokasi belum memenuhi batas minimal presensi.`, meskipun batas sekolah sudah diatur 30 meter.

## Penyebab dan perilaku setelah perbaikan

Kode lama menolak `gps_accuracy < batas_sekolah`. Pembanding tersebut terbalik: nilai GPS adalah ketidakpastian dalam meter, sehingga nilai kecil lebih akurat. Koreksi dasarnya sudah ada pada perubahan lokal ketika pemeriksaan dimulai; perbaikan ini melengkapi pesan, pengambilan lokasi, versi aset, dan tes khusus batas 30 meter.

Dengan batas 30 meter dan titik di dalam polygon sekolah:

| Ketidakpastian GPS | Hasil validasi akurasi |
| --- | --- |
| 0,5 / 5 / 10 / 20 meter | Diterima |
| 29,99 / 30 meter | Diterima |
| 30,1 meter atau lebih | Menunggu sampel lebih akurat di browser; ditolak server jika tetap dikirim |
| Nilai negatif atau tidak valid | Ditolak |

Fitur aktif, jadwal, selfie jika wajib, lokasi di dalam polygon, dan flag lokasi palsu tetap diperiksa. Batas 30 meter bukan radius sekolah. Tidak perlu memperluas polygon atau mengganti pengaturan MI PIJENAN dan MI KARANG hanya untuk mengatasi pembanding yang terbalik ini. Koreksi berlaku untuk semua sekolah berdasarkan pengaturan masing-masing.

Browser mencari lokasi baru sampai 30 detik. Jika belum mendapat sampel yang memenuhi batas, pesan menyebut ketidakpastian GPS terbaik dan batas sekolah. Pengguna dapat mencoba ulang. Versi URL JavaScript mengikuti isi file agar pembaruan aset tidak tertahan cache browser.

## Pemasangan di hosting

Paket `tmp/presensi-akurasi-20260909.zip` berisi empat file aplikasi dan panduan ini. Salin isi paket ke root proyek Laravel aktif, mengikuti struktur folder yang ada:

1. `app/Services/AttendanceValidationService.php` — perbaikan pembanding di server; file utama untuk mengatasi pesan lama.
2. `public/js/attendance-location.js` — pencarian lokasi yang memenuhi batas dan pesan kegagalan.
3. `resources/views/backend/mobile_role2/presensi.blade.php` — memuat JavaScript terbaru dan menangani percobaan ulang.
4. `resources/views/backend/presensi/settings.blade.php` — penjelasan batas maksimum, dengan contoh 30 meter.

Paket ini tidak berisi kredensial, data presensi, atau migrasi. Pengaturan sekolah yang tersimpan, termasuk nilai 30 meter, dipertahankan. Tidak perlu menjalankan migrasi untuk koreksi ini.

Setelah file ditempatkan di aplikasi aktif, jalankan dari root Laravel:

```sh
php artisan view:clear
```

Jika hosting menyajikan folder publik terpisah, tempatkan juga `attendance-location.js` di folder `js` yang dilayani URL aplikasi. Buka ulang halaman presensi. Bila pesan lama masih muncul saat mengirim presensi, periksa bahwa file service diperbarui di proyek yang benar dan muat ulang PHP/OPcache melalui panel hosting jika diperlukan. Pesan tersebut berasal dari server; memuat ulang browser saja tidak memperbarui PHP lama.

## Verifikasi

Tes otomatis menggunakan sekolah, guru, dan koordinat sintetis serta SQLite di memori; tidak mengubah database sekolah:

```sh
php vendor/phpunit/phpunit/phpunit --filter 'AttendanceValidationTest|AttendanceAccuracyTest' --do-not-cache-result
node --test tests/attendance-location.test.cjs
```

Cakupan: penerimaan GPS lebih akurat dan tepat 30 meter; datang dan pulang bagi beberapa guru di dua sekolah; percobaan ulang setelah penolakan; pengaturan sekolah yang berbeda; penolakan titik di luar sekolah dan flag lokasi palsu; izin browser ditolak; waktu tunggu; pembersihan pemantauan GPS.

Hasil lokal: 10 tes PHP lulus (144 assertion), 8 tes JavaScript lulus, dan kedua template Blade berhasil dikompilasi serta lolos pemeriksaan sintaks PHP.

Setelah pemasangan, coba presensi dari guru di masing-masing sekolah. Jika masih gagal, pesan terbaru membedakan lokasi kurang akurat, di luar polygon, izin browser, dan lokasi palsu. Angka terukur pada pesan membantu menentukan langkah berikutnya. Keberhasilan di perangkat dan database produksi belum diverifikasi dari workspace lokal ini.
