# Desain Fitur Presensi Kehadiran SIDIKMA

## Hak Akses

- Role 2: melakukan presensi datang/pulang dan mengajukan izin melalui `mobile/role-2`.
- Role 3: mengelola pengaturan presensi, dashboard monitoring, laporan, dan approval izin melalui menu `Presensi`.

Kontrol akses saat ini memakai guard di controller:

- `AttendanceController::ensureRoleTwo()`
- `AttendanceAdminController::ensureRoleThree()`

## Database

### `attendance_settings`

Pengaturan presensi per sekolah/madrasah berdasarkan `kelas_id`.

- `kelas_id`: relasi sekolah/madrasah admin.
- `enable_check_in`, `enable_check_out`, `enable_permission`: aktivasi fitur.
- `geofence_polygon`: JSON polygon area sekolah, minimal 3 titik `{lat,lng}`.
- `check_in_time`, `check_out_time`: jam masuk/pulang.
- `late_tolerance_minutes`: toleransi terlambat.
- `max_gps_accuracy`: batas akurasi GPS, default 3 meter.
- `enable_fake_gps_detection`: aktif/nonaktif anti fake GPS.
- `require_selfie`: wajib/opsional foto selfie.

### `attendances`

Log presensi dan audit penolakan presensi.

- `user_id`, `kelas_id`, `attendance_date`.
- `check_type`: `datang` atau `pulang`.
- `status`: `hadir`, `terlambat`, atau `ditolak`.
- `checked_at`: timestamp otomatis server.
- `latitude`, `longitude`, `gps_accuracy`.
- `is_inside_geofence`, `is_mock_location`, `mock_detection_source`.
- `selfie_path`.
- `rejection_code`, `rejection_reason`.
- `device_info`.

### `attendance_permissions`

Pengajuan izin.

- `category`: `terlambat`, `sakit`, `tidak_masuk`, `tugas_dinas`, `cuti`.
- `start_date`, `end_date`, `reason`, `attachment_path`.
- `status`: `pending`, `approved`, `rejected`.
- `reviewer_id`, `reviewed_at`, `review_notes`.

## Flow Presensi

1. Role 3 membuka `Presensi > Pengaturan Presensi`.
2. Role 3 mengaktifkan fitur datang/pulang/izin, mengisi jam, toleransi, batas akurasi, polygon geofence, dan pilihan selfie/fake GPS.
3. Jika fitur aktif, menu `Presensi` dan/atau `Izin` tampil di mobile Role 2.
4. Role 2 menekan tombol `Datang` atau `Pulang`.
5. Browser mengambil GPS dengan `enableHighAccuracy`.
6. Data dikirim ke server: koordinat, akurasi, flag mock location jika tersedia, selfie bila ada.
7. Server memvalidasi:
   - fitur aktif,
   - polygon sudah ada,
   - akurasi GPS `<= max_gps_accuracy`,
   - fake GPS tidak terdeteksi,
   - titik berada di dalam polygon,
   - belum presensi dengan jenis yang sama pada tanggal berjalan.
8. Server menyimpan log presensi. Presensi gagal tetap dicatat sebagai `ditolak` untuk audit.
9. UI menampilkan SweetAlert:
   - `Akurasi lokasi tidak valid, silakan mendekat ke area sekolah`
   - `Terdeteksi penggunaan lokasi palsu (Fake GPS)`
   - `Anda berada di luar area sekolah`
   - `Presensi berhasil`

## Catatan Anti Fake GPS

Web Geolocation standar tidak menyediakan sinyal mock location yang konsisten di semua browser. Implementasi saat ini menerima beberapa flag yang bisa dikirim dari WebView/native Android:

- `is_mock_location`
- `mock_location_detected`
- `mocked`
- `isFromMockProvider`

Untuk deteksi fake GPS yang kuat, aplikasi mobile native/WebView harus mengirim hasil pemeriksaan Android `Location.isFromMockProvider()` atau Play Integrity/App Attest ke endpoint presensi. Server sudah siap menolak otomatis saat flag tersebut bernilai benar.

## Endpoint

### Mobile Role 2

- `GET /mobile/role-2/presensi`
- `POST /mobile/role-2/presensi`
- `GET /mobile/role-2/izin`
- `POST /mobile/role-2/izin`

### Admin Role 3

- `GET /presensi/dashboard`
- `GET /presensi/settings`
- `POST /presensi/settings`
- `GET /presensi/laporan`
- `GET /presensi/laporan/export`
- `GET /presensi/izin`
- `POST /presensi/izin/{id}`

## Dashboard & Laporan

Dashboard Role 3 menampilkan:

- Total hadir hari ini.
- Hadir, terlambat, izin, cuti, tidak hadir.
- Persentase kehadiran.
- Grafik 7 hari.
- Aktivitas presensi terbaru.

Laporan Role 3 mendukung:

- Filter tanggal dari/sampai.
- Filter user.
- Filter status: hadir, terlambat, ditolak, izin, cuti.
- Export Excel menggunakan PhpSpreadsheet.
