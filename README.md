# PureGreens

Repository GitHub: [https://github.com/Arayandra/PureGreens.git](https://github.com/Arayandra/PureGreens.git)

PureGreens adalah aplikasi e-commerce untuk katalog dan penjualan produk sayuran segar. Proyek ini dibangun menggunakan Laravel dan Livewire, dengan panel administrasi Filament serta modul rekomendasi produk berbasis algoritma Apriori.

## Fitur

- Katalog produk dengan halaman detail produk.
- Daftar kategori, pencarian, pengurutan, dan filter harga.
- Keranjang belanja: menambahkan produk, mengubah kuantitas, dan menghapus item.
- Registrasi, login, dan pengelolaan profil pengguna.
- Checkout dan struktur integrasi payment gateway iPaymu.
- Dashboard pelanggan untuk melihat pesanan dan invoice.
- Panel admin Filament untuk mengelola produk, kategori, pesanan, dan pengguna.
- Rekomendasi produk menggunakan association rules (Apriori) dari riwayat transaksi.
- Email notifikasi pesanan.

> **Catatan status:** checkout menggunakan callback iPaymu pada `POST /ipaymu/callback`. Callback diverifikasi dengan HMAC-SHA256 menggunakan Merchant VA, mengecek nominal pesanan, dan mencegah callback berulang menurunkan status pesanan yang sudah selesai. Pengujian sandbox dengan kredensial Merchant milik pemilik repository tetap wajib sebelum transaksi nyata.

## Teknologi

| Bagian | Teknologi |
| --- | --- |
| Backend | PHP 8.1+, Laravel 10 |
| UI interaktif | Livewire 2 |
| Admin panel | Filament 2 |
| Frontend build | Vite, Tailwind CSS, Alpine.js |
| Database | MySQL / MariaDB |
| Rekomendasi | Python, pandas, mlxtend (Apriori) |
| Pembayaran | iPaymu (perlu konfigurasi kredensial) |

## Persyaratan

- PHP 8.1 atau lebih baru (PHP 8.2/8.3 direkomendasikan), beserta ekstensi PHP yang dibutuhkan Laravel dan paket Composer (termasuk PDO MySQL, Mbstring, OpenSSL, Tokenizer, XML, Ctype, JSON, dan Fileinfo).
- Composer 2.
- MySQL atau MariaDB.
- Node.js dan npm.
- Python 3 (opsional, hanya untuk modul rekomendasi Apriori).

## Repository dan remote Git

Repository resmi proyek ini adalah [Arayandra/PureGreens](https://github.com/Arayandra/PureGreens). Untuk folder proyek yang sudah ada dan ingin diarahkan ke repository tersebut, jalankan:

```bash
git remote set-url origin https://github.com/Arayandra/PureGreens.git
```

Jika folder belum memiliki remote `origin`, gunakan `git remote add origin` sebagai gantinya.

## Instalasi lokal

1. Clone repository dan masuk ke folder proyek:

   ```bash
   git clone https://github.com/Arayandra/PureGreens.git
   cd PureGreens
   ```

2. Pasang dependency PHP dan frontend:

   ```bash
   composer install
   npm ci
   ```

3. Siapkan file environment dan application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Pada Windows PowerShell, gunakan `Copy-Item .env.example .env` sebagai pengganti perintah `cp`.

4. Buat database MySQL, lalu sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `.env`.

5. Atur `MAIL_FROM_ADDRESS` dan, bila digunakan, `SUPPORT_PHONE`. (Opsional) Isi `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD` pada `.env` sebelum menjalankan seeder. Jika ketiganya tidak disediakan, seeder tidak membuat akun admin bawaan.

6. Jalankan migration dan data contoh:

   ```bash
   php artisan migrate --seed
   ```

7. Buat symbolic link untuk file publik dan build asset:

   ```bash
   php artisan storage:link
   npm run build
   ```

8. Jalankan server pengembangan:

   ```bash
   php artisan serve
   ```

   Buka `http://127.0.0.1:8000` untuk halaman toko. Panel administrasi Filament tersedia di `/admin` setelah akun dengan hak admin dibuat.

## Modul rekomendasi Apriori (opsional)

Instal dependensi Python:

```bash
python -m pip install -r python/requirements.txt
```

Secara default aplikasi menjalankan perintah `python3`. Pada Windows, ubah `PYTHON_BINARY=python` di `.env` bila perintah `python3` tidak tersedia. Modul rekomendasi membaca riwayat item pesanan dari database dan menjalankan `python/apriori_recommendation.py`.

## Konfigurasi iPaymu

Isi variabel berikut pada `.env` sesuai kredensial sandbox atau production dari iPaymu:

```dotenv
IPAYMU_API_KEY=
IPAYMU_VA=
IPAYMU_API_URL=
IPAYMU_RETURN_URL=http://127.0.0.1:8000/checkout-success
IPAYMU_NOTIFY_URL=http://127.0.0.1:8000/payment/notify
IPAYMU_CANCEL_URL=http://127.0.0.1:8000/checkout-cancel
```

Jangan pernah melakukan commit terhadap `.env`, API key, VA, password, atau kredensial pembayaran. Sebelum digunakan di production, lengkapi dan verifikasi endpoint notifikasi pembayaran, validasi tanda tangan callback sesuai dokumentasi iPaymu, HTTPS, serta alur status transaksi.

## Pengujian dan build

```bash
php artisan route:list
php artisan test
npm run build
python -m pip install -r python/requirements.txt
cd python && python -m unittest -v test_apriori_recommendation.py
```

GitHub Actions runs Laravel tests, the Vite build, and the Apriori unit tests on pushes and pull requests.

Pastikan database sudah berjalan dan konfigurasi `.env` sudah benar sebelum menjalankan pengujian yang mengakses database.

## Struktur direktori utama

```text
app/
  Filament/       # Resources dan dashboard admin
  Http/Livewire/  # Komponen halaman dan proses toko
  Models/         # Model Eloquent
  Services/       # Layanan invoice dan rekomendasi
config/           # Konfigurasi aplikasi
database/         # Migration, factory, dan seeder
python/            # Script rekomendasi Apriori
resources/         # Blade views, CSS, dan JavaScript
routes/            # Definisi route web dan API
```

File upload pengguna dan hasil invoice di `storage/app/public` tidak disertakan ke repository. Backup data upload secara terpisah bila diperlukan.

## Keamanan

- Simpan konfigurasi rahasia hanya pada `.env` lokal atau secret manager deployment.
- Gunakan password admin yang kuat dan jangan memakai kredensial development di production.
- Jangan aktifkan `APP_DEBUG=true` pada production.
- Uji pembayaran di sandbox sebelum beralih ke kredensial production.

## Lisensi

Belum ada lisensi proyek yang ditentukan secara khusus. Tambahkan file lisensi sebelum distribusi publik apabila diperlukan.
