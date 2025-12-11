# ✅ MIDTRANS INTEGRATION - TESTING & DEPLOYMENT CHECKLIST

## 🧪 PRE-DEPLOYMENT CHECKLIST

### Environment Setup
- [ ] `.env` file updated dengan Midtrans credentials
  ```
  MIDTRANS_MERCHANT_ID=xxxxx
  MIDTRANS_CLIENT_KEY=xxxxx
  MIDTRANS_SERVER_KEY=xxxxx
  MIDTRANS_IS_PRODUCTION=false (for sandbox)
  ```
- [ ] Database migration completed
- [ ] Payment table exists dengan struktur yang benar
- [ ] Storage & logs directory permissions correct (755)
- [ ] HTTPS enabled (requirement dari Midtrans)

### Code Verification
- [ ] `app/Models/Payment.php` sudah ada
- [ ] `app/Http/Controllers/SnapController.php` sudah updated
- [ ] `app/Http/Controllers/PembayaranController.php` sudah updated
- [ ] `resources/views/backend/pembayaran/payment.blade.php` sudah updated
- [ ] Semua imports sudah correct (use Payment, use Log, etc)
- [ ] No syntax errors: `php artisan tinker` (quit with exit)

### Midtrans Dashboard Config
- [ ] Callback URL registered: `https://yourdomain.com/midtrans/callback`
- [ ] Server Key sudah di-generate dan secured
- [ ] Client Key sudah di-copy ke frontend config
- [ ] Sandbox environment selected (untuk testing)
- [ ] IP address di-whitelist (jika diperlukan)

---

## 🧪 SANDBOX TESTING PHASE

### Test Environment Setup
```bash
# 1. Start local server
php artisan serve

# 2. In another terminal, watch logs
tail -f storage/logs/laravel.log | grep Midtrans

# 3. In another terminal, monitor database
watch -n 1 'mysql -uroot -p sidamar -e "SELECT * FROM payment ORDER BY id DESC LIMIT 5;"'
```

### Test Case 1: Successful Payment ✅
**Objective**: Verify payment berhasil dicatat dengan status "Lunas"

**Steps**:
1. Buka halaman pembayaran
2. Pilih metode pembayaran: **Online**
3. Klik tombol **Bayar**
4. Di Snap payment form, input:
   - Card Number: `4811111111111114`
   - Exp Month: `12`
   - Exp Year: `2027`
   - CVV: `123`
5. Klik **Bayar**

**Verification**:
- [ ] Snap payment form tampil
- [ ] Payment form accept card
- [ ] Page tidak crash
- [ ] Redirect ke halaman sebelumnya
- [ ] Database payment table updated:
  ```sql
  SELECT * FROM payment WHERE status = 'Lunas' ORDER BY id DESC LIMIT 1;
  ```
- [ ] Logs menunjukkan "Callback received":
  ```
  grep "Midtrans Callback Received" storage/logs/laravel.log
  ```
- [ ] WhatsApp notification terkirim (jika enabled)

**Expected Result**: 
```
✅ status = 'Lunas'
✅ order_id = 'ORDER-...'
✅ metode_pembayaran = 'credit_card'
```

---

### Test Case 2: Pending Payment ⏳
**Objective**: Verify payment dengan status pending ditangani dengan benar

**Steps**:
1. Buka halaman pembayaran
2. Pilih metode: **Online**
3. Klik **Bayar**
4. Input card test pending:
   - Card Number: `4911111111111113`
   - Exp Month: `12`
   - Exp Year: `2027`
   - CVV: `123`

**Verification**:
- [ ] Page tampil dengan status "Pending"
- [ ] Database updated dengan status "Pending"
- [ ] Logs menunjukkan pending status
- [ ] Tunggu 10 menit, lihat apakah status berubah

**Expected Result**:
```
✅ status = 'Pending' (untuk beberapa menit)
✅ Tunggu callback dari Midtrans untuk auto-update
```

---

### Test Case 3: Failed Payment ❌
**Objective**: Verify failed payment ditangani dengan error message

