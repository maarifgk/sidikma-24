# 🚀 TROUBLESHOOTING MIDTRANS 401 ERROR - STEP BY STEP

## 🎯 QUICK FIX (5 menit)

Jika Anda dapat credentials Midtrans:

### Step 1: Copy credentials dari Midtrans Dashboard
```
URL: https://dashboard.midtrans.com
Settings → Access Keys → Copy
- Merchant ID: G123456
- Client Key: VT-client-xxxx
- Server Key: VT-server-xxxx
```

### Step 2: Paste ke `.env` file
```env
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_MERCHANT_ID=G123456
MIDTRANS_CLIENT_KEY=VT-client-xxxx
MIDTRANS_SERVER_KEY=VT-server-xxxx
```

### Step 3: Clear config cache
```bash
php artisan config:clear
```

### Step 4: Test
```
Pembuka halaman pembayaran → Test dengan card 4811111111111114
```

**Expected**: ✅ Tidak ada error 401

---

## 🔍 DIAGNOSIS

Jika masih error, jalankan diagnostic:

```bash
# Terminal
php artisan tinker
```

```php
# Di Tinker console

# Check credentials
>>> $helper = App\Providers\Helper::apk()

# Verify setiap credential
>>> $helper->serverKey
"VT-server-xxxx..."  ← Harus ada value

>>> $helper->clientKey  
"VT-client-xxxx..."   ← Harus ada value

>>> $helper->merchantId
"G123456"             ← Harus ada value

>>> $helper->isProduction
false                 ← Untuk testing, harus false
```

### Jika serverKey/clientKey kosong:

```
MASALAH: Credentials tidak ada di .env dan database
SOLUSI: 
1. Update .env dengan credentials Midtrans yang benar
2. ATAU update database aplikasi table dengan credentials
3. Run: php artisan config:clear
4. Test lagi
```

---

## 📝 DETAILED CHECKLIST

### ✅ Check 1: Credentials Tersedia
```bash
php artisan tinker
>>> $cred = App\Providers\Helper::apk()
>>> dd($cred)
```

**Expected Output**:
```
{
  "merchantId": "G123456",
  "clientKey": "VT-client-xxxx",
  "serverKey": "VT-server-xxxx",
  "isProduction": false,
  ...
}
```

❌ Jika kosong atau null → Update `.env` atau database

### ✅ Check 2: Credentials Format Benar
```php
>>> strlen($cred->serverKey) > 10
true

>>> strlen($cred->clientKey) > 10  
true

>>> $cred->serverKey[0..10]
"VT-server-"  ← Harus start dengan VT-server-
```

❌ Jika format salah → Copy ulang dari Midtrans

### ✅ Check 3: Environment Mode Sesuai
```php
>>> $cred->isProduction
false  ← Untuk testing/sandbox
true   ← Untuk production
```

⚠️ **PENTING**: Sandbox credentials hanya bekerja di sandbox mode, tidak bisa digunakan untuk production

### ✅ Check 4: Test Dengan SDK Midtrans
```php
// Di Tinker
>>> \Midtrans\Config::$serverKey = $cred->serverKey
>>> \Midtrans\Config::$clientKey = $cred->clientKey
>>> \Midtrans\Config::$isProduction = $cred->isProduction

// Test connection
>>> try {
    $snapToken = \Midtrans\Snap::getSnapToken([
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
    echo "SUCCESS: " . $snapToken;
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
```

✅ Jika success → Credentials benar  
❌ Jika error → Credentials salah

---

## 🔧 SETUP OPTIONS

### OPSI A: Menggunakan .env (RECOMMENDED)

**Pros:**
- ✅ Credentials aman (tidak di database)
- ✅ Mudah ganti per environment
- ✅ Sesuai best practices
- ✅ Tidak perlu akses admin panel

**Steps:**
```bash
# 1. Update .env
MIDTRANS_SERVER_KEY=VT-server-xxxx
MIDTRANS_CLIENT_KEY=VT-client-xxxx
MIDTRANS_MERCHANT_ID=G123456
MIDTRANS_IS_PRODUCTION=false

# 2. Clear cache
php artisan config:clear

# 3. Test
php artisan tinker
>>> App\Providers\Helper::apk()->serverKey
```

### OPSI B: Menggunakan Database

**Pros:**
- ✅ Bisa update dari admin panel
- ✅ Credentials bisa berubah tanpa restart

**Cons:**
- ❌ Credentials tersimpan di database (kurang aman)
- ❌ Harus punya akses admin panel

