# Rencana Implementasi Aplikasi Web Supply Chain Management (SCM)

## Ringkasan Proyek
Aplikasi SCM berbasis web dengan 5 modul utama untuk mendukung siklus rantai pasok:
1. Manajemen Pengguna & Hak Akses (RBAC)
2. Manajemen Pemasok (Supplier Management)
3. Manajemen Inventaris (Inventory/Warehouse Management)
4. Manajemen Pesanan & Penjualan (Order Management)
5. Manajemen Pengiriman (Logistics & Shipping)

## Teknologi yang Digunakan
- **Backend**: Laravel 12
- **Frontend**: Blade + Tailwind CSS V4 (bahasa Indonesia)
- **Auth**: Laravel Breeze (Blade stack)
- **RBAC**: spatie/laravel-permission
- **Database**: MySQL/MariaDB (nama database: scm_db, user: root, password: kosong)

## Struktur Database (14 Tabel Baru)

### Auth & RBAC
- `roles` (id, name, guard_name)
- `permissions` (id, name, guard_name)
- `model_has_roles` (role_id, model_type, model_id)
- `model_has_permissions` (permission_id, model_type, model_id)
- `role_has_permissions` (permission_id, role_id)

### Manajemen Pemasok
- `suppliers` (id, name, contact_person, email, phone, address, city, province, postal_code, notes, status)
- `purchase_orders` (id, po_number, supplier_id, user_id, order_date, expected_date, status, subtotal, tax, total, notes)
- `purchase_order_items` (id, purchase_order_id, product_id, quantity, unit_price, subtotal, received_quantity)

### Manajemen Inventaris
- `categories` (id, name, type, parent_id)
- `products` (id, category_id, name, sku, description, unit, purchase_price, selling_price, stock_quantity, low_stock_threshold, image, is_active)
- `stock_movements` (id, product_id, type, quantity, reference_type, reference_id, notes, user_id)

### Manajemen Pesanan
- `orders` (id, order_number, customer_name, customer_email, customer_phone, shipping_address, order_date, status, subtotal, tax, shipping_cost, total, notes, user_id)
- `order_items` (id, order_id, product_id, quantity, unit_price, subtotal)

### Manajemen Pengiriman
- `shipments` (id, order_id, shipment_number, carrier, tracking_number, shipping_cost, status, origin, destination, estimated_delivery_date, delivered_at, user_id, notes)
- `tracking_events` (id, shipment_id, status, location, description, timestamp)

## Arsitektur Modul & Fitur

### Modul A: Manajemen Pengguna & Hak Akses (RBAC)
- **Roles**: Admin, Manager, Supplier, Warehouse Staff, Courier/Logistik
- **Features**:
  - CRUD Pengguna (hanya Admin)
  - Assign Role ke Pengguna
  - Middleware & Gates untuk otorisasi berbasis role
  - Manajemen Permission (admin only)

### Modul B: Manajemen Pemasok (Supplier Management)
- **Features**:
  - CRUD Supplier (data kontak, alamat, status aktif/nonaktif)
  - Sistem Purchase Order (PO):
    - Pembuatan PO dengan nomor otomatis (PO-YYYYMMDD-XXXX)
    - Workflow status: Draft → Dikirim → Dikonfirmasi → Diterima → Selesai
    - Manajemen Item PO (produk, kuantitas, harga satuan)
    - Konfirmasi penerimaan barang dari supplier
  - Laporan kinerja supplier

### Modul C: Manajemen Inventaris (Inventory/Warehouse Management)
- **Features**:
  - Manajemen Kategori (bahan baku, produk jadi, packaging)
  - CRUD Produk dengan SKU, deskripsi, satuan, harga beli/jual, ambang stok minimal
  - Pemantauan Stok Real-time
  - Pencatatan Stok Masuk (dari PO yang diterima) & Stok Keluar (untuk pengiriman pesanan)
  - Peringatan Stok Minimal (notifikasi otomatis ketika stok ≤ threshold)
  - Laporan mutasi stok

### Modul D: Manajemen Pesanan & Penjualan (Order Management)
- **Features**:
  - CRUD Pesanan Pelanggan/Distributor
  - Workflow Status Pesanan: Pending → Diproses → Dikirim → Selesai
  - Manajemen Item Pesanan (produk, kuantitas, harga)
  - Konfirmasi pesanan dan perubahan status
  - Laporan penjarian

