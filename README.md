# KEPTS

KATH Emergency Live Patient Tracking System for Komfo Anokye Teaching Hospital.

## Stack
- PHP 8.2+
- Laravel 11
- MySQL
- Bootstrap 5
- Blade Templates
- JavaScript, jQuery, AJAX polling
- DataTables
- SweetAlert2
- Chart.js
- Font Awesome

## Setup
1. Copy `.env.example` to `.env` and configure MySQL.
2. Install Composer dependencies.
3. Run migrations and seeders.
4. Serve the app through your preferred Laravel host.

## Demo Credentials
- Admin: admin@kath.local / Password123!
- Triage Nurse: triage@kath.local / Password123!
- Ward Staff: ward@kath.local / Password123!
- Specialty Doctor: doctor@kath.local / Password123!

## Notes
- Live dashboards poll every 3 seconds through `/api/*` routes exposed under the web session.
- Admin users can manage wards, teams, users, and audit logs.
- Triage nurses can register, update, transfer, discharge, and admit patients.
- User account creation is restricted to email addresses ending in `kath.gov.gh`.