**Steps**:
1. Buka halaman pembayaran
2. Pilih metode: **Online**
3. Klik **Bayar**
4. Input card test failed:
   - Card Number: `4111111111111112`
   - Exp Month: `12`
   - Exp Year: `2027`
   - CVV: `123`

**Verification**:
- [ ] Payment form reject card
- [ ] Error message tampil: "Pembayaran gagal"
- [ ] Database payment tetap ada dengan status "Failed"
- [ ] Logs menunjukkan error

**Expected Result**:
```
✅ status = 'Failed'
✅ User dapat retry pembayaran
```

---

### Test Case 4: Duplicate Prevention 🔒
**Objective**: Prevent duplikasi pembayaran untuk order_id yang sama

**Steps**:
1. Siapkan payment dengan order_id: `ORDER-5-23-1702347600`
2. Insert ke database secara manual:
   ```sql
   INSERT INTO payment (user_id, tagihan_id, order_id, status, created_at)
   VALUES (5, 23, 'ORDER-5-23-1702347600', 'Lunas', NOW());
   ```
3. Coba submit payment dengan order_id yang sama lewat form
4. Simulasi callback dengan order_id yang sama

**Verification**:
- [ ] Warning message: "Pembayaran sudah tercatat"
- [ ] Database tidak add duplicate record
- [ ] Only 1 payment record per order_id

**Expected Result**:
```
✅ Hanya 1 record di database
✅ User dapat mengerti status pembayaran
```

---

### Test Case 5: Callback Processing 📞
**Objective**: Verify callback dari Midtrans diproses dengan benar

**Steps**:
1. Buat payment dengan status "Pending"
2. Simulasi callback menggunakan curl atau Postman

```bash
curl -X POST https://yourdomain.com/midtrans/callback \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": "ORDER-5-23-1702347600",
    "transaction_id": "xxx123xxx",
    "payment_type": "credit_card",
    "transaction_status": "settlement",
    "fraud_status": "accept"
  }'
```

**Verification**:
- [ ] HTTP 200 response returned
- [ ] Database payment updated ke "Lunas"
- [ ] Logs menunjukkan "Payment marked as LUNAS"

**Expected Result**:
```
✅ response code 200
✅ status updated correctly
✅ Logged properly
```

---

### Test Case 6: Error Handling & Logging 📝
**Objective**: Verify error handling dan logging berfungsi

**Steps**:
1. Disable internet connection saat request snap token
2. Submit form dengan data invalid
3. Check logs untuk error messages

**Verification**:
- [ ] Error message tampil ke user
- [ ] Detailed error logged di `storage/logs/laravel.log`
- [ ] Stack trace tercatat
- [ ] No database corruption

**Expected Result**:
```
✅ Log entry: [ERROR] Midtrans Payment Error
✅ Stack trace lengkap tersimpan
✅ User-friendly error message
```

---

### Test Case 7: Manual Payment Method 💰
**Objective**: Verify manual payment method (pembayaran manual)

**Steps**:
1. Buka halaman pembayaran
2. Pilih metode: **Manual**
3. Klik **Bayar**

**Verification**:
- [ ] Form submit langsung (tidak ke Midtrans)
- [ ] Database entry created dengan status "Lunas"
- [ ] Success message tampil
- [ ] No Snap form appears

**Expected Result**:
```
✅ status = 'Lunas' (langsung)
✅ metode_pembayaran = 'Manual'
✅ No order_id generated
```

---

## 📊 TEST RESULTS TEMPLATE

### Test Summary
```
Test Date: 2025-12-11
Environment: Sandbox / Production
Tester: [Your Name]

Total Tests: 7
Passed: ___
Failed: ___
Blocked: ___

Overall Status: ☐ PASS / ☐ FAIL / ☐ PARTIAL
```

### Test Details
```
Test Case 1: Successful Payment
Status: ☐ PASS ☐ FAIL
Notes: _______________

Test Case 2: Pending Payment
Status: ☐ PASS ☐ FAIL
Notes: _______________

... (repeat untuk semua test cases)
```

---

## 🚀 DEPLOYMENT STEPS

### Step 1: Final Code Review
```bash
# Check all modified files
git diff origin/main

# Verify no syntax errors
php artisan tinker
> exit
```

