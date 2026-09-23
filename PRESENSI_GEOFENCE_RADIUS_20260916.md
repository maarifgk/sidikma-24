# Perbaikan geofence presensi — 16 September 2026

## A. Temuan audit dan batas kesimpulan

Kode awal memakai **polygon**, bukan titik pusat dan radius. Tabel `attendance_settings` awal belum memiliki kolom radius/pusat. Angka `max_gps_accuracy` adalah maksimum ketidakpastian GPS; memperbesar angka itu tidak memperbesar polygon. Lingkaran pada pemeriksaan lokasi admin menunjukkan ketidakpastian GPS perangkat, bukan radius penerimaan sekolah.

Sudah tersedia pembacaan pengaturan berdasarkan `users.kelas_id`, refresh konteks tanpa cache sebelum pengambilan GPS, versi konfigurasi, validasi server, serta `watchPosition` hingga 30 detik. Tidak ditemukan syarat harus sama persis dengan satu titik, konversi kilometer yang keliru dalam jalur polygon, radius penerimaan hardcoded, Livewire/Filament, atau cache Redis/session untuk pengaturan presensi. Koordinat presensi sudah memakai `decimal(10,7)` dan tidak perlu diubah.

Empat titik yang diberikan pengguna untuk MI Karangpilang adalah polygon valid:

| Titik | Latitude | Longitude |
| --- | --- | --- |
| 1 | -7.8260874 | 110.7824557 |
| 2 | -7.8254603 | 110.7827508 |
| 3 | -7.8252105 | 110.7820051 |
| 4 | -7.8258722 | 110.7817341 |

Seluruh 13 posisi uji di sudut, tepi, pusat, dan bagian dalam polygon tersebut diterima oleh jalur presensi lokal. Ini membuktikan bahwa polygon itu tidak hanya menerima satu ruangan dalam simulasi koordinat. **Penyebab penolakan perangkat nyata belum dapat dipastikan**: database MySQL proyek tidak dapat terhubung, ID sekolah/nilai akurasi aktif/koordinat percobaan terbaru belum tersedia, dan versi kode hosting belum diperiksa. Belum dapat dipastikan bahwa empat titik tersebut sama dengan konfigurasi yang sedang dipakai server.

## B. File aplikasi yang diubah/ditambahkan dalam pekerjaan ini

| File | Perubahan |
| --- | --- |
| `app/Services/AttendanceValidationService.php` | Definisi area aktif per sekolah, Haversine, pemeriksaan koordinat/akurasi terpisah, validasi radius/polygon, perluasan hash versi. |
| `app/Models/AttendanceSetting.php` | Fillable dan casts kolom mode, pusat, radius. |
| `app/Http/Controllers/AttendanceAdminController.php` | Validasi dan penyimpanan radius; data area dashboard; menolak admin sekolah yang tidak memiliki sekolah valid. |
| `app/Http/Controllers/AttendanceController.php` | Validasi sekolah akun dan logging lokasi pada lingkungan local/testing. |
| `public/js/attendance-geofence.js` | Haversine dan pemeriksaan area radius/polygon yang setara dengan PHP. |
| `public/js/attendance-location.js` | Pemilihan sampel GPS sesuai mode area aktif. |
| `resources/views/backend/presensi/settings.blade.php` | Pilihan mode, input pusat/radius, lingkaran sesuai meter, marker pusat yang dapat digeser, diagnosis dan status perubahan belum disimpan. |
| `resources/views/backend/presensi/dashboard.blade.php` | Menampilkan lingkaran sekolah sesuai radius tersimpan ketika mode radius aktif. |
| `resources/views/backend/mobile_role2/presensi.blade.php` | Membaca mode/radius terbaru, menampilkan jarak/radius/status, tombol Perbarui Lokasi. |
| `app/Console/Commands/DiagnoseAttendanceUser.php` | Menambahkan pemeriksaan area aktif untuk diagnosis pengguna tanpa mengubah data. |
| `app/Console/Commands/DiagnoseAttendanceSchool.php` | Perintah diagnosis konfigurasi sekolah dan perbandingan beberapa sekolah tanpa menulis data. |
| `database/migrations/2026_09_16_000000_add_radius_geofence_to_attendance_settings.php` | Migrasi tambahan untuk mode radius. |

Pengujian ditambahkan pada `tests/Feature/AttendanceRadiusTest.php`, fixture `tests/fixtures/attendance-karangpilang.json`, serta diperluas pada tes JavaScript geofence/lokasi/mobile/settings/syntax dan helper render Blade. Banyak file workspace sudah berubah sebelum pekerjaan ini; perubahan lain pada `git diff` bukan bagian dari pekerjaan geofence ini.

## C. Logika sebelum dan sesudah

