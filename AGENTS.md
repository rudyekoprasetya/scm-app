# Laravel SCM Application Development Guide

## Setup
```bash
composer run setup
```
Installs dependencies, creates .env from .env.example, generates app key, runs migrations, and builds frontend assets.

## Development
```bash
composer run dev
```
Starts Laravel dev server (http://127.0.0.1:8000), queue worker, log viewer (pail), and Vite dev server concurrently.

## Testing
```bash
composer run test
```
Clears config cache and runs PHPUnit tests with SQLite in-memory database.

## Database
- Migrations: `php artisan migrate`
- Fresh migrate: `php artisan migrate:fresh`
- Seeders: `php artisan db:seed`
- Reset & seed: `php artisan migrate:fresh --seed`

## Frontend
- Dev: `npm run dev` (Vite with Hot Module Replacement)
- Build: `npm run build` (production assets)

## Code Quality
- Formatting: `vendor/bin/pint` (Laravel Pint)
- Static analysis: Not configured by default (consider larastan/phpstan for future)

## Key Packages Installed
- laravel/breeze: Authentication scaffolding (Blade + Tailwind)
- spatie/laravel-permission: Role-based access control (RBAC)
- barryvdh/laravel-dompdf: PDF export
- Font Awesome 6.5.1 (CDN): Icons in navigation & dashboard

## Directory Structure
- `app/` - Application code (Models, Controllers, Services, Middleware)
- `routes/` - Web (`web.php`) and Console (`console.php`) routes
- `database/` - Migrations, factories, seeders
- `tests/` - Feature and Unit tests
- `resources/` - Views, CSS, JavaScript (unprocessed)
- `public/` - Compiled assets (built by Vite)
- `bootstrap/app.php` - Application configuration (middleware aliases)

## Environment
- Testing uses SQLite in-memory database (see phpunit.xml)
- Development uses MySQL database (scm_db)
- Sail available for Docker development: `./vendor/bin/sail`

## Authentication & RBAC
- Default admin user: admin@scm.local / admin123
- Roles: admin, manager, supplier, warehouse, courier
- Permissions managed via Spatie Laravel Permission
- Middleware: `role:role-name` for route protection
- Traits: `HasRoles` on User model

## SCM Module Conventions
### Models
- Use singular names: Supplier, PurchaseOrder, Product, etc.
- BelongsTo relationships use snake_case foreign keys (e.g., supplier_id)
- HasMany relationships use plural method names (e.g., purchaseOrders())
- Use enumerated types for status fields in database and cast them in models

### Controllers
- Resource controllers for CRUD operations
- Custom methods for workflow actions (e.g., confirm, ship, deliver)
- Use Form Requests for validation
- Leverage middleware for role-based authorization

### Views
- Blade components for reusable UI elements (buttons, cards, forms)
- Layouts: app.blade.php and guest.blade.php
- Views organized by resource: resources/views/{resource}/
- Use Tailwind CSS for styling
- Form model binding for edit forms

### Routes
- Web routes in routes/web.php
- Route groups for middleware (auth, role)
- Resource routes for standard CRUD
- Custom routes for specific actions

### Database Migrations
- Timestamp columns: created_at, updated_at
- Soft deletes: use deleted_at column when needed
- Indexes on foreign keys and frequently searched columns
- Use appropriate data types (decimal for currency, enum for status)
- Cascade deletes where appropriate

## Implemented SCM Modules (5 Modul Utama)

### A. Manajemen Pengguna & Hak Akses (RBAC)
- User CRUD dengan assign role (hanya admin)
- Role listing dengan permissions
- Middleware role protection di routes
- Action buttons based on status workflow

### B. Manajemen Pemasok (Supplier Management)
- Supplier CRUD (nama, kontak, alamat, status)
- Purchase Order workflow: draft → sent → confirmed → received → completed
- PO items management dengan auto-calculate subtotal/total
- Auto-generate nomor PO: PO-YYYYMMDD-XXXX
- Penerimaan barang otomatis update stok

### C. Manajemen Inventaris (Inventory/Warehouse)
- Kategori produk (bahan baku, produk jadi, packaging)
- Produk dengan SKU, stok, harga, threshold
- Stok Masuk & Stok Keluar (manual + otomatis dari PO/Order)
- Peringatan Stok Menipis (stock <= low_stock_threshold)
- Mutasi stok dengan referensi ke PO/Order

### D. Manajemen Pesanan & Penjualan (Order Management)
- Order CRUD dengan pelanggan & alamat pengiriman
- Order Items management
- Workflow: pending → confirmed → processing → shipped → delivered → completed
- Auto-kurangi stok saat order dibuat
- Restore stok saat order dibatalkan
- Auto-create shipment saat order dikirim

### E. Manajemen Pengiriman (Logistics & Shipping)
- Shipment CRUD dengan carrier & nomor resi
- Workflow: pending → picked_up → in_transit → delivered/failed
- Tracking events timeline
- Auto-generate nomor shipment: SHIP-YYYYMMDD-XXXX
- Dashboard per-role (admin, warehouse, courier)

## File Changes Made
1. `.env`: MySQL database (scm_db)
2. `bootstrap/app.php`: role middleware alias
3. `app/Models/User.php`: HasRoles trait
4. `database/seeders/RoleAndPermissionSeeder.php`: 5 roles, 47 permissions, admin user
5. `app/Http/Middleware/CheckRole.php`: role checking middleware
6. `routes/web.php`: All SCM routes with role middleware
7. 14 Migrations: suppliers, purchase_orders, purchase_order_items, categories, products, stock_movements, orders, order_items, shipments, tracking_events
8. 11 Controllers: Dashboard, User, Role, Supplier, PurchaseOrder, Category, Product, StockMovement, Order, Shipment, TrackingEvent
9. 10 Models: Supplier, PurchaseOrder, PurchaseOrderItem, Category, Product, StockMovement, Order, OrderItem, Shipment, TrackingEvent
10. 14 Form Requests: validation rules for all modules
11. 32 Blade views: all CRUD + workflow views
12. Dashboard with role-specific views (admin/manager, warehouse, courier)

## Next Steps for Future Development
- Add notification/email alerts for low stock
- Add API endpoints for mobile/logistics
- Add real-time dashboard with charts