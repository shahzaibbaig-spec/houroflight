# Deploying Hour of Light with KCAT

The repository must include `packages/nsms/kcat`, `composer.lock`, and
`package-lock.json`. Composer installs KCAT from this local package. Do not
upload local `.env` files, database exports, or replace the live database.

## Existing live website

Back up the live database, `.env`, and uploaded files first. Deploy the reviewed
Git commit using your hosting provider's normal process. Run the following from
the Laravel application directory with PHP 8.2 or later and Composer installed:

```sh
php artisan down
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class='Database\Seeders\KcatPublicTestSeeder' --force
php artisan vendor:publish --tag=kcat-assets --force
npm ci
npm run build
php artisan optimize
php artisan up
```

Stop if any command fails and resolve it before continuing. `artisan up` restores
access after a failed deployment, but only restore access when the installed code
and database are compatible. The public-test seeder creates the 40-question KCAT
assessment and preserves an existing public test and its results on repeat runs.
Do not use the general `db:seed` command: its admin seeder resets a password.

If Node.js is unavailable on the server, run `npm ci` and `npm run build` in your
deployment build environment and deploy the resulting `public/build` directory.
The checked-in `deployment/frontend-build.zip` contains the build for the shared
website and KCAT layout. On the current host, which serves `../public_html`, run:

```sh
composer reinstall nsms/kcat --no-interaction
mkdir -p public/build ../public_html/build
unzip -o deployment/frontend-build.zip -d public/build
unzip -o deployment/frontend-build.zip -d ../public_html/build
php artisan optimize:clear
php artisan optimize
```

After changing frontend source, regenerate this archive from the contents of
`public/build` (with `manifest.json` and `assets` at the archive root).
Remove a stale `public/hot` file if one was uploaded from a development machine.

Preserve the existing `APP_KEY`, database settings, and storage. In the live `.env`,
set `APP_ENV=production`, `APP_DEBUG=false`, and `APP_URL` to the HTTPS website URL.
Do not run `key:generate` on an existing installation.

The web server document root must point to `public/`. The PHP/web-server user must
be able to write to `storage/` and `bootstrap/cache/`. Preserve uploaded files in
`storage/app/public` and run `php artisan storage:link` if the public storage link
has not already been created. Enable HTTPS.

## Fresh installation

Create the server `.env` from `.env.example`, configure the production database
and application URL, and generate an application key once with
`php artisan key:generate` after installing Composer dependencies. Then run the
migrations, KCAT seeder, asset publication, and build steps above. Configure email
delivery if password reset emails are required. Database-backed sessions and cache
require the application migrations to have completed.

## Confirm the deployment

1. Open `/kcat`; the public reasoning assessment should be listed.
2. Register or sign in, choose **Start KCAT**, answer a question, leave, and resume.
3. Complete the assessment and open its results.
4. Download the detailed PDF and verify the scores are present.
5. Confirm another account cannot access that participant's report.

An empty KCAT page usually means the public-test seeder has not run or the test
is inactive. A missing-table error means migrations are incomplete. For a server
error, inspect `storage/logs/laravel.log` while keeping production debug disabled.

See [KCAT details](KCAT.md) for the participant flow and staff features.
