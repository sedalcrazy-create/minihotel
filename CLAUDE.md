# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Bank Melli Dormitory Management System (سیستم مدیریت خوابگاه بانک ملی) - A Laravel 11 application for managing a 132-bed dormitory across 22 units. Features include personnel management, bed reservations, meal tracking, cleaning logs, and maintenance requests. The UI is entirely in Persian (Farsi) with Jalali (Persian) calendar support.

## Development Commands

### Docker (Primary Development Environment)
```bash
# Build and start
docker-compose up -d --build

# Install dependencies
docker-compose exec app composer install

# Run migrations with seed data
docker-compose exec app php artisan migrate --seed

# Generate app key
docker-compose exec app php artisan key:generate

# View logs
docker-compose logs -f app

# Enter container shell
docker-compose exec app sh
```

### Artisan Commands
```bash
docker-compose exec app php artisan [command]

# Clear caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### Code Style
```bash
# Laravel Pint (code formatting)
./vendor/bin/pint
```

### Testing
```bash
# PHPUnit tests
docker-compose exec app php artisan test

# Single test
docker-compose exec app php artisan test --filter=TestClassName
```

## Architecture

### Domain Model Hierarchy
```
Building → Unit → Room → Bed → Reservation
```
- **Building**: Top-level facility container
- **Unit**: 22 units within the building
- **Room**: Individual rooms within units
- **Bed**: 132 beds total (6 beds per unit on average)
- **Reservation**: Links beds to guests/personnel

### Key Models and Relationships

**Reservation** (`app/Models/Reservation.php`)
- Central model connecting guests to beds
- Belongs to: AdmissionType, Personnel, Guest, Room
- Has many-to-many with Beds via `reservation_beds` pivot
- Statuses: `reserved`, `checked_in`, `checked_out`, `cancelled`
- Three admission types: course classes (دوره کلاسی), conferences (همایش), business trips (ماموریت اداری)

**Personnel** (`app/Models/Personnel.php`)
- Bank employees who can make reservations
- Supports Excel import/export via Maatwebsite/Excel
- Fields include employment_code, national_code, service_location, department

**Bed** (`app/Models/Bed.php`)
- Statuses: `available`, `occupied`, `needs_cleaning`, `under_maintenance`
- Has cleaning logs and maintenance requests

### Controllers

- `AuthController`: Login/logout with session-based authentication
- `DashboardController`: Main dashboard with statistics and visual schematic
- `PersonnelController`: CRUD + Excel import/export (template download, bulk import)
- `ReservationController`: CRUD + check-in/check-out actions

### Key Packages

- **morilog/jalali**: Persian (Jalali) date handling
- **maatwebsite/excel**: Excel import/export for personnel
- **barryvdh/laravel-dompdf**: PDF generation for reports
- **spatie/laravel-permission**: Role-based access control (5 user roles)

### Database

- Uses SQLite (file-based at `database/database.sqlite`)
- Migrations are numbered sequentially: `2024_01_01_000001_` through `000014_`

### User Roles

| Role | Email | Default Password |
|------|-------|------------------|
| System Admin | admin@bank.ir | password |
| Operator | operator@bank.ir | password |
| Dormitory Manager | manager@bank.ir | password |

## File Structure Notes

- `docker/nginx/default.conf`: Nginx configuration
- `docker/php/php-fpm.conf`: PHP-FPM settings
- `docker/supervisor/supervisord.conf`: Process supervisor config
- Views are Blade templates in `resources/views/` with RTL Bootstrap 5

## Permissions Fix

If you encounter permission errors:
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```
