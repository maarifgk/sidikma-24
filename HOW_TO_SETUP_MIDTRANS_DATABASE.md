# 📝 HOW TO: Setup Midtrans Credentials di Database Aplikasi

## 🎯 OVERVIEW

ServerKey dan ClientKey Midtrans Anda disimpan di table `aplikasi` di database. Berikut cara untuk set/update credentials tersebut.

---

## 🔑 STEP 1: Dapatkan Credentials dari Midtrans

### Buka Midtrans Dashboard
1. URL: https://dashboard.midtrans.com
2. Login dengan akun Midtrans Anda

### Copy Credentials
1. Pilih Environment: **Sandbox** (untuk testing) atau **Production** (untuk live)
2. Pergi ke: **Settings** → **Access Keys**
3. Copy:
   - **Merchant ID**: `G12345678` (jika ada)
   - **Client Key** (warna biru): `VT-client-xxxxxxxx`
   - **Server Key** (warna merah): `VT-server-xxxxxxxx`

⚠️ **PENTING**: Jangan share Server Key ke siapa pun!

---

## 📊 STEP 2: Simpan ke Database

Ada 3 cara untuk save credentials ke database aplikasi:

### CARA 1: Menggunakan Admin Panel (Paling Mudah) ✅

**Jika Anda punya akses admin panel:**

1. Login ke admin panel aplikasi Anda
2. Pergi ke: **Settings** → **Aplikasi** (atau menu sejenis)
3. Cari field:
   - **Server Key**: Paste `VT-server-xxxxxxxx`
   - **Client Key**: Paste `VT-client-xxxxxxxx`
4. Scroll ke bawah → Klik tombol **Save** atau **Update**
5. Verifikasi: Harusnya ada notifikasi sukses

✅ Selesai! Credentials sudah tersimpan di database.

---

### CARA 2: Menggunakan SQL Query (Jika tidak punya akses admin)

Jika Anda punya akses database langsung (PHPMyAdmin atau command line):

```sql
-- Buka database MySQL/MariaDB

-- Gunakan database yang tepat
USE sidamar;

-- Update table aplikasi dengan credentials
UPDATE aplikasi SET 
    serverKey = 'VT-server-xxxxxxxx',
    clientKey = 'VT-client-xxxxxxxx',
    token_whatsapp = '123456789',  -- Optional: WhatsApp API token
    tlp = '62812345678'             -- Optional: WhatsApp phone
WHERE id = 1;

-- Verify perubahan
SELECT serverKey, clientKey, token_whatsapp, tlp FROM aplikasi WHERE id = 1;
```

**Output yang diharapkan**:
```
+-------------------+-------------------+---------------------+---------------+
| serverKey         | clientKey         | token_whatsapp      | tlp           |
+-------------------+-------------------+---------------------+---------------+
| VT-server-xxxxx   | VT-client-xxxxx   | 123456789           | 62812345678   |
+-------------------+-------------------+---------------------+---------------+
```

✅ Selesai! Credentials sudah tersimpan.

---

### CARA 3: Menggunakan PHPMyAdmin

**Jika Anda gunakan PHPMyAdmin:**

1. Buka PHPMyAdmin (biasanya di `http://localhost/phpmyadmin`)
2. Pilih database: **sidamar** (atau nama database Anda)
3. Pilih table: **aplikasi**
4. Klik tombol **Edit** pada row id=1
5. Cari dan edit field:
   - `serverKey`: `VT-server-xxxxxxxx`
   - `clientKey`: `VT-client-xxxxxxxx`
6. Scroll ke bawah → Klik **Go** atau **Save**

✅ Selesai!

---

## ✅ STEP 3: Verifikasi Credentials Tersimpan

Setelah save, pastikan credentials benar-benar tersimpan dengan mengecek database:

### Menggunakan Command Line:
```bash
# Login ke MySQL
mysql -u root -p sidamar

# Check credentials
SELECT serverKey, clientKey FROM aplikasi WHERE id = 1;
```

**Expected Output:**
```
+-------------------+-------------------+
| serverKey         | clientKey         |
+-------------------+-------------------+
| VT-server-xxxxx   | VT-client-xxxxx   |
+-------------------+-------------------+
```

### Atau Menggunakan PHP Artisan Tinker:
```bash
# Jalankan tinker
php artisan tinker

# Di Tinker console
>>> $apk = DB::table('aplikasi')->first()
>>> $apk->serverKey
"VT-server-xxxxx"  ← Harus ada value

>>> $apk->clientKey
"VT-client-xxxxx"  ← Harus ada value
```

