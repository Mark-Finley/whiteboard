<?php

declare(strict_types=1);

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\TeamMiddleware;
use App\Http\Middleware\TriageMiddleware;
use App\Http\Middleware\WardMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'triage' => TriageMiddleware::class,
            'ward' => WardMiddleware::class,
            'team' => TeamMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Default exception handling is sufficient for this internal application.
    })->create();