### Step 2: Pre-Production Checks
```bash
# Check database migrations
php artisan migrate:status

# Check if all models can be loaded
php artisan tinker
> $payment = App\Models\Payment::first();
> exit

# Check logs directory is writable
ls -la storage/logs/

# Check config
php artisan config:cache
php artisan config:clear
```

### Step 3: Deploy Code
```bash
# Update production branch
git checkout main
git pull origin main

# Install dependencies (if needed)
composer install --optimize-autoloader --no-dev

# Clear caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Step 4: Update Environment
```bash
# Update .env untuk production
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_SERVER_KEY=<production_server_key>
MIDTRANS_CLIENT_KEY=<production_client_key>

# Restart workers (jika menggunakan queue)
php artisan queue:restart
```

### Step 5: Verify Production
```bash
# Check logs
tail -f storage/logs/laravel.log

# Monitor payment table
watch -n 1 'mysql -uroot -p sidamar -e "SELECT COUNT(*) FROM payment; SELECT * FROM payment ORDER BY id DESC LIMIT 5;"'

# Check Midtrans Dashboard untuk transaction history
# Visit: https://dashboard.midtrans.com
```

---

## 📈 MONITORING CHECKLIST

### Daily Monitoring (First Week)
- [ ] Check logs for errors: `grep ERROR storage/logs/laravel.log`
- [ ] Verify payment processing: `SELECT COUNT(*) FROM payment WHERE DATE(created_at) = CURDATE();`
- [ ] Check failed transactions: `SELECT * FROM payment WHERE status = 'Failed' ORDER BY id DESC LIMIT 10;`
- [ ] Monitor callback processing: `grep "Callback Received" storage/logs/laravel.log | tail -20`
- [ ] Verify WhatsApp notifications sent
- [ ] Check Midtrans settlement dashboard

### Weekly Monitoring (After First Week)
- [ ] Payment success rate (target: 95%+)
- [ ] Average processing time
- [ ] Failed transaction reasons
- [ ] Duplicate payment incidents (target: 0)
- [ ] Database growth rate

### Metrics to Track
```sql
-- Payment summary
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_payments,
    SUM(CASE WHEN status = 'Lunas' THEN 1 ELSE 0 END) as successful,
    SUM(CASE WHEN status = 'Failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'Lunas' THEN nilai ELSE 0 END) as total_lunas
FROM payment
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

---

## 🚨 ROLLBACK PROCEDURES

Jika ada masalah critical, cara untuk rollback:

```bash
# 1. Revert code ke versi sebelumnya
git revert HEAD  # Creates new commit
# OR
git reset --hard HEAD~1  # Discard changes (dangerous!)

# 2. Stop payment processing
# Di .env, set: MIDTRANS_IS_PRODUCTION=false
# Update Midtrans callback URL ke development

# 3. Investigate issues
tail -f storage/logs/laravel.log

# 4. Fix dan test lagi
# ... make fixes
# ... run tests

# 5. Re-deploy
git push origin main
```

---

## 📞 EMERGENCY CONTACTS

| Issue | Action | Contact |
|-------|--------|---------|
| Midtrans Callback Not Working | Check callback URL configuration | Midtrans Support |
| Payment Not Being Saved | Check database & logs | Dev Team |
| Duplicate Payments | Check unique constraint on order_id | Database Admin |
| WhatsApp Notifications Fail | Check API credentials | WhatsApp API Support |
| HTTPS/SSL Issues | Check SSL certificate | Server Admin |

---

## ✅ GO-LIVE CHECKLIST

- [ ] All 7 test cases passed
- [ ] Logs reviewed, no errors
- [ ] Database integrity verified
- [ ] Backup created
- [ ] Monitoring setup complete
- [ ] Team trained & ready
- [ ] Documentation prepared
- [ ] Rollback plan tested
- [ ] Stakeholder sign-off obtained
- [ ] Launch approved

---

**Status**: Ready for Testing & Deployment

**Next Action**: Begin sandbox testing phase with all 7 test cases

Questions? Check documentation or logs in `storage/logs/laravel.log`
