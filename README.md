# SCM - Supply Chain Management Apps

Aplikasi web **Supply Chain Management (SCM)** berbasis **Laravel 12** untuk mengelola rantai pasok bisnis secara end-to-end. Mencakup manajemen pengguna & hak akses (RBAC), pemasok, inventaris, pesanan penjualan, dan pengiriman logistik.

---

## Tech Stack & Library

| Kategori | Teknologi |
|---|---|
| **Framework** | Laravel 12, PHP 8.2 |
| **Database** | MySQL (dev/production), SQLite (testing) |
| **Frontend** | Blade, Tailwind CSS 3, Alpine.js, Vite |
| **Auth** | Laravel Breeze (Blade stack) |
| **RBAC** | spatie/laravel-permission v6 |
| **API Auth** | Laravel Sanctum (token-based) |
| **Charts** | Chart.js (real-time dashboard) |
| **Export** | barryvdh/laravel-dompdf (PDF), CSV native (Excel) |
| **Icons** | Font Awesome 6.5.1 (CDN) |
| **Notifications** | Laravel Database Notifications |

---

## Fitur / Menu

### A. Manajemen Pengguna & Hak Akses (RBAC)
- CRUD User dengan assign role (hanya admin)
- Daftar role & permission
- Middleware role protection di routes

### B. Manajemen Pemasok (Supplier)
- CRUD Supplier (nama, kontak, alamat, status)
- **Purchase Order** — workflow: `draft → sent → confirmed → received → completed`
- PO items dengan auto-calculate subtotal/total
- Auto-generate nomor PO: `PO-YYYYMMDD-XXXX`
- Penerimaan barang otomatis update stok

### C. Manajemen Inventaris (Warehouse)
- Kategori produk (raw_material, finished_good, packaging)
- Produk dengan SKU, stok, harga, threshold stok menipis
- Stok Masuk & Stok Keluar (manual + otomatis dari PO/Order)
- **Peringatan Stok Menipis** — notifikasi bell & email saat stok ≤ threshold
- Mutasi stok dengan referensi ke PO/Order
- Dashboard chart stok produk & mutasi 7 hari

### D. Manajemen Pesanan (Order Management)
- Order CRUD dengan pelanggan & alamat pengiriman
- Order Items management
- Workflow: `pending → confirmed → processing → shipped → delivered → completed`
- Auto-kurangi stok saat order dibuat
- Restore stok saat order dibatalkan
- Auto-create shipment saat order dikirim

### E. Manajemen Pengiriman (Logistics)
- Shipment CRUD dengan carrier & nomor resi
- Workflow: `pending → picked_up → in_transit → delivered/failed`
- Tracking events timeline
- Auto-generate nomor shipment: `SHIP-YYYYMMDD-XXXX`
- **Public tracking** via API (tanpa login)

### F. Dashboard Real-time
- Widget statistik per-role (admin, warehouse, courier)
- Chart interaktif (Chart.js) — donut, bar, line
- Auto-refresh setiap 30 detik

### G. Export Laporan
- PDF (DomPDF) dan CSV/Excel untuk semua modul

### H. API Endpoints
- REST API dengan token-based auth (Sanctum)
- Public tracking endpoint
- Mobile/logistics ready

---

## Role & Akun Default

| Role | Email | Password | Deskripsi |
|---|---|---|---|
| **admin** | admin@scm.local | admin123 | Akses penuh semua fitur |
| manager | — | — | Manajemen operasional |
| warehouse | — | — | Manajemen stok & gudang |
| supplier | — | — | Melihat PO terkait |
| courier | — | — | Manajemen pengiriman |

> `php artisan db:seed --class=RoleAndPermissionSeeder` untuk membuat role & admin.

---

## Cara Deploy / Run Dev

### Prasyarat
- PHP 8.2+
- Composer
- MySQL / MariaDB
- Node.js & npm

### Langkah-langkah

```bash
# 1. Clone repositori
git clone <repo-url> scm-app
cd scm-app

# 2. Install PHP dependencies
composer install

# 3. Copy .env dan sesuaikan konfigurasi database
cp .env.example .env
# Edit .env: DB_DATABASE=scm_db, DB_USERNAME=root, DB_PASSWORD=

# 4. Generate application key
php artisan key:generate

# 5. Buat database MySQL
mysql -u root -e "CREATE DATABASE IF NOT EXISTS scm_db"

# 6. Jalankan migrasi & seeder
php artisan migrate
php artisan db:seed --class=RoleAndPermissionSeeder

# 7. Install & build frontend assets
npm install
npm run build

# 8. Jalankan dev server
composer run dev
# atau:
# php artisan serve    (terminal 1)
# npm run dev          (terminal 2)
```

