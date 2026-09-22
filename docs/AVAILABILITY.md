# Availability calendar and manual blocks

This is a file-configured PHP/MySQL availability module, **not a booking engine**. Requests do not reserve inventory. No external sync, pricing, payments, Google authorization, or channel-management code is installed.

## Current local review environment

- Site: `http://127.0.0.1:8080/check-dates`
- Admin: `http://127.0.0.1:8080/admin/calendar`
- API: `http://127.0.0.1:8080/api/availability?unit=the-beginning`
- MySQL 8.4: loopback `127.0.0.1:3307`, database `tribeinn_availability`.
- Its existing data directory is `var/mysql-availability`. **Do not initialize it again.**
- Private connection settings and the admin password **hash** live in ignored `var/availability.local.php`. They are not website deployment assets.
- The application account has SELECT on units and SELECT/INSERT/UPDATE/DELETE on availability_blocks, not schema administration privileges.

Start the existing local database in PowerShell if it is stopped:

```powershell
Start-Process -FilePath 'C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysqld.exe' -ArgumentList '--no-defaults','--basedir=C:/laragon/bin/mysql/mysql-8.4.3-winx64','--datadir=D:/Documents/ChatGPT/tribeinn/var/mysql-availability','--bind-address=127.0.0.1','--port=3307','--mysqlx=OFF','--log-error=D:/Documents/ChatGPT/tribeinn/var/mysql-availability/server.log' -WindowStyle Hidden
php -d session.save_path=D:/Documents/ChatGPT/tribeinn/var/sessions -S 127.0.0.1:8080 router.php
```

Set your admin password without storing plaintext or putting it in command history:

```powershell
./tools/configure-availability-admin.ps1 -Username tribeinn-local
```

The prompt is masked. Only a PHP `password_hash()` is saved; database settings are preserved and previous admin sessions become invalid. Use 12–72 bytes. Login attempts are limited to five per IP in 15 minutes. A temporary lockout may remain until this interval expires.

On Unix, pass a password via stdin to `php tools/configure-availability-admin.php USERNAME`; use a masked shell prompt, not a password literal in a command. Never redirect the password to a file.

## New installation / Hostinger

1. Use PHP 8.1+ with PDO MySQL, mbstring and sessions; MySQL 8.0.16+ (enforced CHECK) or MariaDB 10.2.1+. The local verified version is MySQL 8.4.3 / PHP 8.3.
2. Create a database and import `database/001-availability.sql` in phpMyAdmin. It creates range-based `units` and `availability_blocks` and inserts the `the-beginning` unit. Re-running the initial migration preserves existing blocks. Do not assume it upgrades later schema versions.
3. Alternatively set `TRIBE_MIGRATION_DSN`, `TRIBE_MIGRATION_USER`, `TRIBE_MIGRATION_PASSWORD` only in the migration process, then run `php tools/availability-migrate.php`. Use a DDL-capable migration account, separate from the runtime account.
4. Configure runtime `TRIBE_DB_DSN` (e.g. `mysql:host=localhost;dbname=YOUR_DATABASE;charset=utf8mb4`), `TRIBE_DB_USER`, `TRIBE_DB_PASSWORD`, `TRIBE_ADMIN_USER`, `TRIBE_ADMIN_PASSWORD_HASH` as server environment settings. No default production credentials exist.
5. Where environment setup is inconvenient, set `TRIBE_AVAILABILITY_CONFIG` to an absolute private PHP file **outside public_html**. It returns an array using keys `dsn`, `db_user`, `db_password`, `admin_user`, `admin_password_hash`, optional `unit`, `timezone`, and `security_dir`. Private-file values override environment defaults. Keep the file readable only by the PHP account. The hash can be prepared locally with the CLI tool; do not store an admin plaintext password. The necessary database credential is a secret connection setting, never a public asset.
6. Use HTTPS for admin. Secure cookies are enabled when PHP sees HTTPS; configure the hosting HTTPS environment correctly behind a proxy. Do not trust arbitrary forwarded headers. Use a writable, private PHP session directory and writable private `security_dir` for rate limiting, shared across PHP workers on a single host.
7. Apache/LiteSpeed must honor `.htaccess`; it denies config/includes/pages/tools/docs/database/source-assets/var and dotfiles. If using another server, reproduce those denials. `router.php` is for PHP local development only. Never serve the repository as a static directory.
8. Deploy required PHP/CSS/JS plus `.htaccess`. Do not upload local `var/`, database data files, screenshots, source packs or credentials. Import the schema separately, then keep it outside the public document root. PHP must have `display_errors=Off` and private error logging in production.