---

## 🧪 STEP 4: Test Credentials

Setelah credentials tersimpan, test apakah bekerja:

### Test dengan Tinker:
```bash
php artisan tinker
```

```php
# Di Tinker console

# Ambil credentials dari database
>>> $apk = App\Providers\Helper::apk()

# Verify credentials ada
>>> $apk->serverKey
"VT-server-xxxxx"

>>> $apk->clientKey
"VT-client-xxxxx"

# Konfigurasi Midtrans dengan credentials tersebut
>>> \Midtrans\Config::$serverKey = $apk->serverKey
>>> \Midtrans\Config::$clientKey = $apk->clientKey
>>> \Midtrans\Config::$isProduction = false

# Test membuat Snap Token
>>> try {
    $token = \Midtrans\Snap::getSnapToken([
        'transaction_details' => [
            'order_id' => 'TEST-' . time(),
            'gross_amount' => 100000,
        ],
        'item_details' => [[
            'id' => 'TEST',
            'price' => 100000,
            'quantity' => 1,
            'name' => 'Test Payment',
        ]],
    ]);
    echo "SUCCESS: Token = " . substr($token, 0, 50) . "...";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
```

**Expected Result:**
```
SUCCESS: Token = eyJub2RlX2lkIjoiMjAxNDExMjc5MTA0NDQ...
```

✅ Jika ada token → Credentials benar!
❌ Jika error → Credentials salah atau format tidak cocok

---

## 🔍 DATABASE SCHEMA

Table `aplikasi` memiliki struktur:

```sql
CREATE TABLE aplikasi (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    nama_owner VARCHAR(255),
    alamat TEXT,
    tlp VARCHAR(20),
    nama_aplikasi VARCHAR(255),
    copy_right VARCHAR(255),
    versi VARCHAR(50),
    token_whatsapp VARCHAR(255),
    serverKey VARCHAR(255),          ← Midtrans Server Key
    clientKey VARCHAR(255),          ← Midtrans Client Key
    logo VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    ...
);
```

---

## 📋 CREDENTIALS CHECKLIST

Sebelum declare "selesai", pastikan:

- [ ] Login ke Midtrans Dashboard
- [ ] Copy Server Key (`VT-server-xxxxxxxx`)
- [ ] Copy Client Key (`VT-client-xxxxxxxx`)
- [ ] Save ke database aplikasi table
- [ ] Verify dengan SQL query atau tinker
- [ ] Test membuat Snap Token berhasil
- [ ] Tidak ada error 401 saat payment

---

## 🚀 NEXT STEPS

Setelah credentials tersimpan dengan benar:

1. ✅ Buka halaman pembayaran di aplikasi
2. ✅ Klik tombol **Bayar**
3. ✅ Test dengan card: `4811111111111114`
4. ✅ Harusnya Snap form muncul
5. ✅ Verify payment tercatat di database

---

## ⚠️ TROUBLESHOOTING

### Error: "Unknown column 'serverKey' in table 'aplikasi'"
**Solusi**: Table aplikasi tidak memiliki kolom serverKey
- Update database schema dengan menambah kolom
- Atau gunakan database yang sudah punya kolom tersebut

```sql
ALTER TABLE aplikasi ADD COLUMN serverKey VARCHAR(255) AFTER tlp;
ALTER TABLE aplikasi ADD COLUMN clientKey VARCHAR(255) AFTER serverKey;
```

### Error: "Credentials empty"
**Solusi**: serverKey atau clientKey kosong di database
- Check database apakah ada value
- Copy ulang dari Midtrans Dashboard (jangan ada space)
- Paste lagi ke database

### Error: "401 Unauthorized"
**Solusi**: Credentials salah atau tidak sesuai
- Verify credentials di Midtrans Dashboard
- Copy ulang (exact match, no typo)
- Test dengan tinker untuk debug

---

## 📞 QUICK REFERENCE

| Kolom | Contoh Value | Sumber |
|-------|-------------|--------|
| serverKey | `VT-server-xxxxx` | Midtrans Dashboard |
| clientKey | `VT-client-xxxxx` | Midtrans Dashboard |
| token_whatsapp | `1234567890` | WhatsApp API |
| tlp | `628123456789` | Nomor WhatsApp bisnis |

---

**Status**: ✅ Ready untuk setup

**Next Action**: 
1. Get credentials dari Midtrans
2. Save ke database menggunakan salah satu CARA di atas
3. Test dengan tinker atau payment form
4. Report hasil di chat
