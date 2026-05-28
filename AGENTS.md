# Laravel Application Development Guide

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
- Fresh migrate: `php artisan migrate:fresh` (drop & recreate all tables)
- Seeders: `php artisan db:seed`
- Reset & seed: `php artisan migrate:fresh --seed`

## Frontend
- Dev: `npm run dev` (Vite with Hot Module Replacement)
- Build: `npm run build` (production assets)

## Code Quality
- Formatting: `vendor/bin/pint` (Laravel Pint)
- No static analysis configured by default

## Directory Structure
- `app/` - Application code (Models, Controllers, Services)
- `routes/` - Web (`web.php`) and Console (`console.php`) routes
- `database/` - Migrations, factories, seeders
- `tests/` - Feature and Unit tests
- `resources/` - Views, CSS, JavaScript (unprocessed)
- `public/` - Compiled assets (built by Vite)

## Environment
- Testing uses SQLite in-memory database (see phpunit.xml)
- Sail available for Docker development: `./vendor/bin/sail`