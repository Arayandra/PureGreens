# PureGreens

![CI](https://github.com/Arayandra/PureGreens/actions/workflows/ci.yml/badge.svg?branch=main)

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

Secara default aplikasi menjalankan perintah `python3`. Pada Windows, ubah `PYTHON_BINARY=python` di `.env` bila perintah `python3` tidak tersedia. Modul rekomendasi membaca riwayat item pesanan dari database dan menjalankan `python/apriori_recommendation.py`. Hasil dari proses Python divalidasi kembali di PHP; respons error, ID non-integer, ID tidak valid, dan rekomendasi produk yang sama dengan input tidak dikirim ke tampilan.

## Konfigurasi iPaymu

Isi variabel berikut pada `.env` sesuai kredensial sandbox atau production dari iPaymu:

```dotenv
IPAYMU_API_KEY=
IPAYMU_VA=
IPAYMU_API_URL=
IPAYMU_RETURN_URL=http://127.0.0.1:8000/checkout-success
IPAYMU_NOTIFY_URL=https://your-public-domain.example/ipaymu/callback
IPAYMU_CANCEL_URL=http://127.0.0.1:8000/checkout-cancel
```

Jangan pernah melakukan commit terhadap `.env`, API key, VA, password, atau kredensial pembayaran. Endpoint callback aplikasi sudah tersedia di `POST /ipaymu/callback`, diverifikasi dengan HMAC-SHA256, dan memeriksa nominal serta status transaksi. Ganti URL contoh di atas dengan alamat HTTPS publik yang dapat dijangkau server iPaymu; `127.0.0.1` hanya cocok untuk pengembangan lokal dan tidak dapat dijangkau provider. Daftarkan URL publik yang sama pada dashboard iPaymu, lalu uji seluruh alur menggunakan kredensial sandbox sebelum transaksi nyata.

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



## Rencana upgrade Laravel

**Baseline saat ini:** Laravel 10, PHP 8.1+, Filament 2, dan Livewire 2. Security support Laravel 10 berakhir pada **4 Februari 2025**.

**Target:** Laravel 13 dengan PHP 8.3+. Upgrade belum diterapkan pada `composer.json` atau `composer.lock`. Pekerjaan ini memiliki risiko integrasi lebih tinggi karena versi Filament, Livewire, Cashier, dan plugin Filament harus dinaikkan secara kompatibel; jangan hanya mengganti constraint framework.

Tahapan yang disarankan:

1. Buat branch `upgrade/laravel-13` dan simpan baseline test serta CI saat ini.
2. Siapkan PHP 8.3+, lalu periksa dukungan Laravel 13 untuk Filament, Livewire, Cashier, Filament Export, Overlook, shopping cart, dan invoice package.
3. Upgrade komponen admin dan Livewire mengikuti panduan migrasi resmi masing-masing paket. Pisahkan perubahan UI admin dari perubahan alur pembayaran.
4. Verifikasi autentikasi, katalog, cart, checkout, invoice, order history, admin CRUD, serta modul Apriori. Uji callback iPaymu memakai kredensial sandbox dan endpoint HTTPS publik; CI saja tidak menggantikan pengujian sandbox provider.
5. Jalankan `composer validate`, `composer audit`, `php artisan test`, build Vite, test Python untuk modul rekomendasi, dan workflow CI. Perbarui lockfile setelah dependency dan seluruh test tervalidasi.

File `draft.yaml`, `.blueprint`, dataset, model, dan gambar produk tidak dihapus pada tahap pembersihan ini karena perlu dipastikan terlebih dahulu apakah masih digunakan untuk dokumentasi, generator, atau fitur aplikasi.

Referensi: [Laravel release/support policy](https://laravel.com/framework/docs/releases) · [Laravel 13 upgrade guide](https://laravel.com/docs/13.x/upgrade).


## Screenshot aplikasi

Screenshot browser dari aplikasi yang sedang berjalan belum tersedia di repository ini. Foto sayuran dan gambar produk adalah aset konten, bukan bukti tampilan aplikasi. Setelah menjalankan toko dan panel admin dengan data demo, simpan screenshot halaman katalog/produk, cart atau checkout, serta dashboard Filament ke `docs/screenshots/` dan tampilkan dengan tautan Markdown.


## Latest CI verification

Verified on **16 November 2026** on branch `main`: **28 Laravel tests passed (62 assertions)** and **5 Python Apriori unit tests passed**. CI also completed the Vite production build. The iPaymu callback has unit-level verification, but a real provider sandbox transaction still needs a separately configured end-to-end test.
