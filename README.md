# Aplikasi Sistem Manajemen Tagihan Pembayaran Internet

Aplikasi Sistem Manajemen Tagihan Pembayaran Internet dirancang untuk mempermudah proses pengelolaan dan pembayaran tagihan internet. Aplikasi ini dibuat menggunakan **Laravel v10** dengan minimal **PHP v8.2**.

---

## 🚀 Fitur Utama

### Dashboard & Manajemen
- **Dashboard Admin** - Statistik pendapatan, pengeluaran, profit dengan filter bulan/tahun
- **Manajemen Data Paket** - Kelola paket layanan internet
- **Manajemen Pelanggan** - CRUD data pelanggan dengan autogenerate akun
- **Buat & Kelola Tagihan** - Generate tagihan otomatis untuk semua pelanggan
- **Tagihan Lunas** - Riwayat pembayaran pelanggan
- **Manajemen Pengeluaran** - Catat pengeluaran operasional
- **Manajemen User Sistem** - Kelola akun admin

### Integrasi
- **WhatsApp Gateway (Fonnte)** - Notifikasi tagihan via WhatsApp
- **Payment Gateway (Tripay)** - Pembayaran online (QRIS, Transfer, E-Wallet)
- **MikroTik Integration** - Monitoring status jaringan pelanggan
- **GenieACS Integration** - Monitoring RX Power & WiFi Settings ONT

### Fitur Tambahan
- **Network Monitoring Map** - Peta lokasi pelanggan dengan status online/offline
- **Notifikasi Pelanggan** - In-app notification untuk pelanggan
- **Broadcast Notifikasi** - Kirim pengumuman ke pelanggan (pengingat/gangguan)
- **PWA Support** - Install sebagai aplikasi di HP
- **Laporan** - Export laporan ke Excel/PDF

---

## 📋 Persyaratan Sistem

- PHP **>= 8.2**
- Composer
- MySQL / MariaDB
- XAMPP atau web server sejenis

---

## 🛠️ Langkah Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/azizt91/Aplikasi-Sistem-Manajemen-Tagihan-Pembayaran-Internet.git
cd Aplikasi-Sistem-Manajemen-Tagihan-Pembayaran-Internet
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
```

Edit file `.env` sesuai konfigurasi database:
```env
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate App Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrations
```bash
php artisan migrate
```

### 6. Jalankan Seeders
```bash
php artisan db:seed
```

### 7. Buat Storage Link
```bash
php artisan storage:link
```

### 8. Jalankan Aplikasi
```bash
php artisan serve
```

Akses aplikasi di: `http://127.0.0.1:8000`

---

## 🔐 Login Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gmail.com | password123 |

---

## 📝 Catatan Penting

1. **Tambahkan Data Paket Terlebih Dahulu** sebelum menambahkan pelanggan
2. **Email & Password Pelanggan** dibuat otomatis saat menambahkan data pelanggan
3. Untuk integrasi **MikroTik**, konfigurasikan di menu Setting > MikroTik
4. Untuk integrasi **GenieACS**, konfigurasikan di menu Setting > GenieACS
5. Untuk **WhatsApp Notification**, konfigurasikan Fonnte API di menu Setting

---

## 👤 Author

- **Taufiq Aziz**
- [Facebook](https://www.facebook.com/azizt91) | [Instagram](https://www.instagram.com/azizt91) | [YouTube](https://youtube.com/@taufiqaziz1691) | [X](https://x.com/azizt91)

---

## 📄 License

[MIT License](https://opensource.org/licenses/MIT)