Sebelumnya: polygon tersimpan → akurasi memenuhi batas → titik berada di polygon → aturan fake GPS/jadwal/selfie/duplikasi tetap berlaku.

Sekarang: ambil sekolah akun → ambil **mode area tersimpan** → periksa akurasi → periksa polygon atau jarak terhadap radius → terapkan persyaratan presensi lainnya. Datang dan pulang memakai service yang sama. Mode radius tidak mengharuskan titik berada di polygon lama sekaligus; mode polygon tetap memakai aturan sebelumnya.

Saat admin menyimpan mode radius, latitude, longitude dan radius disimpan pada baris sekolah tersebut. Polygon lama tidak dihapus. Mengganti ke mode polygon juga mempertahankan nilai radius yang sudah disimpan. Tidak ada radius universal atau cabang berdasarkan nama sekolah.

Konteks lokasi baru mencakup mode/pusat/radius. Hash versi berubah ketika area berubah; setiap Perbarui Lokasi dan pengiriman presensi mengambil konteks terbaru tanpa cache. Konflik versi saat GPS sedang diambil menyebabkan refresh dan satu kali percobaan ulang otomatis, tanpa membuat penolakan palsu. Pengubahan radius tidak membutuhkan pembersihan cache Laravel setiap kali.

## D. Migrasi dan pemasangan

Migrasi baru menambah:

- `geofence_mode`: default `polygon` agar sekolah lama tetap memakai polygon.
- `center_latitude`, `center_longitude`: nullable `decimal(10,7)`.
- `radius_meters`: nullable `decimal(10,2)`.

Migrasi tidak mengubah nilai polygon, batas akurasi, akun, atau presensi lama. Radius baru tidak ditebak dari polygon. Migrasi diuji dengan SQLite di memori, termasuk pemeriksaan data lama sebelum/sesudah. **Migrasi belum diterapkan pada database proyek/hosting karena koneksi tidak tersedia.**

Pada deployment, pasang file aplikasi beserta migrasi ini dan jalankan dari root Laravel yang melayani domain:

```sh
php artisan migrate --path=database/migrations/2026_09_16_000000_add_radius_geofence_to_attendance_settings.php --force
php artisan view:clear
```

Perintah `--path` hanya menjalankan migrasi tambahan ini. Tabel presensi dan migrasi kolom datang/pulang dari versi sebelumnya harus sudah tersedia. Pasang JavaScript ke folder publik yang benar, lalu muat ulang halaman guru/admin. Pembersihan view ini hanya untuk pemasangan kode baru, bukan setiap pengubahan radius. Jika proses web masih memakai kode lama karena OPcache, muat ulang proses PHP melalui hosting.

## E. Perhitungan jarak dan akurasi

Mode radius menggunakan Haversine dengan jari-jari bumi 6.371.000 meter. Latitude/longitude dikonversi ke radian; hasil jarak dalam meter dibandingkan dengan `radius_meters` sekolah. Batas yang sama dipakai Leaflet `L.circle(..., {radius: ...})`, JavaScript, dan Laravel.

Radius 100 menerima 10, 50, 99, dan tepat 100 meter; **101 meter ditolak**. Epsilon `0.0000001` meter hanya mengatasi pembulatan floating point, bukan toleransi GPS. Ketidakpastian GPS tidak ditambahkan ke radius. Mode polygon tetap menerima sisi/sudut dengan toleransi pembulatan 1 cm yang sudah ada sebelumnya.

Akurasi diperiksa terpisah: harus finite, tidak negatif, dan tidak melebihi batas sekolah. Batas 30 menerima GPS 5/10/30; GPS 31 ditolak sebagai `invalid_accuracy`, bukan `outside_geofence`. Nilai batas sekolah lama tetap dipertahankan. Radius yang dapat disimpan melalui admin adalah 1–5000 meter; ini batas validasi input, bukan nilai radius bawaan.

Browser memakai `watchPosition`, `enableHighAccuracy: true`, `maximumAge: 0`, timeout per permintaan 15 detik dan batas keseluruhan 30 detik. Sampel kurang akurat ditunggu; sampel di luar area diberi kesempatan membaik. Sampel pertama yang sudah cukup akurat dan di dalam area dapat segera digunakan. Browser biasa tidak dapat menjamin GPS indoor stabil atau membuktikan bahwa posisi perangkat tidak dimanipulasi; deteksi flag fake GPS yang sudah ada tetap dipertahankan.

## F. Cara menguji MI Karangpilang

1. Pastikan sekolah pada akun guru benar. Baca konfigurasi di lingkungan yang terhubung database:

   ```sh
   php artisan presensi:diagnose-school "MI Karangpilang"
   ```

   Jika nama database berbeda, gunakan bagian nama atau ID hasil pencarian. Periksa ID sekolah, ID setting, mode aktif, koordinat/radius atau polygon, batas akurasi, serta jumlah guru/admin yang terhubung.

