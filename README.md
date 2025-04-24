# Aplikasi Sistem Manajemen Tagihan Pembayaran Internet

## Tentang Aplikasi

Aplikasi Sistem Manajemen Tagihan Pembayaran Internet dirancang untuk mempermudah proses pengelolaan dan pembayaran tagihan internet. Aplikasi ini dibuat menggunakan Laravel v10.* dan minimal PHP v8.2 jadi apabila pada saat proses instalasi atau penggunaan terdapat error atau bug kemungkinan karena versi dari PHP yang tidak support.

### **Persyaratan Sistem**
- PHP minimal versi **8.2**  
- Sudah terinstal **XAMPP** atau perangkat sejenis  
  [Download XAMPP di sini](https://www.apachefriends.org/download.html)  
- Composer telah terinstal di komputer  
  [Unduh Composer di sini](https://getcomposer.org/download/)  

---

### **Langkah Instalasi**
1. **Clone repository**:
   ```bash
   git clone https://github.com/azizt91/Aplikasi-Sistem-Manajemen-Tagihan-Pembayaran-Internet.git
   ```
2. **Masuk ke folder proyek**:
   ```bash
   cd Aplikasi-Sistem-Manajemen-Tagihan-Pembayaran-Internet
   ```
3. **Instal dependensi Laravel menggunakan Composer**:
   ```bash
   composer install
   ```
4. **Buat file `.env` dari contoh**:
   ```bash
   cp .env.example .env
   ```
5. **Buat database baru di phpMyAdmin**:
   - Masuk ke phpMyAdmin dan buat database baru sesuai kebutuhan.
   - Import file SQL dari folder `db/laravel10.sql` ke dalam database.

6. **Sesuaikan konfigurasi database di file `.env`**:
   Edit baris berikut di file `.env` agar sesuai dengan database yang telah kamu buat:
   ```env
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

8. **Tambah user admin melalui seeder**:
   Jalankan perintah berikut untuk menambahkan user admin:
   ```bash
   php artisan db:seed --class=NamaSeeder
   ```

9. **Jalankan aplikasi**:
   ```bash
   php artisan serve
   ```

Selamat, aplikasi siap diakses! 😊  

---

### **Login Admin**
- **Email**: youremail@gmail.com  
- **Password**: password123  

---

### **Catatan**
- **Tambahkan Data Paket Terlebih Dahulu**: Sebelum menambahkan pelanggan, pastikan kamu sudah menambahkan data paket.  
- **Email dan Password Pelanggan**: Email dan password untuk login pelanggan akan otomatis dibuat saat menambahkan data pelanggan.


## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
