<?php

use App\Console\Commands\MailTest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'volunteer_teacher' => \App\Http\Middleware\VolunteerTeacherMiddleware::class,
            'volunteer.teacher' => \App\Http\Middleware\VolunteerTeacherMiddleware::class,
        ]);
    })
    ->withCommands([
        MailTest::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
