# KCAT Laravel package

This copy is integrated into Hour of Light; see the host's `docs/KCAT.md` first.
It supports PHP 8.2+ and Laravel 11. The host configures existing admins as KCAT managers,
registers migrations automatically, and supplies web-based access management.
The original standalone installation notes below remain available for reference.

## 1. Register the package

Run from the OTHER website's root:

```sh
composer config repositories.kcat path packages/nsms/kcat
composer require nsms/kcat:1.0.0
php artisan vendor:publish --tag=kcat-assets
php artisan migrate --path=packages/nsms/kcat/database/migrations
```

Laravel discovers `Nsms\Kcat\KcatServiceProvider` through Composer. If the website disables package discovery, add that provider to its `bootstrap/providers.php` array.

The migrations create only KCAT tables, including `kcat_access`, `kcat_students` and `kcat_school_classes`. They reference the existing `users` table but do not add columns or change passwords or website roles. These are first-install migrations: do not run them over pre-existing `kcat_*` tables from another implementation. Back up the destination database before its normal deployment migration process.

The package does not run or require the NSMS school migrations. It does not automatically seed demo data.

## 2. Grant module access and link students

Use EXISTING account emails from the destination website:

```sh
php artisan kcat:grant counselor@example.com counselor
php artisan kcat:grant principal@example.com principal
php artisan kcat:student student@example.com STU001 --class="Class 8" --section="A"
php artisan kcat:grant student@example.com student
```

Replace the example emails and student ID. The link command prints the KCAT student database ID used by manual entry. Repeat it for each student. The package links by `user_id`; editing an email or name does not transfer ownership to another student's records.

Each account has one KCAT module role. `kcat:grant email none` revokes KCAT access. These commands do not create login accounts, reset passwords, or change the host site's role system. Use the host site's account registration/administration for those operations.

## 3. Add questions

For the original small sample test:

```sh
php artisan db:seed --class="Nsms\Kcat\Seeders\KcatDefaultSeeder"
```

Alternatively, use the original larger question-bank generator:

```sh
php artisan kcat:generate-question-bank --dry-run
php artisan kcat:generate-question-bank
```

The first command previews generation; the second inserts the generated bank. The seeded sample is a draft. Log in as the counselor, open **Tests**, review the questions, activate the test, and create an assignment for a linked student or class. The original Grade 7–12 eligibility check is retained for individual student assignments.

Database-stored custom questions from the old website are not included. The original database was unavailable. No student information, credentials, existing assignments or results are included in this package. Any future data migration needs explicit mapping to the destination `kcat_students` and `kcat_school_classes` IDs; do not import a full NSMS SQL dump.

## 4. Open the module

| Account | URL |
| --- | --- |
| Counselor | `/kcat/counselor/dashboard` |
| Student | `/kcat/student/assignments` |
| Principal | `/kcat/principal/reports` |

Paste this into a destination Blade navigation template for a student link:

```blade
@can('kcat.attempt_kcat_test')
    <a href="{{ route('kcat.student.kcat.assignments.index') }}">Take KCAT Test</a>
@endcan
```

For a counselor link:

```blade
@can('kcat.view_kcat_panel')
    <a href="{{ route('kcat.career-counselor.kcat.dashboard') }}">Manage KCAT</a>
@endcan
```

All pages use the destination login/session and Laravel CSRF protection. Access is checked on the server; students are restricted to their linked identity and own attempts. The package has its own navigation and does not load the NSMS sidebar or JS application. It includes precompiled Tailwind CSS, so no Node build is needed to install it.

To change the URL prefix, publish `php artisan vendor:publish --tag=kcat-config`, then edit `config/kcat.php`. Route helper names remain the same. Clear/rebuild the destination's route/config caches using its normal deployment process.

## Included behavior and deliberate differences

- Copied engines: verbal, quantitative, non-verbal and spatial questions; generated visual assets; fixed/adaptive attempts; scoring; stream suggestions; counselor notes; question quality; printable reports; principal analytics; completed-result deletion.
- Laravel classes, Blade view names and route names are namespaced to prevent collisions with the destination application.
- KCAT student/class records and access grants are separate from NSMS student records and Spatie roles.
- The original Career Profile attachment integration is omitted because it requires the separate school career-counseling module. It remains available in the untouched source snapshot for reference.
- The export corrects signed elapsed-time calculation and keeps the final fixed-test question/Submit button reachable after saving the last answer. These changes exist only in the export.
- The package retains the original scoring and interpretation logic.
- The fixed-test duration setting is metadata; this export does not add a new server-enforced examination timer.

## Run the isolated tests

The destination must have PHPUnit 11 available as a development dependency. The tests bootstrap a separate Laravel application under `tests/work` and use only in-memory SQLite, not the destination database.

PowerShell, from the destination root:

```powershell
$env:KCAT_VENDOR_AUTOLOAD = (Resolve-Path 'vendor/autoload.php').Path
php vendor/phpunit/phpunit/phpunit -c packages/nsms/kcat/phpunit.xml
```

Linux/macOS:

```sh
KCAT_VENDOR_AUTOLOAD="$PWD/vendor/autoload.php" php vendor/phpunit/phpunit/phpunit -c packages/nsms/kcat/phpunit.xml
```

Do not run the extraction helper from the source export against the destination. Only `package/` is installed.
