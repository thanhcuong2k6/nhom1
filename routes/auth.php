<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Middleware\Authenticate;

Route::middleware('web')->group(function () {
    // These routes are included from web.php - defining minimal auth routes
    // Login, register, etc. are handled by Laravel Breeze or similar packages
});
