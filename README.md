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

## Akun Demo / Kredensial

Untuk kebutuhan development dan pengujian end-to-end, project menyediakan beberapa akun demo.

Semua akun demo di bawah menggunakan password:

```text
password
```

| Role | Email | Deskripsi |
|---|---|---|
| 👑 **Super Admin** | `superadmin@jepret.test` | Akses Command Center, Photographer Management, Ledger, Withdrawal, Storage, Platform Settings, dan laporan. |
| 📸 **Photographer Pro** | `fotografer_pro@jepret.test` | Akun Photographer dengan aktivitas, transaksi, portfolio, dan data operasional yang lebih lengkap. |
| 📸 **Photographer Basic** | `fotografer_basic@jepret.test` | Akun Photographer untuk menguji package, quota storage, upload, dan flow dasar Photographer. |
| 🛍️ **Buyer VIP** | `pembeli_vip@jepret.test` | Akun Buyer dengan riwayat pembelian dan data transaksi untuk kebutuhan QA. |
| 🛍️ **Buyer Biasa** | `pembeli2@jepret.test` | Akun Buyer standar untuk menguji Gallery, Cart, Checkout, Payment, Purchases, dan Download. |

> **Catatan:** Akun ini hanya untuk development/testing. Jangan gunakan password atau kredensial demo pada environment production.

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

## 📖 Panduan Penggunaan (User Flow)

Berikut adalah skenario pengujian end-to-end untuk mencoba alur utama JEPRET secara terpadu dari sisi **Buyer**, **Photographer**, hingga **Super Admin**.

### Tahap 1: Pengalaman Pembeli (Buyer)

1. Buka browser lalu **Login** menggunakan akun:

   ```text
   pembeli2@jepret.test
   Password: password
   ```

2. Masuk ke **Gallery** dan cari foto berdasarkan event, photographer, lokasi, kategori, tanggal, atau filter lain yang tersedia.

3. Klik salah satu foto untuk membuka halaman detail.

4. Tambahkan foto ke **Cart**, lalu lanjutkan ke **Checkout**.

5. Buat order pembayaran dan selesaikan flow pembayaran yang tersedia pada environment development.

6. Setelah transaksi berstatus **Paid**, buka menu **Pembelian Saya**.

7. Buka detail order untuk melihat seluruh foto yang telah dibeli.

8. Klik **Download File Pembelian** untuk mengunduh versi foto yang sudah menjadi hak Buyer.

   Sesuai flow watermark JEPRET:

   ```text
   Sebelum pembelian
   → Marketplace Preview
   → System Watermark

   Setelah transaksi Paid
   → Purchased Variant
   → Photographer Watermark
   ```

9. Coba download ulang foto melalui **Pembelian Saya** untuk memastikan akses tetap tersedia hanya untuk Buyer pemilik transaksi.

---

### Tahap 2: Pengalaman Photographer

1. Buka browser lain atau jendela **Incognito**, lalu Login menggunakan:

   ```text
   fotografer_pro@jepret.test
   Password: password
   ```

2. Buka halaman **Ringkasan / Dashboard Photographer**.

3. Periksa statistik utama seperti:

   ```text
   Pendapatan Bersih
   Foto Terjual
   Total Kunjungan
   Rasio Konversi Beli
   Saldo Siap Tarik
   ```

4. Transaksi Buyer yang sudah berstatus **Paid** harus muncul pada:

   ```text
   Ringkasan
   → Penjualan Terkini

   Pesanan & Transaksi
   → Orders
   ```

5. Pastikan pendapatan Photographer bertambah berdasarkan pembagian tetap:

   ```text
   Photographer = 90%
   Platform = 10%
   ```

6. Buka menu **Kamera** untuk melihat atau mengelola data kamera.

7. Buka menu **Foto / Event** untuk menguji flow:

   ```text
   Event
   → Upload Photo
   → Metadata
   → Price
   → Watermark Processing
   → Publish
   ```

8. Periksa **Penggunaan Storage** untuk memastikan pemakaian storage dan quota aktif terbaca dengan benar.

9. Dari halaman **Ringkasan**, gunakan form **Withdrawal / Tarik Saldo**.

10. Masukkan nominal penarikan serta tujuan Bank atau E-Wallet.

11. Setelah dikirim, status withdrawal akan menjadi:

   ```text
   Pending
   ```

12. Permintaan tersebut harus langsung tersedia pada halaman Withdrawal milik Super Admin.

---

### Tahap 3: Pengalaman Super Admin

1. Login menggunakan:

   ```text
   superadmin@jepret.test
   Password: password
   ```

2. Buka **Command Center** untuk melihat ringkasan platform.

3. Buka **Photographer Compliance / Photographer Management** untuk:

   - melihat Photographer
   - melakukan approval atau rejection
   - memeriksa informasi terkait akun Photographer

4. Buka **Transaction Ledger**.

5. Pastikan transaksi Buyer sebelumnya muncul pada ledger yang sama dengan transaksi yang terlihat oleh Photographer.

6. Periksa nilai finansial transaksi:

   ```text
   GMV
   Photographer Share = 90%
   Platform Share = 10%
   ```

7. Buka **Withdrawals**.

8. Cari permintaan withdrawal dari `fotografer_pro@jepret.test`.

9. Super Admin dapat memproses withdrawal sesuai status yang tersedia:

   ```text
   Pending
   → Held
   → Success

   atau

   Pending / Held
   → Rejected
   ```

10. Untuk menyelesaikan skenario utama, ubah withdrawal menjadi **Success**.

11. Kembali ke Dashboard Photographer dan pastikan status withdrawal sudah ikut berubah menjadi **Success**.

---

### Tahap 4: Verifikasi Integrasi End-to-End

Setelah tiga role diuji, pastikan seluruh data saling terhubung:

```text
Buyer membeli Photo
        ↓
Transaction dibuat
        ↓
Transaction menjadi Paid
        ↓
Buyer mendapat akses file pembelian
        ↓
Photographer melihat penjualan
        ↓
Photographer menerima 90% revenue
        ↓
Super Admin melihat Transaction yang sama
        ↓
Photographer melakukan Withdrawal
        ↓
Super Admin memproses Withdrawal
        ↓
Status kembali tampil pada Photographer
```

Hal-hal penting yang perlu diverifikasi:

- Buyer hanya dapat mengakses pembeliannya sendiri
- Photographer hanya dapat mengelola resource miliknya sendiri
- transaksi yang terlihat Buyer, Photographer, dan Super Admin berasal dari data yang sama
- revenue tetap **90% Photographer / 10% Platform**
- raw original tidak memiliki akses publik langsung
- marketplace preview menggunakan **System Watermark**
- purchased variant menggunakan **Photographer Watermark**
- storage quota Photographer tetap diterapkan
- withdrawal tidak dapat diproses dua kali
- status transaksi dan withdrawal konsisten pada seluruh role

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
