<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailTest extends Command
{
    protected $signature = 'mail:test
                            {to : Recipient email address}
                            {--mailer= : Mailer to use (default uses config mail.default)}';

    protected $description = 'Send a basic SMTP test email using Laravel Mail';

    public function handle(): int
    {
        $to = (string) $this->argument('to');
        $mailer = (string) ($this->option('mailer') ?: config('mail.default', 'smtp'));

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');

            return self::FAILURE;
        }

        if ($mailer === 'smtp') {
            $smtp = (array) config('mail.mailers.smtp', []);
            $username = (string) ($smtp['username'] ?? '');
            $password = (string) ($smtp['password'] ?? '');

            if ($username === '' || $password === '' || str_contains($username, 'YOUR_MAILTRAP_') || str_contains($password, 'YOUR_MAILTRAP_')) {
                $this->error('SMTP credentials are not configured. Update MAIL_USERNAME and MAIL_PASSWORD in .env first.');

                return self::FAILURE;
            }
        }

        try {
            $fromAddress = (string) (config('mail.from.address') ?: 'no-reply@houroflight.com');
            $fromName = (string) (config('mail.from.name') ?: 'Hour of Light');

            Mail::mailer($mailer)->raw(
                "This is a test email from Hour of Light.\n\nSent at: ".now()->toDateTimeString(),
                function ($message) use ($to, $fromAddress, $fromName): void {
                    $message
                        ->from($fromAddress, $fromName)
                        ->to($to)
                        ->subject('SMTP Test Email - Hour of Light');
                }
            );

            $this->info("Test email sent successfully to {$to} using mailer [{$mailer}].");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to send test email: '.$e->getMessage());
            Log::error('mail:test failed', [
                'mailer' => $mailer,
                'to' => $to,
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->line('Check storage/logs/laravel.log for the full stack trace.');

            return self::FAILURE;
        }
    }
}
