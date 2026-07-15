<?php

namespace App\Http\Middleware;

use App\Services\Settings\SettingsManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(
        protected SettingsManager $settings
    ) {}

    /**
     * Handle an incoming request for both web and admin panels.
     * Priority: User preference (cookie/localStorage) > Admin default setting > Config default
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->getLocale($request);

        if ($this->isValidLocale($locale)) {
            App::setLocale($locale);
        }

        $response = $next($request);

        // If lang parameter is provided, set cookie for localStorage sync
        if ($request->has('lang') && $this->isValidLocale($request->input('lang'))) {
            $locale = $request->input('lang');
            Cookie::queue('locale', $locale, 525600); // 1 year
        }

        return $response;
    }

    /**
     * Get the locale for the current request.
     * Priority: User's saved preference (cookie) > Admin's default setting > App config
     */
    protected function getLocale(Request $request): string
    {
        // 1. Check for query parameter (user changing language)
        if ($request->has('lang')) {
            return $request->input('lang');
        }

        // 2. Check user's saved preference in cookie (synced with localStorage)
        if ($request->hasCookie('locale')) {
            return $request->cookie('locale');
        }

        // 3. Check admin's global default setting from database
        $adminDefault = $this->settings->get('app.locale');
        if ($adminDefault) {
            return $adminDefault;
        }

        // 4. Fallback to application config
        return config('app.locale', 'en');
    }

    /**
     * Check if the locale is valid.
     */
    protected function isValidLocale(string $locale): bool
    {
        return in_array($locale, config('app.supported_locales', ['en', 'ar']));
    }
}