## Dates and persistence

Each block occupies `[start_date, end_date)`. A block 2026-10-03 → 2026-10-07 occupies nights 3, 4, 5, 6. Guests may check out on October 3 or check in on October 7. A checkout-only boundary is labelled in the guest calendar even though that night's date is unavailable.

Overlap means `requested_start < block_end AND requested_end > block_start`. Same-day stays, reversed dates, malformed dates and past arrivals are rejected. Dates are calendar dates in Asia/Kolkata, with no daylight-saving/hour arithmetic. The frontend uses UTC only to render date labels consistently; the server supplies today's local date.

`units`: id, unique slug, name, active, timestamps.

`availability_blocks`: id, unit_id foreign key, start_date, exclusive end_date, private reason, extensible source, timestamps; composite lookup index. No per-day rows. Admin creates `source=manual` only and cannot edit/remove other sources. Overlapping manual blocks are allowed and merged for display; deleting one does not unblock another overlapping block. An ongoing block may be edited while preserving its past start date; expired blocks are omitted from the upcoming list.

## Public API and enquiry flow

GET `/api/availability?unit=the-beginning` returns only `unit`, `today`, and merged `unavailable` objects with `start`/`end`. It never returns IDs, reasons or sources. No-store response headers ensure a fresh check.

Optional `start=YYYY-MM-DD&end=YYYY-MM-DD` adds `valid: true/false`. Invalid input gives 400/422; inactive/unknown units 404; database/provider failure 503 **without an empty unavailable list**. Only GET is supported.

The guest calendar and date fields stay synchronized. Continue refreshes availability before opening the existing Email/WhatsApp choice. Both channel actions recheck; WhatsApp offers a normal link if the browser blocks the asynchronous popup. Nothing sends automatically on Continue. Email additionally calls the same service on the server after CSRF and enquiry validation, before transport. No browser result is trusted as permission to send.

On a database/API outage, calendar dates are disabled and a visible message explains that dates are unverified. Typed enquiries may continue, with the same warning repeated in the contact choice. Previously known overlaps remain rejected. If availability fails between channel selection and email submission, the email response carries `availability_unverified` and the success message clarifies that personal checking is needed. The existing email already says availability is unconfirmed. This permissive outage policy is deliberate for enquiries, not suitable for confirmed bookings.

## Admin security

Single configured admin, no role/account dashboard. PHP password hashing/verification; separate strict-mode, HttpOnly, SameSite=Strict session; Secure on HTTPS; regeneration at login; 30-minute sliding idle timeout; hash/config changes invalidate sessions; logout destroys session and cookie. Login, logout, save, and removal require session CSRF tokens. Removal requires explicit confirmation. GET never mutates. Database mutations use bound parameters and enforce unit/source server-side. Private text is escaped on output. No-store, frame protection and same-origin form policy apply. Database errors are generic in responses.

Rate limiting is per remote IP in a private locked file, surviving new cookies. This small single-host implementation is not a distributed identity service; future multi-host deployment must centralize the limiter/session storage. Password hashes and connection credentials never enter public JSON or markup.

## Future integrations

`AvailabilityProvider::ranges(unit, today)` → `AvailabilityService` → public endpoint / enquiry check. The service merges all provider ranges and strips metadata. The current MySQL provider reads all sources. Future Airbnb, Agoda, MakeMyTrip iCal or Google Calendar integrations can import normalized exclusive-end ranges with source/external-event identifiers and sync state, or add another provider at `availabilityService()`. Neither guest UI nor enquiry logic needs a source-specific change.

Import deduplication, cancellation handling, freshness/failure policy, scheduled jobs, timezone normalization and Google OAuth are intentionally future work. No external source is fetched now.

## Verification

```text
php tools/check-stay.php
php tools/check-experience.php
php tools/check-enquiry.php
php tools/check-guest-guide.php
php tools/check-availability.php
```

For real local MySQL/admin HTTP checks, set `TRIBE_TEST_ADMIN_PASSWORD` only in the test process, then run `php tools/check-availability.php --integration`. Optional `TRIBE_TEST_BASE_URL` defaults to localhost:8080; the integration runner refuses non-local HTTP targets. Use only a local development database. It creates unique-reason future test blocks and removes those fixtures in `finally`. It sends no emails. Pure date/provider/security tests run without MySQL.

Browser review should cover mobile/desktop, keyboard dates, valid/blocked/manual date input, checkout boundaries, admin create/edit/remove, private reasons, outage fallback and restored service. Screenshots and review fixtures belong in ignored `var/availability-review/`, not production assets.