Akses aplikasi di **http://127.0.0.1:8000** — login dengan `admin@scm.local` / `admin123`.

### Testing
```bash
composer run test
# atau:
php artisan config:clear && vendor/bin/phpunit
```

### Perintah Penting
```bash
php artisan migrate:fresh --seed            # Reset database + seed
php artisan app:check-low-stock              # Cek & kirim notifikasi stok menipis
php artisan notifications:table && migrate   # Setup tabel notifikasi
```

---

## API Endpoint Documentation

**Base URL:** `http://127.0.0.1:8000/api`

### Authentication

| Method | Endpoint | Deskripsi |
|---|---|---|
| `POST` | `/api/login` | Login, dapatkan token |
| `POST` | `/api/logout` | Hapus token (auth) |
| `GET` | `/api/user` | Data user saat ini (auth) |

**Login Request:**
```json
{
  "email": "admin@scm.local",
  "password": "admin123",
  "device_name": "mobile-app"
}
```

**Login Response:**
```json
{
  "token": "1|abc123...",
  "user": { "id": 1, "name": "Admin", "email": "admin@scm.local", "roles": ["admin"], "permissions": [...] }
}
```

> **Semua endpoint di bawah ini membutuhkan header:** `Authorization: Bearer {token}`

### Dashboard

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/dashboard/stats` | Statistik dashboard per-role |

### Suppliers

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/suppliers` | Daftar supplier (paginated) |
| `POST` | `/api/suppliers` | Tambah supplier |
| `GET` | `/api/suppliers/{id}` | Detail supplier |
| `PUT` | `/api/suppliers/{id}` | Update supplier |
| `DELETE` | `/api/suppliers/{id}` | Hapus supplier |

### Products

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/products` | Daftar produk aktif (paginated) |
| `GET` | `/api/products/{id}` | Detail produk |
| `GET` | `/api/products/low-stock` | Produk dengan stok menipis |

### Purchase Orders

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/purchase-orders` | Daftar PO (paginated) |
| `GET` | `/api/purchase-orders/{id}` | Detail PO + items |
| `PATCH` | `/api/purchase-orders/{id}/send` | Kirim PO (draft → sent) |
| `PATCH` | `/api/purchase-orders/{id}/confirm` | Konfirmasi PO (sent → confirmed) |
| `PATCH` | `/api/purchase-orders/{id}/receive` | Terima PO (confirmed → received) |
| `PATCH` | `/api/purchase-orders/{id}/cancel` | Batalkan PO |

### Orders

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/orders` | Daftar pesanan (paginated) |
| `GET` | `/api/orders/{id}` | Detail pesanan + items |
| `PATCH` | `/api/orders/{id}/confirm` | Konfirmasi pesanan |
| `PATCH` | `/api/orders/{id}/process` | Proses pesanan |
| `PATCH` | `/api/orders/{id}/ship` | Kirim pesanan |
| `PATCH` | `/api/orders/{id}/deliver` | Tandai terkirim |
| `PATCH` | `/api/orders/{id}/cancel` | Batalkan pesanan |

### Shipments & Tracking

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/shipments` | Daftar pengiriman (paginated) |
| `GET` | `/api/shipments/{id}` | Detail pengiriman + tracking events |
| `POST` | `/api/shipments/{id}/tracking` | Update status tracking |
| `GET` | `/api/tracking?tracking_number=X` | **Public** — lacak resi (tanpa auth) |

### Notifications

| Method | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/api/notifications` | Daftar notifikasi user |
| `POST` | `/api/notifications/{id}/read` | Tandai satu notifikasi dibaca |
| `POST` | `/api/notifications/read-all` | Tandai semua notifikasi dibaca |

---

### Contoh Request Update Tracking (Mobile)

```bash
curl -X POST http://127.0.0.1:8000/api/shipments/1/tracking \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "in_transit",
    "location": "Jakarta",
    "description": "Paket dalam perjalanan menuju Surabaya"
  }'
```

### Contoh Public Tracking (Mobile/Public)

```bash
curl "http://127.0.0.1:8000/api/tracking?tracking_number=JNE00123456"
```

```json
{
  "data": {
    "shipment_number": "SHIP-20260528-0001",
    "carrier": "JNE",
    "tracking_number": "JNE00123456",
    "status": "in_transit",
    "origin": "Jakarta",
    "destination": "Surabaya",
    "tracking_events": [
      {
        "status": "picked_up",
        "location": "Jakarta",
        "description": "Paket telah diambil",
        "occurred_at": "2026-05-28T10:00:00.000000Z"
      },
      {
        "status": "in_transit",
        "location": "Jakarta",
        "description": "Paket dalam perjalanan menuju Surabaya",
        "occurred_at": "2026-05-28T14:30:00.000000Z"
      }
    ]
  }
}
```
