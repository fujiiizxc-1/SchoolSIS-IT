<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (PDOException $exception) {
            Log::warning('A database operation failed.', ['sqlstate' => $exception->getCode()]);

            return false;
        });
        $exceptions->render(function (PDOException $exception, Request $request) {
            $message = 'The system could not complete your request. Please try again later.';

            return $request->expectsJson() ? response()->json(['message' => $message], 503) : response()->view('errors.503', [], 503);
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
