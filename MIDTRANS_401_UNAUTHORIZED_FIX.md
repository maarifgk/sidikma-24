# 🔴 ERROR 401: Unauthorized Transaction - API Key Salah

## 🎯 MASALAH

```
HTTP status code: 401
"Access denied due to unauthorized transaction, please check client or server key"
```

## 🔍 ROOT CAUSE

Credentials Midtrans di table `aplikasi` **tidak match** dengan credentials Midtrans yang sebenarnya. Ini bisa terjadi karena:

1. ❌ Server Key atau Client Key di database **salah/typo**
2. ❌ Server Key atau Client Key **belum diupdate** di database
3. ❌ Menggunakan **sandbox credentials** tetapi set production mode
4. ❌ Menggunakan **production credentials** tetapi set sandbox mode

---

## ✅ SOLUSI

### OPSI 1: RECOMMENDED - Gunakan `.env` File (Paling Aman)

**Mengapa `.env` lebih baik?**
- ✅ Credentials tidak tersimpan di database (lebih aman)
- ✅ Mudah ganti per environment (sandbox vs production)
- ✅ Tidak perlu akses ke admin panel untuk update
- ✅ Sesuai best practices Laravel

#### Step 1: Update `.env` File

Buka file `.env` di root project:

```env
# .env (di root project)

# ============================================
# MIDTRANS CONFIGURATION
# ============================================

# Test ini dengan Sandbox dulu (false = sandbox)
MIDTRANS_IS_PRODUCTION=false

# Dapatkan dari Midtrans Dashboard: https://dashboard.midtrans.com
# Settings → Access Keys → Sandbox (atau Production)

MIDTRANS_MERCHANT_ID=G12345678
MIDTRANS_CLIENT_KEY=VT-client-xxxxxxxxxxxx
MIDTRANS_SERVER_KEY=VT-server-xxxxxxxxxxxx
```

#### Step 2: Update Helper Class

Edit `app/Providers/Helper.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class Helper
{
    static public function apk()
    {
        // OPSI 1: Ambil dari .env (RECOMMENDED)
        return (object) [
            'merchantId' => env('MIDTRANS_MERCHANT_ID'),
            'clientKey' => env('MIDTRANS_CLIENT_KEY'),
            'serverKey' => env('MIDTRANS_SERVER_KEY'),
            'isProduction' => env('MIDTRANS_IS_PRODUCTION', false),
            'token_whatsapp' => env('WHATSAPP_TOKEN'),
            'tlp' => env('WHATSAPP_PHONE'),
        ];

        // OPSI 2 (lama): Ambil dari database
        // Uncomment jika masih ingin gunakan database
        // return DB::table('aplikasi')->first();
    }

    static public function log_transaction($params)
    {
        $data = [
            'user_id'    => request()->user()->id,
            'activity'  => empty($params['activity']) ? "" : $params['activity'],
            'ctime'     => date('Y-m-d H:i:s'),
            'ip'        => $_SERVER['REMOTE_ADDR'],
            'detail'    => !empty($params['detail']) ? $params['detail'] : "",
        ];

        return DB::table('mm_logs')->insert($data);
    }
}
```

#### Step 3: Update SnapController Config

Di `app/Http/Controllers/SnapController.php`, ganti semua:

```php
// DARI:
\Midtrans\Config::$isProduction = (env('MIDTRANS_IS_PRODUCTION', false) == true);

// KE:
\Midtrans\Config::$isProduction = Helper::apk()->isProduction;
```

---

### OPSI 2: Update Database (Jika ingin tetap pakai database)

#### Step 1: Get Credentials Dari Midtrans

1. Login ke: https://dashboard.midtrans.com
2. Pilih environment: **Sandbox** (untuk testing)
3. Pergi ke: **Settings** → **Access Keys**
4. Copy:
   - **Merchant ID**
   - **Client Key** (warna biru)
   - **Server Key** (warna merah - JANGAN SHARE!)

#### Step 2: Update Database

Buka admin panel aplikasi Anda dan edit setting Midtrans:
- Pergi ke: **Settings** → **Aplikasi**
- Update:
  - Server Key: `VT-server-xxxxxxxxxxxxxx`
  - Client Key: `VT-client-xxxxxxxxxxxxxx`
