# Setup Laravel Reverb untuk Real-time Chat

## Masalah: Port 8080 sudah digunakan

Jika Anda mendapatkan error `Failed to listen on "tcp://0.0.0.0:8080"`, berarti port 8080 sudah digunakan oleh aplikasi lain.

## Solusi 1: Gunakan Port Lain (Recommended)

### Langkah 1: Update `.env`
Tambahkan atau update konfigurasi Reverb di file `.env`:

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8081
REVERB_SCHEME=http
```

**Catatan:** Ganti port dari `8080` ke `8081` atau port lain yang tersedia (misalnya: 8082, 8443, 9001).

### Langkah 2: Update Vite Environment Variables
Tambahkan di `.env` untuk frontend:

```env
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Langkah 3: Generate Reverb Keys
Jalankan command untuk generate keys:

```bash
php artisan reverb:install
```

Atau generate secara manual:

```bash
php artisan reverb:key
```

### Langkah 4: Start Reverb Server
Jalankan Reverb server dengan port baru:

```bash
php artisan reverb:start --port=8081
```

Atau jika sudah set di `.env`, cukup:

```bash
php artisan reverb:start
```

## Solusi 2: Gunakan Log Driver untuk Development (Tanpa Real-time)

Jika Anda tidak memerlukan real-time chat untuk saat ini, gunakan log driver:

### Update `.env`:
```env
BROADCAST_CONNECTION=log
```

Chat akan tetap berfungsi, tetapi tidak real-time. Pesan baru akan muncul setelah refresh halaman.

## Solusi 3: Stop Proses yang Menggunakan Port 8080

Jika Anda ingin menggunakan port 8080:

1. Cari proses yang menggunakan port 8080:
   ```bash
   netstat -ano | findstr :8080
   ```

2. Stop proses tersebut (ganti PID dengan PID yang ditemukan):
   ```bash
   taskkill /PID 7320 /F
   ```

3. Kemudian jalankan Reverb:
   ```bash
   php artisan reverb:start
   ```

## Testing Real-time Chat

Setelah Reverb berjalan:

1. Buka 2 browser window/tab dengan user berbeda
2. Login sebagai user berbeda di masing-masing window
3. Kirim pesan dari satu window
4. Pesan harus muncul secara real-time di window lain tanpa refresh

## Troubleshooting

### Error: "Class Pusher\Pusher not found"
```bash
composer require pusher/pusher-php-server
```

### Error: "Failed to create broadcaster"
Pastikan `BROADCAST_CONNECTION` di `.env` sesuai dengan driver yang tersedia:
- `reverb` - untuk Laravel Reverb
- `log` - untuk development tanpa real-time
- `null` - untuk disable broadcasting

### Chat tidak real-time
1. Pastikan Reverb server berjalan
2. Pastikan Vite environment variables sudah di-set
3. Rebuild frontend assets: `npm run build` atau `npm run dev`
4. Clear browser cache

## Production Setup

Untuk production, pertimbangkan:
- Menggunakan Laravel Horizon untuk queue processing
- Setup SSL/TLS untuk secure WebSocket connection
- Gunakan load balancer dengan sticky sessions
- Atau gunakan service seperti Pusher.com atau Ably

