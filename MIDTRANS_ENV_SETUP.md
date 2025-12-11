# MIDTRANS INTEGRATION - ENVIRONMENT CONFIGURATION

## 📋 Konfigurasi yang Diperlukan di `.env`

Pastikan Anda memiliki konfigurasi Midtrans berikut di file `.env`:

```env
# ============================================
# MIDTRANS CONFIGURATION
# ============================================

# Mode Production atau Sandbox
# false = Sandbox (Testing)
# true = Production (Live)
MIDTRANS_IS_PRODUCTION=false

# Merchant ID dari Midtrans Dashboard
MIDTRANS_MERCHANT_ID=your_merchant_id_here

# Client Key (untuk frontend)
# Dapatkan dari: https://dashboard.midtrans.com
MIDTRANS_CLIENT_KEY=your_client_key_here

# Server Key (untuk backend - JAGA KERAHASIAAN!)
# Dapatkan dari: https://dashboard.midtrans.com
MIDTRANS_SERVER_KEY=your_server_key_here

# ============================================
# WHATSAPP NOTIFICATION (Optional)
# ============================================

# Token untuk WhatsApp API (dari https://wa.dlhcode.com)
WHATSAPP_TOKEN=your_whatsapp_token_here

# Nomor telepon pengirim
WHATSAPP_PHONE=62812345678
```

---

## 🔐 Cara Mendapatkan Credentials

### 1. **Dari Midtrans Dashboard** (`https://dashboard.midtrans.com`)

**Langkah-langkah**:
1. Login ke Midtrans Dashboard
2. Pilih Environment: **Sandbox** (untuk testing) atau **Production** (untuk live)
3. Pergi ke: `Settings` → `Access Keys`
4. Copy:
   - **Merchant ID**
   - **Client Key**
   - **Server Key** (JANGAN dishare ke siapa pun!)

### 2. **Dari WhatsApp API** (`https://wa.dlhcode.com`)
1. Daftar akun
2. Buat API Token
3. Copy token ke `.env` dengan nama `WHATSAPP_TOKEN`

---

## ✅ Verifikasi Konfigurasi

Buat file `test-midtrans.php` di root project untuk test:

```php
<?php
require 'vendor/autoload.php';

use App\Providers\Helper;

echo "=== MIDTRANS CONFIGURATION TEST ===\n\n";

echo "Server Key: " . substr(Helper::apk()->serverKey, 0, 10) . "****\n";
echo "Client Key: " . substr(Helper::apk()->clientKey, 0, 10) . "****\n";
echo "Merchant ID: " . Helper::apk()->merchantId . "\n";
echo "Is Production: " . (Helper::apk()->isProduction ? "YES" : "NO") . "\n";

echo "\n✅ Konfigurasi valid!\n";
?>
```

Jalankan:
```bash
php test-midtrans.php
```

---

## 🚀 Testing Card Numbers

### Sandbox Testing Cards:

**BERHASIL (Status: settlement/capture)**
- Card: `4811111111111114`
- Exp Month: `12`
- Exp Year: `2027` (atau tahun depan)
- CVV: `123`

**PENDING (Status: pending)**
- Card: `4911111111111113`
- Exp Month: `12`
- Exp Year: `2027`
- CVV: `123`

**GAGAL (Status: denied)**
- Card: `4111111111111112`
- Exp Month: `12`
- Exp Year: `2027`
- CVV: `123`

---

## 📊 Database Columns untuk Payment Table

Pastikan table `payment` sudah ada dengan struktur berikut:

```sql
CREATE TABLE payment (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    tagihan_id BIGINT UNSIGNED,
    kelas_id BIGINT UNSIGNED,
    bulan_id BIGINT UNSIGNED NULL,
    nilai BIGINT NOT NULL,
    order_id VARCHAR(255) UNIQUE,
    pdf_url VARCHAR(255) NULL,
    metode_pembayaran VARCHAR(50),
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    -- Tambahkan foreign key lainnya sesuai kebutuhan
    INDEX idx_order_id (order_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);
```

---

## 🔍 Monitoring & Debugging

### 1. **View Payment Logs**
```bash
tail -f storage/logs/laravel.log | grep "Midtrans"
```

### 2. **Check Payment Records**
```sql
SELECT * FROM payment 
WHERE status = 'Pending' 
ORDER BY created_at DESC 
LIMIT 10;
```

### 3. **Check Failed Transactions**
```sql
SELECT * FROM payment 
WHERE status = 'Failed' 
ORDER BY created_at DESC 
LIMIT 10;
```

---

## ⚠️ PENTING!

1. **JANGAN PERNAH** commit file `.env` ke repository
2. **JANGAN SHARE** Server Key Anda
3. **GUNAKAN** HTTPS pada production
4. **BACKUP** database secara regular
5. **MONITOR** logs untuk error atau suspicious activity

---

## 📞 KONTAK SUPPORT

- **Midtrans**: https://www.midtrans.com/contact-us
- **WhatsApp API**: https://wa.dlhcode.com/support
- **Documentation**: https://docs.midtrans.com
