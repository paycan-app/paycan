<?php

namespace App\Http\Middleware;

use App\Helpers\InstallationHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip installation check for install routes
        if ($request->is('install*')) {
            return $next($request);
        }

        // Check if app is installed
        if (!InstallationHelper::isInstalled()) {
            return redirect()->route('install.welcome');
        }

        return $next($request);
    }
}