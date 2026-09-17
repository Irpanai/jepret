<div align="center">
  <!-- <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?q=80&w=1000&auto=format&fit=crop" alt="Jepret Banner" width="100%" style="border-radius: 12px; max-height: 300px; object-fit: cover;"> -->
  
  <br />
  <br />

  <h1>📸 JEPRET</h1>
  <p>
    <strong>Marketplace Fotografi Event Cerdas & Terintegrasi</strong>
  </p>
  <p>
    Platform revolusioner yang mempertemukan Fotografer Event (Car Free Day, Marathon, Konser) dengan para Peserta melalui ekosistem transaksi digital yang aman, transparan, dan terotomatisasi.
  </p>

  <p>
    <a href="#fitur-unggulan">Fitur</a> •
    <a href="#persyaratan-sistem">Instalasi</a> •
    <a href="#akun-demo-kredensial">Akun Demo</a> •
    <a href="#panduan-penggunaan-user-flow">Panduan Penggunaan</a>
  </p>
</div>

---

## 🌟 Fitur Unggulan

JEPRET dirancang dengan fokus pada pengalaman pengguna (*User Experience*) yang premium dan kemudahan transaksi.

- 🔐 **Multi-Role Authentication** - Akses khusus untuk Super Admin dan Fotografer, sedangkan Pembeli masuk langsung ke galeri dan riwayat pembelian.
- 💰 **Sistem Bagi Hasil Otomatis (Ledger)** - Rekonsiliasi finansial transparan secara *real-time* (90% Hak Kreator, 10% Pendapatan Platform).
- 🖼️ **Master Asset Library** - Pembeli mendapatkan tautan unduh permanen ke foto resolusi tinggi (*Master File*) setelah pembayaran lunas.
- 🛡️ **Proteksi Watermark** - Foto *preview* yang diunggah otomatis dilindungi sehingga mencegah pencurian hak cipta sebelum transaksi selesai.
- 🔍 **Pencarian Cerdas AI (Mock)** - Pembeli dapat mencari foto mereka berdasarkan Nama Event, Nomor *Bib*, atau *Tag* Visual (misal: "sepeda", "baju merah").
- 💳 **Withdrawal Management** - Fotografer dapat menarik saldo pendapatan mereka yang kemudian diverifikasi dan disetujui oleh Super Admin.

---

## 💻 Persyaratan Sistem & Tech Stack

Proyek ini dibangun menggunakan arsitektur modern untuk memastikan performa yang cepat dan stabil.

| Kategori | Teknologi yang Digunakan |
| :--- | :--- |
| **Backend Framework** | Laravel 11.x (PHP 8.2+) |
| **Frontend Styling** | Tailwind CSS (Utility-First CSS) |
| **Frontend Interactivity**| Alpine.js (Lightweight JavaScript) |
| **Database** | MySQL / PostgreSQL |
| **Package Manager** | Composer & NPM |

---

## ⚙️ Panduan Instalasi & Konfigurasi Lokal

Ikuti petunjuk langkah demi langkah di bawah ini untuk menjalankan aplikasi secara lokal di komputer Anda.

### 1. Persiapan Repositori
Clone repositori ke dalam direktori lokal Anda:
```bash
git clone https://github.com/username/jepret.git
cd jepret
```

