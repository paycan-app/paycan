<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Portal Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the embedded payment portal
    |
    */

    /**
     * Allowed domains for iframe embedding
     *
     * Set to '*' to allow all domains (less secure)
     * Or provide an array of allowed domains: ['https://example.com', 'https://app.example.com']
     */
    'allowed_iframe_domains' => env('PORTAL_ALLOWED_DOMAINS', '*'),

    /**
     * Portal link expiration in hours
     *
     * How long signed portal URLs remain valid
     */
    'link_expiration_hours' => env('PORTAL_LINK_EXPIRATION_HOURS', 24),

    /**
     * Rate limiting for portal routes
     *
     * Number of requests per minute
     */
    'rate_limit' => env('PORTAL_RATE_LIMIT', 120),

];