2. Untuk memakai empat titik yang diberikan, pilih **Polygon / Batas Lahan**, cocokkan urutan titik dan simpan. Untuk beralih ke radius, pilih **Radius dari Titik Sekolah**, tentukan pusat sebenarnya dan radius dalam meter melalui admin, lalu simpan. Empat titik batas bukan instruksi untuk otomatis mengubahnya menjadi satu titik pusat/radius.
3. Pada HP guru, tekan **Perbarui Lokasi**. Periksa sekolah, koordinat dan akurasi. Pada mode radius juga tampil jarak/radius serta status area. Coba datang dari beberapa sisi bangunan yang masih termasuk area.
4. Coba pulang; aturan jam/alasan pulang awal tetap berlaku. Coba dari luar radius untuk memastikan server menolak.
5. Jika masih ditolak, gunakan `php artisan presensi:diagnose-user "email-akun"` untuk membaca koordinat dan kode penolakan terbaru. Perbandingan memakai area aktif sekarang, bukan snapshot area saat catatan lama dibuat. Cocokkan data ini dengan posisi fisik dan versi aplikasi di hosting.

Hasil lokal MI Karangpilang: **13 posisi × datang/pulang diterima** dengan akurasi sintetis 8 meter pada polygon pengguna. Belum merupakan uji HP atau keberhasilan akun produksi.

## G. Validasi sekolah lain

Pengujian membuktikan sekolah A dengan radius 50 menolak jarak 100, sedangkan sekolah B dengan radius 200 menerima jarak 100. Admin sekolah A tidak dapat mengganti pengaturan sekolah B dengan memalsukan parameter `kelas_id`. Parameter radius/pusat dari kiriman guru juga tidak menggantikan konfigurasi server.

Untuk membandingkan konfigurasi nyata tanpa mengubah data:

```sh
php artisan presensi:diagnose-school ID_SEKOLAH_A --compare=ID_SEKOLAH_B
```

Ganti placeholder dengan ID hasil diagnosis. Ulangi presensi datang/pulang pada akun masing-masing sekolah setelah deployment. Sekolah yang tetap memakai polygon tidak perlu dialihkan ke radius.

## H. Hasil pengujian

Lulus **40 tes PHP / 456 assertions** dan **41 tes JavaScript**:

```sh
php vendor/phpunit/phpunit/phpunit --filter 'AttendanceRadiusTest|AttendanceValidationTest|AttendanceAccuracyTest|AttendanceGeofenceTest|AttendanceDiagnosisTest' --do-not-cache-result
node --test tests/attendance-syntax.test.cjs tests/attendance-settings.test.cjs tests/attendance-mobile.test.cjs tests/attendance-location.test.cjs tests/attendance-geofence.test.cjs
```

Cakupan: radius 10/50/99/100/101 meter, beberapa arah, update 50→150, konflik versi saat GPS diambil, pemisahan sekolah, penolakan radius palsu dari browser, akurasi, koordinat kosong, fake GPS, selfie wajib, keterlambatan, pulang awal, polygon lama, polygon pengguna, migrasi tanpa perubahan presensi lama, diagnosis tanpa menulis, editor circle/marker, serta sintaks JavaScript sebelum/sesudah render Blade.

PHP memakai database SQLite sintetis; JavaScript memakai simulasi DOM/GPS/Leaflet. Halaman dashboard/settings/mobile diuji render untuk mode radius dan polygon. Tidak ada klaim bahwa console perangkat nyata atau log hosting sudah diuji.

## I. Risiko dan status

- Koneksi MySQL proyek belum tersedia; konfigurasi dan penolakan terbaru MI Karangpilang belum dapat dibandingkan langsung dengan sekolah lain. Penyebab insiden nyata masih perlu data tersebut.
- Mode radius sengaja memakai batas tegas. GPS yang meleset ke luar area tetap dapat ditolak meskipun orangnya berada di dalam; pesan membedakan jarak dan akurasi. Perbarui Lokasi mengambil sampel baru.
- Pengaturan pusat/radius harus mencerminkan lahan yang diizinkan admin. Circle dapat mencakup lahan di luar bentuk polygon; pergantian mode harus disengaja.
- Logging koordinat hanya aktif untuk lingkungan `local`/`testing`, bukan halaman produksi. Data log dibatasi pada identitas numerik, koordinat, area, akurasi dan hasil validasi.
- `down()` migrasi hanya menghapus kolom baru; rollback akan menghilangkan konfigurasi radius baru dan kembali menggunakan polygon lama. Jangan menjalankan rollback untuk menguji production.

**Status implementasi dan tes lokal: selesai. Status pemasangan, akar penolakan perangkat nyata, dan verifikasi akun MI Karangpilang di hosting: belum terverifikasi.**