### 2. Instalasi Dependensi
Instal seluruh *library* PHP dan dependensi JavaScript yang dibutuhkan:
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (Lingkungan)
Salin *template environment* bawaan dan sesuaikan dengan kredensial *database* lokal Anda:
```bash
cp .env.example .env
```
Buka file `.env` di teks editor, lalu cari dan ubah bagian berikut sesuai dengan koneksi MySQL/PostgreSQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jepret
DB_USERNAME=root
DB_PASSWORD=
```

### 4. *Generate Key* & Migrasi Data
Buat kunci keamanan aplikasi dan masukkan struktur *database* beserta data tiruan (*dummy*):
```bash
php artisan key:generate
php artisan migrate:fresh --seed
```
> **Catatan:** Perintah `--seed` di atas sudah otomatis memuat paket langganan, event, puluhan foto, dan ratusan riwayat transaksi tiruan agar aplikasi langsung siap digunakan.

### 5. *Symlink Storage* (⚠️ Sangat Penting)
Agar seluruh foto yang diunggah oleh Fotografer dapat diakses secara publik melalui *browser*, Anda **wajib** menjalankan perintah ini:
```bash
php artisan storage:link
```

### 6. Jalankan Server
Buka dua (*2*) terminal terpisah, dan jalankan perintah berikut secara bersamaan:

**Terminal 1 (Backend PHP):**
```bash
php artisan serve
```

**Terminal 2 (Frontend Assets Compiler):**
```bash
npm run dev
```

Aplikasi sekarang dapat diakses melalui browser di alamat: **[http://localhost:8000](http://localhost:8000)** 🎉

---

## 🔑 Akun Demo / Kredensial

Untuk mempermudah proses pengujian aplikasi (*testing*), kami telah menyediakan beberapa variasi akun. Seluruh sandi (*password*) untuk akun di bawah ini adalah: **`password`**

| Role (Peran) | Email Akun | Deskripsi Singkat |
| :--- | :--- | :--- |
| 👑 **Super Admin** | `superadmin@jepret.test` | Akses ke Sistem Root, Ledger Finansial, dan *Approval* pencairan dana (*Withdrawal*). |
| 📸 **Fotografer Pro** | `fotografer_pro@jepret.test` | Akun fotografer agensi (Paket Pro). Memiliki pendapatan tinggi dan banyak riwayat transaksi. |
| 📸 **Fotografer Basic** | `fotografer_basic@jepret.test` | Akun pemula (Paket Basic). Cocok untuk melihat limitasi paket. |
| 🛍️ **Pembeli VIP** | `pembeli_vip@jepret.test` | Kolektor foto yang sering memberikan *Tip* besar kepada fotografer. |
| 🛍️ **Pembeli Biasa** | `pembeli2@jepret.test` | Pengguna standar yang melakukan transaksi pembelian biasa. |

---

## 📖 Panduan Penggunaan (User Flow)

Berikut adalah skenario pengujian terbaik untuk mencoba keseluruhan fitur aplikasi secara terpadu *(End-to-End)*:

### Tahap 1: Pengalaman Pembeli (Buyer)
1. Buka browser dan **Login** menggunakan akun `pembeli2@jepret.test`.
2. Di halaman beranda (*Marketplace*), cari foto berdasarkan event atau gaya tertentu.
3. Klik foto yang diminati untuk masuk ke halaman detail, lalu klik tombol **Beli / Checkout**.
4. *(Transaksi disimulasikan lunas)*. Anda akan diarahkan ke menu **Library Foto Saya**.
5. Coba klik tombol **Download Master** untuk mengunduh versi foto tanpa *watermark*.

### Tahap 2: Pengalaman Fotografer (Kreator)
1. Buka jendela browser *Incognito* baru, lalu **Login** menggunakan akun `fotografer_pro@jepret.test` (kreator dari foto yang baru saja dibeli).
2. Perhatikan **Saldo Aktif** Anda di Dashboard. Saldo tersebut otomatis bertambah senilai **90%** dari harga foto (+Tip jika ada pembeli VIP).
3. Buka menu **Pesanan & Transaksi (Orders)** untuk melihat jejak rekam pembelian tadi.
4. Pergi ke menu **Keuangan (Earnings)**, lalu klik tombol **Tarik Saldo**. Masukkan nominal, dan klik kirim. Status penarikan Anda sekarang adalah *Menunggu (Pending)*.

### Tahap 3: Pengalaman Super Admin
1. Login menggunakan akun `superadmin@jepret.test`.
2. Buka menu **Ledger Transaksi** untuk melihat ringkasan volume uang masuk secara global (GMV, Pendapatan Platform 10%, Hak Fotografer 90%).
3. Buka menu **Penarikan Dana (Withdrawals)**.
4. Cari permintaan penarikan dari Fotografer tadi, lalu klik **Setujui (Approve)**.
5. (Selesai). Uang tersebut secara sistem sudah dianggap ditransfer, dan jika Anda kembali ke *dashboard* Fotografer, status penarikannya telah berubah menjadi **Berhasil (Success)**.

---

<div align="center">
  Dibuat dengan ❤️ menggunakan Laravel.
</div>
