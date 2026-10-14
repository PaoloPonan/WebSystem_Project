# Inventory HQ – Laboratory Exercise 6

## Real-Time Inventory Tracking System (Laravel MVC)

This project extends Laboratory Exercise 5 with **real-time inventory tracking** features.

### New Features (Lab 6)

- **InventoryTransaction model** – tracks stock_in, stock_out, and adjustment movements
- **Eloquent Observer** – automatically updates product quantity when a transaction is created/deleted
- **Database transactions** – ensures atomic stock updates (no partial writes)
- **Low-stock notifications** – database + mail channels via Laravel Notifications
- **Transaction CRUD + filters** – filter by product, type, and date range
- **Stock Adjustment form** – reconcile physical vs system counts
- **API endpoints** for dashboard stats, low-stock list, and recent transactions
- **AJAX polling** – dashboard refreshes statistics every 30 seconds
- **Product transaction history** – running balance on the product show page

### Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB (or SQLite for quick testing)
- Node.js (optional, for Vite assets)

### Setup

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file and generate key
cp .env.example .env
php artisan key:generate

# 3. Configure database in .env (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 4. Run migrations
php artisan migrate

# 5. Seed sample products (optional)
php artisan db:seed --class=ProductSeeder

# 6. Start the server
php artisan serve
```

Visit http://127.0.0.1:8000 and register/login.

### Key Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | /dashboard | Real-time dashboard |
| GET | /products | Product index |
| GET | /transactions | Transaction list + filters |
| GET | /transactions/create | Record stock movement |
| GET | /transactions/adjustment/form | Stock adjustment |
| GET | /api/dashboard/stats | JSON stats (AJAX) |
| GET | /api/products/low-stock | JSON low-stock products |
| GET | /api/transactions/recent | JSON recent transactions |

### Architecture Notes

1. **Observer** (`InventoryTransactionObserver`) listens to `creating`, `created`, and `deleted` events.
2. Stock quantity is updated inside `DB::transaction()` with row locking (`lockForUpdate`).
3. Negative inventory is prevented both in the observer and controller validation.
4. Deleting a transaction **reverses** the stock change.
5. Low-stock alerts are deduplicated (no duplicate notifications within 24 hours per product).

### Lab Questions (for your report)

1. **Failed quantity update after transaction create**  
   Wrap both operations in `DB::transaction()`. If the quantity update throws, Laravel rolls back the entire transaction so the InventoryTransaction record is never left without a matching stock change.

2. **Exception inside DB::transaction()**  
   Laravel automatically rolls back the database transaction when an uncaught exception is thrown inside the closure. The changes are never committed.

3. **AJAX Polling vs WebSockets**  
   - Polling: simple, no extra services, higher server load, not truly instant.  
   - WebSockets (Laravel Echo + Pusher/Reverb): true push, efficient, more complex setup.  
   This lab uses polling (30 s interval) for simplicity.

### Color Scheme

Primary navy (#1e3a5f), construction orange accent (#e67e22), success green. Defined in `public/css/custom.css`.

---
Laboratory Exercise 6 – Extending MVC Architecture with Laravel: Real-Time Inventory Tracking System