- Klik **Save**

Atau gunakan SQL langsung:

```sql
UPDATE aplikasi SET 
    serverKey = 'VT-server-xxxxxxxxxxxx',
    clientKey = 'VT-client-xxxxxxxxxxxx'
WHERE id = 1;
```

---

## 🔑 CARA DAPATKAN API KEY MIDTRANS

### Step 1: Login ke Midtrans Dashboard
- URL: https://dashboard.midtrans.com
- Login dengan akun Midtrans Anda

### Step 2: Pilih Environment
- **Sandbox**: Untuk testing (gunakan ini dulu!)
- **Production**: Untuk live transaction

### Step 3: Akses Access Keys
- Menu: **Settings** → **Access Keys**

### Step 4: Copy Credentials
```
Merchant ID:  G12345678              ← Copy ini
Client Key:   VT-client-xxxxx...     ← Copy ini ke .env atau DB
Server Key:   VT-server-xxxxx...     ← Copy ini (RAHASIA!)
```

---

## ✅ VERIFICATION CHECKLIST

Sebelum test, pastikan:

- [ ] `.env` file sudah update dengan credentials yang benar
- [ ] ATAU database `aplikasi` table sudah update
- [ ] `MIDTRANS_IS_PRODUCTION=false` (untuk Sandbox testing)
- [ ] Helper::apk() mengembalikan object dengan properti yang benar

### Quick Check di Tinker:

```php
# Buka terminal
php artisan tinker

# Test credentials
>>> $cred = App\Providers\Helper::apk()
>>> $cred->serverKey
>>> $cred->clientKey
>>> $cred->merchantId
```

---

## 🧪 TEST CARD NUMBERS (SANDBOX)

Gunakan ini untuk testing (hanya berlaku di Sandbox):

### ✅ Success
- Card: `4811111111111114`
- Exp: `12/2027`
- CVV: `123`
- Result: Settlement/Success

### ⏳ Pending
- Card: `4911111111111113`
- Exp: `12/2027`
- CVV: `123`
- Result: Pending

### ❌ Deny
- Card: `4111111111111112`
- Exp: `12/2027`
- CVV: `123`
- Result: Denied

---

## 🚀 RECOMMENDED SETUP

```
┌─────────────────────────────────────────────┐
│         RECOMMENDED ARCHITECTURE            │
├─────────────────────────────────────────────┤
│                                             │
│  .env (Environment Variables)               │
│  ├─ MIDTRANS_MERCHANT_ID                   │
│  ├─ MIDTRANS_CLIENT_KEY                    │
│  ├─ MIDTRANS_SERVER_KEY                    │
│  └─ MIDTRANS_IS_PRODUCTION                 │
│           ↓                                 │
│  Helper::apk() ← Ambil dari .env             │
│           ↓                                 │
│  SnapController ← Gunakan Helper             │
│                                             │
│  Database `aplikasi` (untuk config lain)   │
│  ├─ nama_aplikasi                          │
│  ├─ nama_owner                             │
│  └─ ... (bukan API keys)                   │
│                                             │
└─────────────────────────────────────────────┘
```

---

## ⚠️ PENTING!

1. **JANGAN HARDCODE** Server Key di kode
2. **JANGAN COMMIT** Server Key ke Git
3. **JANGAN SHARE** Server Key ke siapa pun
4. **SELALU GUNAKAN** `.env` atau environment variables
5. **PRODUCTION**: Ganti ke production credentials hanya saat live

---

## 📋 NEXT STEPS

1. ✅ Dapatkan credentials dari Midtrans Dashboard
2. ✅ Update `.env` atau database dengan credentials yang benar
3. ✅ Verify credentials dengan tinker command
4. ✅ Test pembayaran dengan test card
5. ✅ Monitor logs untuk error

---

## 🔗 RESOURCES

- 📖 Midtrans Docs: https://docs.midtrans.com
- 🔐 Get API Key: https://dashboard.midtrans.com
- 📚 Laravel .env: https://laravel.com/docs/configuration
- 🧪 Sandbox Testing: https://docs.midtrans.com/reference/sandbox-environment

---

**Status**: 🔧 ACTION REQUIRED

**Next Action**: Update `.env` atau database dengan credentials Midtrans yang benar