**Steps:**
```bash
# 1. Login ke admin panel aplikasi
# 2. Pergi ke: Settings → Aplikasi
# 3. Update:
#    - Server Key: VT-server-xxxx
#    - Client Key: VT-client-xxxx
# 4. Klik Save

# OR gunakan SQL:
mysql> UPDATE aplikasi SET 
        serverKey = 'VT-server-xxxx',
        clientKey = 'VT-client-xxxx'
        WHERE id = 1;
```

---

## 📊 DECISION MATRIX

| Faktor | .env File | Database |
|--------|-----------|----------|
| **Keamanan** | ⭐⭐⭐⭐⭐ | ⭐⭐ |
| **Kemudahan Update** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Multi-Environment** | ⭐⭐⭐⭐⭐ | ⭐ |
| **Best Practice** | ⭐⭐⭐⭐⭐ | ⭐⭐ |
| **Rekomendasi** | **✅ PILIH INI** | ⚠️ Backup option |

---

## 🌍 ENVIRONMENT COMPARISON

### Sandbox (Testing)
```env
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_CLIENT_KEY=VT-client-xxxSandboxKey
MIDTRANS_SERVER_KEY=VT-server-xxxSandboxKey
```

**Karakteristik**:
- Gratis, tanpa komisi
- Test cards tersedia
- Tidak ada real money
- Untuk development/testing

### Production (Live)
```env
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_CLIENT_KEY=VT-client-xxxProductionKey
MIDTRANS_SERVER_KEY=VT-server-xxxProductionKey
```

**Karakteristik**:
- Real transactions
- Real money involved
- Komisi per transaksi
- Untuk production/live

⚠️ **PENTING**: Gunakan sandbox dulu sebelum production!

---

## 🧪 FULL TEST PROCEDURE

```bash
# 1. Setup credentials
# Edit .env dengan credentials Midtrans sandbox

# 2. Clear cache
php artisan config:clear
php artisan cache:clear

# 3. Verify credentials
php artisan tinker
>>> App\Providers\Helper::apk()->serverKey
# Should return: VT-server-xxxxx

# 4. Start server
php artisan serve

# 5. Test pembayaran
# Buka: http://localhost:8000
# Akses halaman pembayaran
# Input test card: 4811111111111114
# Exp: 12/2027, CVV: 123

# 6. Verify database
mysql> SELECT * FROM payment WHERE order_id LIKE 'ORDER-%' ORDER BY id DESC LIMIT 1;
# Should show: status = 'Pending' (waiting for callback)

# 7. Check logs
tail -f storage/logs/laravel.log
# Should show: "Midtrans Snap Token Generated"
```

---

## 🚨 COMMON ERRORS & SOLUTIONS

### Error 1: serverKey/clientKey Empty
```
Error: Gagal membuat token pembayaran: Midtrans API is returning API error. HTTP status code: 401
```

**Solusi**:
1. Check `.env` file ada credentials
2. Check database aplikasi table punya credentials
3. Run: `php artisan config:clear`

### Error 2: Wrong Credentials
```
Error: Access denied due to unauthorized transaction
```

**Solusi**:
1. Login ke Midtrans Dashboard
2. Copy credentials lagi (exact, jangan ada space)
3. Update `.env` atau database
4. Test ulang

### Error 3: Wrong Environment Mode
```
Error: 401 Unauthorized
```

**Solusi**:
1. If menggunakan sandbox credentials: `MIDTRANS_IS_PRODUCTION=false`
2. If menggunakan production credentials: `MIDTRANS_IS_PRODUCTION=true`
3. Jangan mix-and-match!

### Error 4: Typo di Credentials
```
Error: 401 Unauthorized
```

**Solusi**:
1. Copy dari Midtrans Dashboard (jangan manual type)
2. Paste ke `.env` (jangan ada space di awal/akhir)
3. Verify dengan: `cat .env | grep MIDTRANS`
4. Test dengan tinker

---

## 📞 FINAL CHECKLIST

Sebelum declare "fixed":

- [ ] Credentials ada di `.env` atau database
- [ ] Credentials format benar (start dengan VT-server-, VT-client-)
- [ ] Environment mode sesuai (sandbox vs production)
- [ ] `php artisan config:clear` sudah dijalankan
- [ ] Test dengan tinker berhasil
- [ ] Test pembayaran dengan card 4811111111111114 berhasil
- [ ] Logs menunjukkan "Snap Token Generated"
- [ ] Database payment table terisi dengan data pembayaran

---

**Status**: ✅ Ready untuk troubleshooting

**Next Step**: Ikuti "QUICK FIX" di atas untuk instant solution
