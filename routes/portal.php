<?php

use App\Http\Controllers\PortalController;
use App\Http\Controllers\PortalDemoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal Routes
|--------------------------------------------------------------------------
| Stateless portal SPA that uses JWT tokens for authentication.
| The portal is embedded in external apps and uses the public API via SDK.
*/

// Portal entry point - validates signed URL and returns Inertia SPA with JWT token
Route::get('portal', [PortalController::class, 'index'])
    ->name('portal');

// Portal demo - generates a test signed URL for demo user
Route::get('portal-demo', [PortalDemoController::class, 'index'])
    ->name('portal.demo');
