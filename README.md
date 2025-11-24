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

#### 🔹 C. Sistem Akun User
- Register & Login (Email, Google OAuth)
- Role management (Buyer, Seller, Admin)
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

### 🧩 3. Fitur Admin

- Manage user & permissions
- Approve sellers
- Approve produk & jasa
- Komisi marketplace management
- Manajemen pembayaran
- Kategori marketplace
- Landing page builder
- Sistem kupon & promo
- Notifikasi email otomatis
- Laporan transaksi & statistik

---

### 🧩 4. Tools Teknik Sipil (Built-in)

- Kalkulator RAB (Rencana Anggaran Biaya)
- Kalkulator volume material
- Kalkulator struktur sederhana
- Kalkulator pondasi
- Estimasi waktu proyek
- Overhead & profit calculator

---

### 🧩 5. Sistem Pembayaran

- Midtrans / Xendit integration
- Bank transfer manual
- Saldo internal platform

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
