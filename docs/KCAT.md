# KCAT in Hour of Light

Anyone with a website account can take KCAT and download their detailed PDF report.
No administrator approval, school assignment, or staff role is required. New signups
arrive at `/kcat`; existing accounts can open KCAT from the website menu.

## Deploy

From the application root, with the normal website environment configured:

```sh
php composer.phar install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=Database\Seeders\KcatPublicTestSeeder --force
php artisan vendor:publish --tag=kcat-assets --force
php artisan view:clear
npm ci
npm run build
```

The public-test seeder creates one active 40-question assessment, with ten questions
per reasoning area and a mix of difficulties. It is safe to repeat: it leaves an
existing public test and its results unchanged. The question bank and private school
tests are not exposed through self-service. Use the normal database backup process
before deploying migrations.

Do not run the general DatabaseSeeder to install KCAT: it contains an unrelated
admin account seeder that resets a password.

## Participant flow

1. Sign up, or sign in with an existing account, and open **KCAT**.
2. Click **Start KCAT**. The website creates the participant record and assignment.
3. Answer the questions and submit. An unfinished test can be resumed.
4. Read the detailed report and choose **Download detailed report (PDF)**.
5. Reopen previous reports from **My tests and reports** on the KCAT page.

The report includes overall marks, section percentages, correct/incorrect/unanswered
counts, suggested practice, and stream matches. It is available immediately after
submission. Access is restricted to the participant's own completed attempts. PDF
responses are private and are not written to public storage. Private counselor notes
are excluded; only a summary explicitly shared with the student can appear.

Starting the same public test again resumes the existing attempt or opens its result.
The public assessment is designed for Grades 7–12. Its suggested duration is not a
server-enforced timer. Stream suggestions retain the transferred scoring logic.

## Staff features

Website admins can manage assessments. `/admin/kcat/access` is optional, for granting
counselor/principal roles and organizing participants into classes. Removing an
assigned role does not remove public testing or personal report access.

School-assigned fixed and adaptive assessments remain available. Staff can populate
a separate question bank with `php artisan kcat:generate-question-bank` and use it to
create additional assessments. The public test is identified by `self_service_key`;
ordinary tests and banks remain private unless deliberately configured for public use.

## Verification and local development

The package is under `packages/nsms/kcat`, mirrored into `vendor`. After changing
package source, run `php composer.phar reinstall nsms/kcat` and clear compiled views.
PHP 8.2+ and Laravel 11 are supported. Both suites use in-memory SQLite:

```powershell
php vendor/phpunit/phpunit/phpunit
$env:KCAT_VENDOR_AUTOLOAD = (Resolve-Path 'vendor/autoload.php').Path
php vendor/phpunit/phpunit/phpunit -c packages/nsms/kcat/phpunit.xml
```

The transfer contains source, sample questions, and a generator. Historical records,
custom question records, and uploaded images from the original website were not in
the ZIP and have not been imported.