# 🏗️ Sipilku - Platform All-in-One untuk Dunia Teknik Sipil

**Sipilku** adalah platform marketplace komprehensif yang menggabungkan konsep Envato Marketplace dengan layanan profesional dan tools perhitungan teknik sipil. Platform ini dirancang untuk menjadi pusat solusi digital bagi para profesional, mahasiswa, dan praktisi teknik sipil.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Pengembangan](#-pengembangan)
- [Struktur Proyek](#-struktur-proyek)
- [Kontribusi](#-kontribusi)

---

## ✨ Fitur Utama

### 🧩 1. Fitur User (Frontend)

#### 🔹 A. Marketplace Produk Digital
- File perhitungan sipil (Excel/Spreadsheet)
- Template laporan & dokumen (PDF, Word)
- Gambar kerja (DWG, SketchUp, Revit)
- Tools perhitungan (PHP, JS, Excel, dll.)
- Ebook / modul teknik sipil
- Software kecil (kalkulator struktur, pondasi, RAB)
- Preview file sebelum pembelian
- Download setelah purchase

#### 🔹 B. Marketplace Jasa Sipil
- Jasa gambar kerja
- Jasa hitung struktur
- Jasa RAB (Rencana Anggaran Biaya)
- Jasa site manager
- Konsultasi teknik sipil
- Pengajuan penawaran custom
- Sistem negosiasi (bidding)
- Chat real-time antara klien dan vendor
- Review & rating sistem

#### 🔹 C. Marketplace Toko Bangunan & Pabrik/UMKM
- **Toko Bangunan:**
  - Direktori toko bangunan terverifikasi
  - Katalog produk material toko (harga, stok, spesifikasi)
  - Pencarian toko berdasarkan lokasi
  - Rekomendasi toko terdekat untuk kontraktor
  - Perbandingan harga antar toko
  - Request penawaran material dari multiple toko
  - Tracking pengiriman material

- **Pabrik/UMKM Konstruksi:**
  - Direktori berbagai pabrik & UMKM terverifikasi
  - **Pabrik Beton:** Ready mix berbagai grade (K-100, K-125, K-150, K-175, K-200, dll), precast, mobil molen pricing
  - **Pabrik Bata (UMKM):** Bata merah, bata putih, bata press - berbagai ukuran & kualitas
  - **Pabrik Genting (UMKM):** Genting tanah liat, genting beton, metal roof - berbagai ukuran
  - **Pabrik Baja:** IWF, H-Beam, UNP, berbagai ukuran & spesifikasi
  - **Pabrik Precast:** Panel, kolom, balok precast
  - **Pabrik Keramik/Granit:** Keramik lantai, dinding, granit - berbagai ukuran & motif
  - **Pabrik Kayu:** Balok, papan, triplek - berbagai jenis kayu & grade
  - **UMKM Lainnya:** Berbagai produk konstruksi lainnya
  
- **Fitur Pabrik/UMKM:**
  - Pencarian pabrik berdasarkan jenis & lokasi proyek
  - **Rekomendasi pabrik terdekat** - Menghindari biaya pengiriman yang mahal
  - **Kalkulasi ongkos kirim** berdasarkan jarak (km) dari pabrik ke lokasi proyek
  - **Perbandingan harga & kualitas** antar pabrik (same product type)
  - Request penawaran dari multiple pabrik (compare price, quality, delivery cost)
  - Kalkulator kebutuhan material (volume/quantity) dan estimasi biaya
  - Scheduling pengiriman
  - Tracking pengiriman
  - Quality rating & review system

#### 🔹 D. Sistem Akun User
- Register & Login (Email, Google OAuth)
- Role management (Buyer, Seller, Contractor, Store Owner, Admin)
- Dashboard user personal
- Orders management
- Downloads history
- Request jasa custom
- Chat/messages center

---

### 🧩 2. Fitur Seller / Contributor

#### 🔹 Upload Produk
- Upload file dengan berbagai format
- Harga custom per produk
- Sistem diskon & promo
- Preview image/video
- Kategori produk
- Komisi otomatis

#### 🔹 Jasa Profesional
- Buat listing jasa
- Tentukan harga paket
- Form penawaran custom
- Chat dengan klien
- Status pengerjaan tracking

#### 🔹 Payout System
- Request withdraw (Bank, e-wallet)
- Riwayat komisi
- Laporan pendapatan detail

---

### 🧩 3. Fitur Kontraktor

#### 🔹 Material Procurement
- Request material dari multiple toko
- Bandingkan penawaran harga
- Pilih toko terdekat berdasarkan lokasi proyek
- Track pengiriman material
- History pembelian material
- Integrasi dengan RAB Calculator

#### 🔹 Factory/UMKM Procurement
- Request produk dari multiple pabrik/UMKM (beton, bata, genting, baja, precast, keramik, kayu, dll)
- **Rekomendasi pabrik terdekat** berdasarkan jenis produk & lokasi proyek (minimalkan ongkos kirim)
- Bandingkan harga total (harga produk + ongkos kirim)
- **Perbandingan kualitas** produk antar pabrik (rating, sertifikat, review)
- Kalkulator kebutuhan material (volume/quantity: m3, m2, pcs, kg, dll)
- Pilih produk sesuai spesifikasi (grade, ukuran, kualitas)
- Schedule pengiriman sesuai timeline proyek
- Track pengiriman (location tracking)
- History pembelian dari berbagai pabrik/UMKM
- Integrasi dengan RAB Calculator (harga produk + ongkos kirim otomatis)

**Contoh spesifik:**
- **Beton:** Pilih grade (K-100, K-125, K-150, K-175, K-200), hitung volume (m3), bandingkan harga + ongkos mobil molen
- **Bata:** Pilih jenis & kualitas, hitung kebutuhan (pcs/kubik), bandingkan harga antar UMKM
- **Genting:** Pilih jenis & ukuran, hitung kebutuhan (m2/pcs), perbandingan harga & kualitas
- **Baja:** Pilih jenis & ukuran, hitung kebutuhan (kg/ton), perbandingan harga & spesifikasi

#### 🔹 Store Recommendations
- Rekomendasi toko berdasarkan jarak
- Filter toko berdasarkan rating & harga
- Lihat katalog produk semua toko
- Perbandingan harga material

---

### 🧩 4. Fitur Toko Bangunan / Store Owner

#### 🔹 Store Management
- Registrasi toko dengan verifikasi admin
- Upload profil toko (foto, alamat, kontak)
- Atur lokasi toko (koordinat GPS)
- Upload dokumen legalitas (SIUP, NPWP)

#### 🔹 Product Catalog
- Kelola katalog produk material
- Update harga & stok real-time
- Import produk bulk (Excel/CSV)
- Kategori produk material
- Foto & spesifikasi produk

#### 🔹 Order Management
- Terima request material dari kontraktor
- Kirim penawaran harga
- Kelola order (pending, processing, completed)
- Tracking pengiriman
- Invoice & pembayaran

#### 🔹 Store Analytics
- Dashboard penjualan
- Analytics produk populer
- Laporan pendapatan
- Statistik order

---

### 🧩 5. Fitur Pabrik/UMKM Owner

#### 🔹 Factory Management
- Registrasi pabrik/UMKM dengan verifikasi admin
- Pilih jenis pabrik (beton, bata, genting, baja, precast, keramik, kayu, UMKM lainnya)
- Upload profil pabrik (foto, alamat, kontak)
- Atur lokasi pabrik (koordinat GPS)
- Upload dokumen legalitas (Izin operasional, NPWP, sertifikat kualitas, dll)
- Status verifikasi (UMKM/Industri)

#### 🔹 Product Catalog Management
- Kelola katalog produk sesuai jenis pabrik
- Update harga produk (flexible unit: m3, m2, pcs, kg, ton, dll)
- Set harga ongkos kirim (per km atau flat rate)
- Kelola spesifikasi produk (grade, ukuran, kualitas, dll)
- Ketersediaan produk & stock management
- Operating hours & production capacity
- Foto & spesifikasi produk detail

**Contoh per jenis pabrik:**
- **Pabrik Beton:** Ready mix grade (K-100, K-125, dll), harga per m3, mobil molen pricing
- **Pabrik Bata:** Bata merah/putih/press, kualitas A/B/C, harga per pcs/kubik
- **Pabrik Genting:** Genting tanah liat/beton, ukuran, harga per m2/pcs
- **Pabrik Baja:** IWF/H-Beam/UNP, ukuran & spesifikasi, harga per kg/ton

#### 🔹 Order Management
- Terima request produk dari kontraktor
- Kirim penawaran harga (produk + ongkos kirim)
- Kalkulasi ongkos kirim otomatis berdasarkan jarak
- Kelola order (pending, processing, in delivery, completed)
- Schedule pengiriman produk
- Tracking pengiriman
- Invoice & pembayaran

#### 🔹 Factory Analytics
- Dashboard order & penjualan
- Analytics produk populer (per jenis)
- Laporan pendapatan
- Statistik order & delivery
- Track ongkos kirim vs revenue
- Quality rating & review management

---

### 🧩 6. Fitur Admin

- Manage user & permissions
- Approve sellers
- Approve toko bangunan & pabrik/UMKM (beton, bata, genting, baja, precast, keramik, kayu, dll)
- Approve produk & jasa
- Manage factory types & categories
- Komisi marketplace management
- Manajemen pembayaran
- Kategori marketplace
- Landing page builder
- Sistem kupon & promo
- Notifikasi email otomatis
- Laporan transaksi & statistik
- Manajemen store verification
- Manajemen factory/UMKM verification
- Quality certification management untuk pabrik

---

### 🧩 7. Tools Teknik Sipil (Built-in)

- **Kalkulator RAB (Rencana Anggaran Biaya)**
  - Integrasi dengan data harga toko bangunan
  - **Integrasi dengan data harga semua pabrik/UMKM** (beton, bata, genting, baja, precast, keramik, kayu, dll)
  - Update harga material otomatis dari toko
  - Update harga produk pabrik otomatis dari pabrik terdekat (semua jenis)
  - Kalkulasi ongkos kirim berdasarkan jarak proyek (semua produk)
  - Perbandingan harga material antar toko
  - Perbandingan harga produk antar pabrik (termasuk ongkos kirim & kualitas)
  - Rekomendasi toko & pabrik terdekat untuk optimasi biaya (all factory types)
  - Total cost breakdown (material + produk pabrik + ongkos kirim)
  - Quality comparison dalam perhitungan biaya
  
- **Kalkulator Volume Material**
  - Integrasi dengan data harga toko untuk estimasi biaya
  
- **Kalkulator Kebutuhan Material Pabrik**
  - **Kalkulator Volume Beton (m3):** Hitung kebutuhan beton, estimasi biaya + ongkos kirim, rekomendasi pabrik terdekat
  - **Kalkulator Kebutuhan Bata:** Hitung kebutuhan bata (pcs/kubik), estimasi biaya, perbandingan harga & kualitas UMKM
  - **Kalkulator Kebutuhan Genting:** Hitung kebutuhan genting (m2/pcs), estimasi biaya, perbandingan antar UMKM
  - **Kalkulator Kebutuhan Baja:** Hitung kebutuhan baja (kg/ton), estimasi biaya, perbandingan spesifikasi & harga
  - Dan kalkulator untuk produk pabrik lainnya
  
- Kalkulator struktur sederhana
- Kalkulator pondasi
- Estimasi waktu proyek
- Overhead & profit calculator

---

### 🧩 8. Sistem Pembayaran

- Midtrans / Xendit integration
- Bank transfer manual
- Saldo internal platform
- Payment untuk pembelian material dari toko

---

## 🛠️ Teknologi yang Digunakan

### Backend
- **Laravel 12** - PHP Framework
- **MySQL / MariaDB** - Database
- **Laravel Cashier** - Payment processing
- **Spatie Roles & Permissions** - Role management

### Frontend
- **Blade Templates** - Server-side templating
- **Alpine.js** - Lightweight JavaScript framework
- **TailwindCSS 4** - Utility-first CSS framework
- **Vite** - Build tool & dev server
- **Livewire** (Opsional) - Full-stack framework
- **Google Maps API / Mapbox** - Geolocation & maps untuk toko

### Development Tools
- **Pest** - Testing framework
- **Laravel Pint** - Code style fixer
- **Laravel Sail** - Docker development environment

---

## 📦 Persyaratan Sistem

- PHP >= 8.2
- Composer
- Node.js >= 18.x & NPM
- MySQL >= 8.0 atau MariaDB >= 10.3
- Web server (Apache/Nginx) atau PHP built-in server

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd sipilku.com
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sipilku
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migrasi

```bash
php artisan migrate
```

### 6. Build Assets

```bash
npm run build
```

### 7. Jalankan Development Server

```bash
# Menggunakan setup script (recommended)
composer run dev

# Atau manual
php artisan serve
npm run dev
```

Akses aplikasi di: `http://localhost:8000`

---

## 💻 Pengembangan

### Scripts yang Tersedia

```bash
# Setup awal proyek
composer run setup

# Development mode (server + queue + vite)
composer run dev

# Run tests
composer run test
# atau
php artisan test
```

### Code Style

```bash
# Format code dengan Laravel Pint
./vendor/bin/pint
```

### Database

```bash
# Create migration
php artisan make:migration create_example_table

# Create model dengan migration
php artisan make:model Example -m

# Run migrations
php artisan migrate

# Rollback migration
php artisan migrate:rollback
```

---

## 📁 Struktur Proyek

```
sipilku.com/
├── app/
│   ├── Http/
│   │   └── Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   └── Providers/           # Service providers
├── bootstrap/               # Bootstrap files
├── config/                  # Configuration files
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── public/                  # Public assets
├── resources/
│   ├── css/                 # CSS files
│   ├── js/                  # JavaScript files
│   └── views/               # Blade templates
├── routes/                  # Route definitions
├── storage/                 # Storage files
├── tests/                   # Test files
└── vendor/                  # Composer dependencies
```

---

## 🗺️ Alur Sistem Sipilku

Platform Sipilku menghubungkan berbagai stakeholder dalam ekosistem konstruksi:

1. **BUYER** → Request jasa/produk → **CONTRACTOR**
2. **CONTRACTOR** → Request material → **TOKO BANGUNAN**
   - Sistem mencari toko terdekat dari lokasi proyek
   - Bandingkan harga material antar toko
3. **CONTRACTOR** → Order produk konstruksi → **PABRIK/UMKM**
   - Sistem mencari **pabrik/UMKM terdekat** dari lokasi proyek (beton, bata, genting, baja, precast, keramik, kayu, dll)
   - Kalkulasi ongkos kirim berdasarkan jarak
   - **Hindari ongkos kirim mahal** dengan pabrik terdekat
   - Bandingkan harga total & kualitas produk (harga + ongkos kirim + quality rating)
   - Perbandingan spesifik per jenis produk
4. **CONTRACTOR** → Offer jasa → **SELLER** (jika perlu)
5. Sistem rekomendasi otomatis berdasarkan lokasi & kebutuhan
   - Rekomendasi toko terdekat untuk material
   - **Rekomendasi pabrik/UMKM terdekat** untuk berbagai produk (beton, bata, genting, baja, dll) - optimasi ongkos kirim & kualitas
   - Total cost optimization (material + produk pabrik + delivery)
   - Quality-based recommendations (best price-quality-location combination)

**Data dari toko bangunan & pabrik/UMKM dimanfaatkan untuk:**

**Toko Bangunan:**
- Rekomendasi toko terdekat untuk kontraktor
- Update harga material di RAB Calculator
- Perbandingan harga material
- Estimasi biaya material yang akurat

**Pabrik/UMKM (Beton, Bata, Genting, Baja, Precast, Keramik, Kayu, dll):**
- **Rekomendasi pabrik terdekat** berdasarkan jenis produk & lokasi proyek
- **Minimalkan ongkos kirim** dengan pilihan pabrik terdekat (semua jenis)
- Update harga produk pabrik di RAB Calculator (termasuk ongkos kirim)
- **Perbandingan harga & kualitas** produk antar pabrik (same product type)
- Estimasi biaya produk yang akurat (termasuk delivery cost)
- Kalkulasi ongkos kirim otomatis berdasarkan jarak pabrik ke lokasi proyek
- Integrasi dengan tools kalkulator (volume beton, kebutuhan bata, genting, baja, dll)
- Quality rating & review untuk membantu keputusan pembelian

---

## 📝 Tasklist Development

Lihat file [TASKLIST.md](./TASKLIST.md) untuk daftar lengkap task development yang perlu dikerjakan.

---

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📄 License

Proyek ini menggunakan lisensi MIT. Lihat file [LICENSE](LICENSE) untuk detail lebih lanjut.

---

## 📞 Kontak & Support

Untuk pertanyaan atau dukungan, silakan buat issue di repository ini.

---

**Dibuat dengan ❤️ untuk komunitas teknik sipil Indonesia**
