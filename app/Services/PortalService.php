<?php

namespace App\Services;

use Illuminate\Support\Facades\URL;

class PortalService
{
    /**
     * Generate a signed portal URL for a user
     *
     * @param  string  $userId  User ID
     * @param  int  $hoursValid  Hours until expiration (default: 24)
     * @return string Signed portal URL
     */
    public static function generatePortalUrl(string $userId, int $hoursValid = 24): string
    {
        return URL::temporarySignedRoute(
            'portal',
            now()->addHours($hoursValid),
            ['user' => $userId]
        );
    }

    /**
     * Get iframe embed code for the portal
     *
     * @param  string  $portalUrl  Signed portal URL
     * @param  int  $width  Iframe width in pixels
     * @param  int  $height  Iframe height in pixels
     * @return string HTML iframe code
     */
    public static function getEmbedCode(
        string $portalUrl,
        int $width = 800,
        int $height = 600
    ): string {
        $escapedUrl = htmlspecialchars($portalUrl, ENT_QUOTES, 'UTF-8');

        return sprintf(
            '<iframe src="%s" width="%d" height="%d" frameborder="0" style="border: 1px solid #e5e7eb; border-radius: 8px;"></iframe>',
            $escapedUrl,
            $width,
            $height
        );
    }
}