### Modul E: Manajemen Pengiriman (Logistics & Shipping)
- **Features**:
  - Manajemen Pengiriman per Pesanan
  - Pemilihan Kurir/JNE/J&T/etc. dan biaya pengiriman
  - Pembuatan Nomor Resi Otomatis (SHIP-YYYYMMDD-XXXX)
  - Pelacakan Status Pengiriman: Menunggu Diambil → Dalam Transit → Diterima → Gagal
  - Sistem Tracking Events (riwayat update status dengan timestamp & lokasi)
  - Integrasi dengan pesanan (shipment dibuat setelah pesanan dikonfirmasi)

## Alur Implementasi (7 Fase)

| Fase | Fokus | Detail |
|------|-------|--------|
| **1** | Foundation | - Setup koneksi MySQL di `.env`<br>- Install Laravel Breeze (blade stack)<br>- Install spatie/laravel-permission<br>- Buat migration & seed untuk roles & permissions<br- Seed default admin user<br>- Layout blade dengan sidebar navigasi berbasis role |
| **2** | A - Pengguna & RBAC | - UserController (index, create, store, show, edit, update, destroy)<br>- Role assignment UI<br>- Middleware: `role:admin`, `role:warehouse`, dsb.<br>- Blade views untuk user management |
| **3** | B - Pemasok & PO | - SupplierController (CRUD)<br>- PurchaseOrderController dengan workflow status<br>- PurchaseOrderItem management (inline atau terpisah)<br>- Nomor PO auto-generate<br>- Views: supplier list, form, PO list, PO detail/form |
| **4** | C - Inventaris | - CategoryController<br>- ProductController dengan SKU unique<br>- StockMovementController (stock_in, stock_out forms)<br>- Low stock alert (blade component + notification system)<br>- Views: kategori, produk, stok masuk/keluar, peringatan stok |
| **5** | D - Pesanan | - OrderController (CRUD + status update)<br>- OrderItem management<br>- Nomor ORD auto-generate<br>- Workflow status pesanan<br>- Views: pesanan list, form, detail, item management |
| **6** | E - Pengiriman | - ShipmentController (create, update status, tracking)<br>- TrackingEventController (riwayat update)<br>- Nomor SHIP auto-generate<br>- Integrasi dengan order (shipment dibuat dari order status "dikirim")<br>- Views: pengiriman list, form, detail, tracking timeline |
| **7** | Dashboard & Laporan | - DashboardController dengan ringkasan:<br>  * PO menunggu konfirmasi<br>  * Stok di bawah ambang minimal<br>  * Pesanan aktif (diproses/dikirim)<br>  * Pengiriman hari ini<br>- Laporan dasar (PDF/print): laporan stok, laporan penjarian, laporan pengiriman<br>- Widget/grafik sederhana menggunakan Chart.js atau Tailwind components |

## Estimasi Effort
- **Backend**: ~160-200 jam (tergantung kompleksitas workflow)
- **Frontend**: ~120-160 jam (Blade views dengan Tailwind)
- **Total**: ~280-360 jam (35-45 hari kerja penuh waktu satu pengembang)

## Catatan Implementasi
1. **Bahasa UI**: Semua label, placeholder, pesan validasi, notifikasi dalam Bahasa Indonesia.
2. **Keamanan**: Laravel built-in protections (CSRF, XSS, SQL injection) + middleware auth.
3. **Validasi**: Form request validation untuk semua input.
4. **Relasi Database**: Menggunakan foreign key constraints dan cascade waar diperlukan.
5. **Index**: Tambah index pada kolom yang sering di-search (sku, nomor po, nomor order, tracking number).
6. **Testing**: Unit test untuk fitur kriteria (workflow PO, order status, stock movement).
7. **Deployment**: Siap untuk di-deploy ke server dengan PHP 8.2+, Composer, Node.js (untuk Vite/Tailwind).

## Selanjutnya
Setelah persetujuan rencana ini, langkah selanjutnya adalah:
1. Memperbarui file `.env` dengan kredensial MySQL
2. Menjalankan `composer install` dan `npm install`
3. Memulai implementasi Fase 1 (Foundation)

---
*Rencana ini dibuat berdasarkan kebutuhan spesifik untuk sistem SCM minimal dengan 5 modul utama yang dapat berjalan secara end-to-end.*