<?php

use App\Http\Middleware\AdminAuthenticated;
use App\Http\Middleware\StudentAuthenticated;
use App\Http\Middleware\TeacherAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => AdminAuthenticated::class,
            'teacher.auth' => TeacherAuthenticated::class,
            'student.auth' => StudentAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
