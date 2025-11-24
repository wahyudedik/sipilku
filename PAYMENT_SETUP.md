# Setup Payment Integration (Midtrans)

## Konfigurasi

### 1. Install Package
Package `midtrans/midtrans-php` sudah terinstall.

### 2. Setup Environment Variables
Tambahkan ke file `.env`:

```env
# Midtrans Configuration
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

**Untuk Development:**
- Dapatkan credentials dari [Midtrans Sandbox](https://dashboard.sandbox.midtrans.com/)
- Set `MIDTRANS_IS_PRODUCTION=false`

**Untuk Production:**
- Dapatkan credentials dari [Midtrans Dashboard](https://dashboard.midtrans.com/)
- Set `MIDTRANS_IS_PRODUCTION=true`

### 3. Setup Webhook URL
Di Midtrans Dashboard, set webhook URL:
```
https://yourdomain.com/payments/callback
```

## Payment Methods

### 1. Balance (Saldo Akun)
- Instant payment dari saldo user
- Order langsung completed
- Download token langsung tersedia

### 2. Midtrans (Online Payment)
- Payment gateway integration
- Support: Credit Card, Debit Card, E-Wallet, Virtual Account
- Real-time payment status via webhook
- Auto-complete order setelah payment success

### 3. Manual Transfer
- Bank transfer manual
- Admin perlu konfirmasi pembayaran
- Order status: pending → completed (setelah admin confirm)

## Payment Flow

### Midtrans Payment Flow:
1. User pilih "Pembayaran Online (Midtrans)" di checkout
2. Order dibuat dengan status `pending`, payment_method `midtrans`
3. User di-redirect ke `/payments/process/{order}`
4. PaymentController generate Snap token
5. Snap.js popup muncul untuk payment
6. User complete payment di Midtrans
7. Midtrans mengirim webhook ke `/payments/callback`
8. PaymentController process callback:
   - Update transaction status
   - Update order status ke `completed`
   - Generate download token
   - Transfer balance ke seller
   - Send notifications
9. User di-redirect ke `/payments/status/{order}`

### Manual Transfer Flow:
1. User pilih "Transfer Manual" di checkout
2. Order dibuat dengan status `pending`, payment_method `manual`
3. User melihat instruksi transfer di order detail
4. User transfer ke rekening yang ditentukan
5. Admin melihat order di `/admin/orders`
6. Admin konfirmasi pembayaran
7. Order status update ke `completed`
8. Download token generated
9. User dapat download file

## Payment History

User dapat melihat riwayat pembayaran di `/payments/history` dengan filter:
- Type: purchase, deposit, withdrawal, commission, refund, payout
- Status: pending, processing, completed, failed, cancelled
- Payment Method: balance, midtrans, manual, bank_transfer

## Testing

### Sandbox Testing:
1. Gunakan test credentials dari Midtrans Sandbox
2. Test dengan card number: `4811 1111 1111 1114` (Visa)
3. CVV: `123`
4. Expiry: Any future date
5. 3DS: `112233` (OTP)

### Production:
1. Update credentials ke production
2. Set `MIDTRANS_IS_PRODUCTION=true`
3. Test dengan real payment methods

## Troubleshooting

### Payment callback tidak terpanggil:
1. Pastikan webhook URL sudah di-set di Midtrans Dashboard
2. Pastikan server dapat diakses dari internet (untuk production)
3. Check logs: `storage/logs/laravel.log`

### Snap token tidak muncul:
1. Check Midtrans credentials di `.env`
2. Check browser console untuk error
3. Pastikan Snap.js script ter-load

### Payment gagal:
1. Check transaction status di Midtrans Dashboard
2. Check order status di database
3. Review callback logs

## Security Notes

1. **Webhook Verification**: Midtrans akan mengirim signature untuk verifikasi (implementasi dapat ditambahkan)
2. **CSRF Protection**: Payment callback route tidak menggunakan CSRF (normal untuk webhook)
3. **Order Verification**: Setiap payment callback verify order ownership
4. **Transaction Logging**: Semua payment activity di-log untuk audit

