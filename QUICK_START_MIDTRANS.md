# 🚀 QUICK START GUIDE - MIDTRANS FIX

## 📝 Ringkasan Perbaikan

Perbaikan telah dibuat untuk mengatasi **5 masalah utama** dalam integrasi Midtrans:

| # | Masalah | Solusi | Status |
|---|---------|--------|--------|
| 1 | Model Payment tidak ada | Buat `app/Models/Payment.php` | ✅ Done |
| 2 | Order ID random & tidak unik | Gunakan `ORDER-{uid}-{tag}-{time}` | ✅ Done |
| 3 | Callback tidak validasi | Tambah status mapping & fraud check | ✅ Done |
| 4 | Redirect terlalu cepat | Tambah delay 2 detik | ✅ Done |
| 5 | Tidak ada error handling | Tambah try-catch & logging | ✅ Done |

---

## 📦 File yang Dibuat/Diubah

### **BUAT BARU** ✨
```
✅ app/Models/Payment.php
✅ MIDTRANS_FIX_REPORT.md (dokumentasi lengkap)
✅ MIDTRANS_ENV_SETUP.md (setup environment)
```

### **DIUBAH** 🔧
```
✅ app/Http/Controllers/SnapController.php
✅ app/Http/Controllers/PembayaranController.php
✅ resources/views/backend/pembayaran/payment.blade.php
```

---

## ⚡ LANGKAH-LANGKAH IMPLEMENTASI

### **STEP 1: Update Environment Variable**
```env
# Di file .env, pastikan ada:
MIDTRANS_MERCHANT_ID=your_id
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_IS_PRODUCTION=false  # true untuk production
```

### **STEP 2: Verifikasi Database**
Pastikan table `payment` ada dengan kolom:
```sql
- id (PRIMARY KEY)
- user_id
- tagihan_id
- kelas_id
- bulan_id (nullable)
- nilai
- order_id (UNIQUE)
- pdf_url (nullable)
- metode_pembayaran
- status
- created_at
- updated_at
```

### **STEP 3: Pull Latest Code**
```bash
cd /Users/lpmnudiymacpro/Downloads/sidamar
git pull origin main
```

### **STEP 4: Test di Sandbox**
1. Buka: `http://localhost:8000` (atau URL test Anda)
2. Akses halaman pembayaran
3. Gunakan test card: `4811111111111114`
4. Verifikasi payment tercatat di database

### **STEP 5: Monitor Logs**
```bash
tail -f storage/logs/laravel.log
```

### **STEP 6: Production Ready**
```bash
# Update .env
MIDTRANS_IS_PRODUCTION=true

# Update production credentials
MIDTRANS_SERVER_KEY=xxx_prod_xxx
MIDTRANS_CLIENT_KEY=xxx_prod_xxx
```

---

## 🧪 TEST CASES

### ✅ Test 1: Pembayaran Berhasil
```
Card: 4811111111111114
Expected: Status berubah ke "Lunas"
Check: database payment table
```

### ✅ Test 2: Pembayaran Pending
```
Card: 4911111111111113
Expected: Status tetap "Pending"
Check: tunggu callback 5-10 menit
```

### ✅ Test 3: Pembayaran Gagal
```
Card: 4111111111111112
Expected: Status berubah ke "Failed"
Check: error message tampil
```

### ✅ Test 4: Duplicate Prevention
```
Submit pembayaran 2x dengan data sama
Expected: Hanya 1 record di database
Check: warning message "Pembayaran sudah tercatat"
```

---

## 🔍 TROUBLESHOOTING

### ❌ Error: "Model Payment not found"
**Solusi**: 
- Pastikan file `app/Models/Payment.php` sudah ada
- Run: `composer dump-autoload`

### ❌ Error: "SNAP token generation failed"
**Solusi**:
- Verifikasi `MIDTRANS_SERVER_KEY` di `.env`
- Check logs: `storage/logs/laravel.log`
- Pastikan internet connection aktif

### ❌ Error: "Callback not processed"
**Solusi**:
- Pastikan URL callback registered di Midtrans Dashboard
- URL harus: `https://yourdomain.com/midtrans/callback`
- Check logs untuk error messages

### ❌ Payment tidak masuk ke database
**Solusi**:
- Check: Database connection di `.env`
- Run: `php artisan migrate` (jika belum)
- Pastikan table `payment` sudah ada
- Check: Storage/logs untuk error details

---

## 📊 DATABASE QUERIES UNTUK MONITORING

### Cek semua pembayaran pending:
```sql
SELECT * FROM payment 
WHERE status = 'Pending' 
ORDER BY created_at DESC;
```

### Cek pembayaran hari ini:
```sql
SELECT * FROM payment 
WHERE DATE(created_at) = CURDATE()
ORDER BY created_at DESC;
```

### Cek total pembayaran per bulan:
```sql
SELECT 
    DATE_TRUNC('month', created_at) as bulan,
    COUNT(*) as jumlah,
    SUM(nilai) as total
FROM payment
WHERE status = 'Lunas'
GROUP BY DATE_TRUNC('month', created_at)
ORDER BY bulan DESC;
```

### Cek pembayaran failed:
```sql
SELECT * FROM payment 
WHERE status = 'Failed' 
ORDER BY created_at DESC;
```

---

## 📞 SUPPORT CONTACTS

| Service | Contact | Docs |
|---------|---------|------|
| Midtrans | support@midtrans.com | https://docs.midtrans.com |
| WhatsApp API | support@wa.dlhcode.com | https://wa.dlhcode.com |
| Laravel | stackoverflow.com | https://laravel.com/docs |

---

## 🎯 NEXT MILESTONE

- [ ] Test di Sandbox environment
- [ ] Verifikasi semua card test berhasil
- [ ] Monitor logs selama 24 jam
- [ ] Backup database
- [ ] Switch ke Production credentials
- [ ] Monitor production untuk 1 minggu
- [ ] Archive old payment records (optional)

---

## 📋 CHECKLIST SEBELUM LAUNCH

- [ ] `.env` sudah updated dengan credentials
- [ ] Database migration sudah jalan
- [ ] Table `payment` sudah ada
- [ ] Midtrans Dashboard sudah register URL callback
- [ ] HTTPS sudah aktif (requirement Midtrans)
- [ ] Logs directory writable
- [ ] Storage directory permissions 775
- [ ] Test card transactions berhasil
- [ ] Callback processed di logs
- [ ] User notifications (WhatsApp) berfungsi

---

**Status**: ✅ SIAP UNTUK TESTING

Untuk dokumentasi lebih lengkap, lihat:
- `MIDTRANS_FIX_REPORT.md` - Penjelasan detail semua perbaikan
- `MIDTRANS_ENV_SETUP.md` - Setup environment variable

Pertanyaan? Check logs di `storage/logs/laravel.log`
