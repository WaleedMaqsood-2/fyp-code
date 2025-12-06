<?php

namespace App\Http;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
class Kernel extends HttpKernel
{
   // app/Http/Kernel.php
protected $routeMiddleware = [
    // ... other middleware
    'notification.security' => \App\Http\Middleware\NotificationSecurity::class,
    'notification.rate' => \App\Http\Middleware\NotificationRateLimit::class,
];
}