# Perbaikan pesan akurasi minimum pada server

11 September 2026. Pesan terbaru yang dikonfirmasi pengguna adalah:

> Akurasi lokasi belum memenuhi batas minimal presensi.

Pesan ini ditemukan pada kode lama `AttendanceValidationService`, dengan kondisi `$accuracy <= 0 || $accuracy < $minimumGpsAccuracy`. Contoh: batas sekolah 30 meter dan GPS 5 meter menghasilkan penolakan. Aturan yang benar memakai batas maksimum ketidakpastian: GPS 5 dan 30 meter diterima; 31 meter ditolak. Koordinat tetap diperiksa terhadap area sekolah.

Nilai akurasi browser menyatakan ketidakpastian posisi dalam meter; angka yang lebih kecil berarti posisi lebih presisi. Rujukan: [GeolocationCoordinates.accuracy](https://developer.mozilla.org/en-US/docs/Web/API/GeolocationCoordinates/accuracy).

Kalimat tersebut tidak ada di kode aplikasi workspace saat ini. Jika pesan itu muncul pada **percobaan baru**, proses aplikasi yang menanganinya masih menjalankan validasi lama atau salinan kode lain. Jika hanya terlihat dalam riwayat, pesan lama dapat tetap tersimpan setelah pembaruan. Menambah atau memindahkan polygon tidak memperbaiki pembanding akurasi yang terbalik.

## Paket kecil

`tmp/presensi-akurasi-server-20260911.zip` berisi:

- `app/Services/AttendanceValidationService.php`: validator yang sudah diperbaiki, kompatibel dengan controller presensi lama dan pembaruan sinkronisasi sebelumnya.
- `tools/verify-presensi-gps.php`: pemeriksaan CLI memakai koordinat sintetis dan batas 30 meter, tanpa koneksi database.
- Panduan ini.

Salin file validator ke **folder Laravel yang benar-benar dipakai domain presensi**, menggantikan `app/Services/AttendanceValidationService.php`. File ini bukan untuk folder `public/js` atau `public_html/js`. Jika ada beberapa salinan proyek, pastikan domain dan document root mengarah ke salinan yang diperbarui. Tidak diperlukan migrasi atau pengubahan batas GPS sekolah.

Setelah mengganti file, **muat ulang PHP pada hosting** agar proses web/OPcache menggunakan kode baru. Gunakan fasilitas pengelolaan PHP pada panel hosting; bila tidak tersedia, minta pengelola hosting memuat ulang proses PHP yang melayani domain. `php artisan view:clear` hanya membersihkan tampilan Blade dan tidak memastikan cache kode PHP proses web berubah.

Jika pemeriksaan perubahan file pada OPcache dinonaktifkan, perubahan kode perlu diaktifkan dengan invalidasi/reset cache atau pemuatan ulang proses yang melayaninya. Rujukan: [konfigurasi OPcache PHP](https://www.php.net/manual/en/opcache.configuration.php#ini.opcache.validate-timestamps).

## Membuktikan kode yang dimuat

Dari direktori Laravel di terminal hosting, jalankan:

```sh
php tools/verify-presensi-gps.php
```

Hasil yang benar:

- `validasi_maksimum_gps_benar: true`
- GPS 5 meter: diterima.
- GPS 30 meter: diterima.
- GPS 31 meter: ditolak dengan pesan batas maksimum.

Periksa `file_validator_cli` untuk mengetahui file yang dimuat oleh perintah. Pemeriksaan ini tidak mengubah data dan tidak membuktikan versi proses web; CLI dan web dapat menggunakan proses/cache atau folder yang berbeda. Jika pemeriksaan CLI benar tetapi percobaan baru di browser masih memakai pesan minimum, fokuskan pemeriksaan pada folder aplikasi domain dan PHP/OPcache yang melayaninya.

Setelah proses web diperbarui, coba presensi dari HP guru di sekolah. Jika masih ditolak, gunakan **teks penolakan baru**: pesan ketidakpastian maksimum, di luar batas sekolah, atau penolakan lain memerlukan penanganan sesuai hasil tersebut. Riwayat lama tidak diubah menjadi hadir.

Workspace belum memiliki akses hosting. Paket dan pemeriksaan lokal sudah disiapkan; pemasangan pada domain dan keberhasilan akun produksi belum dapat diverifikasi langsung.
