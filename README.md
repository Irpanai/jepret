<div align="center">

# 📸 JEPRET

### Digital Photography Marketplace Platform

**Temukan foto. Simpan momen.**

JEPRET adalah platform marketplace fotografi digital yang menghubungkan **Photographer** dan **Buyer** dalam satu ekosistem terintegrasi untuk menemukan, mengelola, menjual, membeli, dan mengunduh karya fotografi secara aman.

[Website](https://jepret.web.id) · [Gallery](https://jepret.web.id/galeri) · [Photographers](https://jepret.web.id/photographers)

</div>

---

## Tentang JEPRET

JEPRET dirancang untuk menyederhanakan proses jual-beli foto event secara digital.

Photographer dapat mengunggah dan mengelola karya, mengatur harga, memantau transaksi, mengelola storage, serta melakukan penarikan pendapatan. Buyer dapat menjelajahi galeri, menemukan foto dari berbagai event, melakukan pembelian, dan mengakses kembali foto yang telah dibeli.

Alur utama:

```text
Buyer
→ Gallery
→ Cart
→ Checkout
→ Transaction
→ Photo
→ Photographer
→ Revenue Split
→ Withdrawal
→ Super Admin
```

---

## Fitur Utama

### Buyer

- Menjelajahi galeri fotografi publik
- Filter dan pencarian berdasarkan event, photographer, lokasi, kategori, tanggal, dan metadata terkait
- Detail foto dan photographer
- Shopping cart
- Checkout dan pembayaran
- Riwayat pembelian
- Akses ulang foto yang telah dibeli
- Download file pembelian dengan otorisasi
- Profile dan autentikasi pengguna

### Photographer

- Dashboard / Ringkasan
- Statistik penjualan dan performa
- Pengelolaan kamera
- Pengelolaan event
- Multi-upload foto
- Pengaturan metadata dan harga
- Pengelolaan status publikasi
- Personal watermark
- System watermark untuk preview marketplace
- Pengelolaan storage
- Orders & Transactions
- Export CSV
- Profile & Portfolio
- Withdrawal / pencairan saldo
- Notifikasi transaksi terbaru

### Super Admin

- Command Center
- Photographer verification
- Photographer management
- Transaction ledger
- Revenue monitoring
- Withdrawal management
- Storage monitoring dan quota override
- Platform settings
- Watermark settings
- Audit log
- Executive PDF report
- CSV export
- Monitoring relasi Buyer, Photographer, Transaction, dan Withdrawal

---

## Revenue Split

JEPRET menggunakan pembagian pendapatan tetap:

| Pihak | Persentase |
|---|---:|
| Photographer | **90%** |
| Platform JEPRET | **10%** |

Nilai pembagian disimpan pada snapshot transaksi agar transaksi lama tidak berubah saat harga foto diperbarui.

---

## Sistem Watermark

JEPRET menggunakan tiga lapisan aset foto:

```text
1. Raw Original
   → private
   → tidak dapat diakses publik

2. Marketplace Preview
   → menggunakan System Watermark
   → digunakan untuk preview sebelum pembelian

3. Purchased Variant
   → menggunakan Photographer Watermark
   → hanya dapat diakses Buyer dengan transaksi paid
```

Tujuannya adalah menjaga karya Photographer tetap terlindungi tanpa mengganggu pengalaman Buyer.

---

## Storage & Package

| Paket | Harga | Storage |
|---|---:|---:|
| Trial | Gratis / 7 hari | 500 MB |
| Starter | Rp29.000 / bulan | 5 GB |
| Creator | Rp59.000 / bulan | 20 GB |
| Studio | Custom | Custom |

Super Admin dapat memberikan override quota apabila diperlukan.

---

## Withdrawal Flow

```text
Photographer
→ Request Withdrawal
→ Pending
→ Super Admin Review
→ Held / Success / Rejected
```

Withdrawal terhubung langsung dengan saldo Photographer dan diproses dengan proteksi agar perubahan saldo tidak dieksekusi lebih dari satu kali.

---

## Tech Stack

| Kategori | Teknologi |
|---|---|
| Backend | Laravel 13 |
| Language | PHP 8.3+ |
| Frontend | Blade |
| Styling | Tailwind CSS |
| Interactivity | Alpine.js |
| Build Tool | Vite |
| Database | MySQL / PostgreSQL |
| Image Processing | Intervention Image |
| Payment Integration | Midtrans |
| PDF Reporting | Laravel DomPDF |
| Package Manager | Composer & NPM |

---

## Role & Access

### Public Visitor

Dapat mengakses area publik seperti:

```text
/
 /galeri
 /photographers
 /photographers/{slug}
 /p/{photo}
 /about
 /pricing
 /terms-of-service
 /privacy-policy
```

### Buyer

Buyer memiliki akses ke:

```text
Cart
Checkout
Payment
Purchase History
Purchased Photo Download
Profile
```

### Photographer

Photographer memiliki workspace untuk:

```text
Dashboard
Cameras
Events
Photos
Orders
Storage
Portfolio
Withdrawal
Account Settings
```

### Super Admin

Super Admin mengelola:

```text
Platform Dashboard
Photographers
Compliance
Ledger
Withdrawals
Storage
Platform Settings
Reports
```

---

## Instalasi Lokal

### 1. Clone Repository

```bash
git clone https://github.com/Irpanai/jepret.git
cd jepret
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Atur konfigurasi database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jepret
DB_USERNAME=root
DB_PASSWORD=
```

Atur juga konfigurasi lain sesuai environment:

```text
APP_URL
MAIL_*
FILESYSTEM_DISK
SESSION_DRIVER
QUEUE_CONNECTION
MIDTRANS_*
```

---

### 4. Database

Untuk development baru:

```bash
php artisan migrate --seed
```

Untuk environment yang sudah memiliki data:

```bash
php artisan migrate
```

Jangan menggunakan `migrate:fresh` pada database production.

---

### 5. Storage Link

```bash
php artisan storage:link
```

Storage public hanya digunakan untuk aset yang memang boleh diakses publik.

Raw original photo tetap berada pada storage private/protected sesuai implementasi aplikasi.

---

### 6. Jalankan Development Server

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Akses aplikasi di:

```text
http://localhost:8000
```

---

## Production Build

Build frontend:

```bash
npm run build
```

Optimasi Laravel:

```bash
php artisan optimize
```

Migrasi production:

```bash
php artisan migrate --force
```

Pastikan konfigurasi production:

```env
APP_ENV=production
APP_DEBUG=false
```

---

## Seeder & Development Data

Project menyediakan seed data untuk kebutuhan development dan QA, termasuk:

- Buyer
- Photographer
- Super Admin
- Packages
- Events
- Photos
- Transactions
- Withdrawals
- Platform settings
- Relational demo data

Data seed hanya ditujukan untuk environment development/testing.

Jangan menggunakan kredensial demo sebagai kredensial production.

---

## Testing & QA

Jalankan test:

```bash
php artisan test
```

Format code:

```bash
vendor/bin/pint
```

Build frontend:

```bash
npm run build
```

Audit route:

```bash
php artisan route:list --except-vendor
```

Periksa diff:

```bash
git diff --check
```

Viewport QA yang direkomendasikan:

```text
Mobile:
375×812
390×844
430×932

Tablet:
768×1024

Desktop:
1366×768
1440×900
1920×1080
```

---

## Security Principles

- Raw original photo tidak memiliki public URL langsung
- Buyer hanya dapat mengunduh foto yang dimiliki melalui transaksi paid
- Photographer hanya dapat mengubah resource miliknya sendiri
- Super Admin routes dilindungi role authorization
- Mutation menggunakan HTTP method yang sesuai
- Form dilindungi CSRF
- Financial mutation menggunakan database transaction dan locking bila diperlukan
- Revenue menggunakan transaction snapshot
- Withdrawal diproses secara idempotent
- File path dan download authorization diverifikasi server-side

---

## SEO & Performance

Public pages mendukung optimasi dasar seperti:

- Dynamic page title
- Meta description
- Canonical URL
- Open Graph
- Twitter Card
- Sitemap
- Structured Data
- Lazy loading untuk image non-critical
- Eager loading Eloquent untuk mencegah N+1 query
- Pagination untuk dataset besar
- Responsive image layout
- Mobile-first responsive UI

Private pages seperti Checkout, Purchases, Photographer Dashboard, dan Super Admin tidak ditujukan untuk indexing search engine.

---

## Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   └── Middleware/
├── Models/
├── Services/
└── ...

database/
├── migrations/
├── factories/
└── seeders/

resources/
├── views/
│   ├── fotografer/
│   ├── superadmin/
│   ├── purchases/
│   ├── components/
│   └── ...
├── css/
└── js/

routes/
└── web.php

tests/
```

---

## Main User Flow

### Buyer

```text
Landing
→ Gallery
→ Photo Detail
→ Cart
→ Checkout
→ Payment
→ Purchase Success
→ Purchases
→ Download
```

### Photographer

```text
Login
→ Complete Profile
→ Package
→ Dashboard
→ Camera
→ Event
→ Upload Photo
→ Watermark Processing
→ Metadata & Price
→ Publish
→ Marketplace
→ Transaction
→ Balance
→ Withdrawal
```

### Super Admin

```text
Login
→ Command Center
→ Photographer Verification
→ Transaction Ledger
→ Withdrawal Review
→ Storage Management
→ Platform Settings
→ Reports
```

---

## Project Goals

JEPRET dibangun untuk membuat proses fotografi event lebih sederhana bagi kedua pihak.

**Photographer**

```text
Upload → Publish → Sell → Manage → Withdraw
```

**Buyer**

```text
Discover → Purchase → Download → Keep
```

Dengan pendekatan tersebut, JEPRET menjadi satu platform terintegrasi untuk mendukung distribusi, perlindungan, dan monetisasi karya fotografi digital.

---

<div align="center">

### JEPRET — Find Your Moment.

**Digital Photography Marketplace Platform**

Built with Laravel.

</div>
