<?php

return [
    // Success messages
    'saved' => [
        'title' => 'Settings saved successfully',
        'body' => 'Your settings have been updated.',
    ],

    // App settings
    'app' => [
        'general' => [
            'label' => 'General',
            'description' => 'Basic application settings',
        ],
        'api' => [
            'label' => 'API Secret Key',
            'description' => 'Manage API secret key for external integrations.',
        ],
        'name' => [
            'label' => 'Application Name',
            'helper' => 'The name of your application',
        ],
        'url' => [
            'label' => 'Application URL',
            'helper' => 'The base URL for your application',
        ],
        'timezone' => [
            'label' => 'Timezone',
            'helper' => 'Application timezone (e.g., UTC, America/New_York)',
        ],
        'locale' => [
            'label' => 'Default Language',
            'helper' => 'Default language for all users. Users can override this with their own preference.',
        ],
        'api_key' => [
            'label' => 'API Secret Key',
            'helper' => 'API secret key for external integrations',
        ],
    ],

    // Payment gateway settings
    'stripe' => [
        'label' => 'Stripe Payment Gateway',
        'description' => 'Configure your Stripe payment gateway settings',
        'enabled' => [
            'label' => 'Enable Stripe',
            'helper' => 'Toggle to enable or disable Stripe payments',
        ],
        'api_key' => [
            'label' => 'Secret Key',
            'helper' => 'Your Stripe secret key (encrypted and never shown publicly)',
        ],
        'publishable_key' => [
            'label' => 'Publishable Key',
            'helper' => 'Your Stripe publishable key',
        ],
        'enable_subscriptions' => [
            'label' => 'Enable Subscription Support',
            'helper' => 'Allow this gateway to process recurring subscription payments',
        ],
        'webhook_secret' => [
            'label' => 'Webhook Secret',
            'helper' => 'Webhook signing secret for verifying Stripe events',
        ],
    ],

    'paypal' => [
        'label' => 'PayPal Payment Gateway',
        'description' => 'Configure your PayPal payment gateway settings',
        'enabled' => [
            'label' => 'Enable PayPal',
            'helper' => 'Toggle to enable or disable PayPal payments',
        ],
        'client_id' => [
            'label' => 'Client ID',
            'helper' => 'Your PayPal application client ID',
        ],
        'secret' => [
            'label' => 'Client Secret',
            'helper' => 'Your PayPal application secret (encrypted)',
        ],
        'mode' => [
            'label' => 'Mode',
            'helper' => 'Select sandbox for testing or live for production',
            'options' => [
                'sandbox' => 'Sandbox (Testing)',
                'live' => 'Live (Production)',
            ],
        ],
        'enable_subscriptions' => [
            'label' => 'Enable Subscription Support',
            'helper' => 'Allow this gateway to process recurring subscription payments',
        ],
        'webhook_id' => [
            'label' => 'Webhook ID',
            'helper' => 'PayPal webhook ID for event verification',
        ],
    ],

    // Email settings
    'email' => [
        'label' => 'Email Configuration',
        'description' => 'Configure email delivery settings',
        'default_provider' => [
            'label' => 'Default Email Provider',
            'helper' => 'Select which email service to use for sending emails',
        ],
        'from_address' => [
            'label' => 'From Email Address',
            'helper' => 'Email address that emails will be sent from',
        ],
        'from_name' => [
            'label' => 'From Name',
            'helper' => 'Name that will appear in the "From" field',
        ],
    ],

    'smtp' => [
        'label' => 'SMTP',
        'description' => 'Configure SMTP server settings for direct email delivery',
        'host' => [
            'label' => 'SMTP Host',
            'helper' => 'Your SMTP server hostname',
        ],
        'port' => [
            'label' => 'SMTP Port',
            'helper' => 'SMTP server port (usually 587 for TLS or 465 for SSL)',
        ],
        'encryption' => [
            'label' => 'Encryption',
            'helper' => 'Encryption method',
            'options' => [
                'none' => 'None',
            ],
        ],
        'username' => [
            'label' => 'SMTP Username',
            'helper' => 'SMTP authentication username',
        ],
        'password' => [
            'label' => 'SMTP Password',
            'helper' => 'SMTP authentication password (encrypted)',
        ],
    ],

    'mailgun' => [
        'label' => 'Mailgun',
        'description' => 'Configure Mailgun email service',
        'domain' => [
            'label' => 'Mailgun Domain',
            'helper' => 'Your Mailgun domain',
        ],
        'secret' => [
            'label' => 'API Key',
            'helper' => 'Your Mailgun API key (encrypted)',
        ],
        'endpoint' => [
            'label' => 'Endpoint',
            'helper' => 'Select your Mailgun region',
            'options' => [
                'us' => 'US Region',
                'eu' => 'EU Region',
            ],
        ],
    ],

    'postmark' => [
        'label' => 'Postmark',
        'description' => 'Configure Postmark email service',
        'token' => [
            'label' => 'Server Token',
            'helper' => 'Your Postmark server API token (encrypted)',
        ],
        'message_stream_id' => [
            'label' => 'Message Stream ID',
            'helper' => 'Message stream identifier (usually "outbound")',
        ],
    ],

    'ses' => [
        'label' => 'Amazon SES',
        'description' => 'Configure Amazon Simple Email Service',
        'key' => [
            'label' => 'Access Key ID',
            'helper' => 'Your AWS access key ID',
        ],
        'secret' => [
            'label' => 'Secret Access Key',
            'helper' => 'Your AWS secret access key (encrypted)',
        ],
        'region' => [
            'label' => 'AWS Region',
            'helper' => 'The AWS region where your SES is configured',
        ],
    ],

    'resend' => [
        'label' => 'Resend',
        'description' => 'Configure Resend email service',
        'key' => [
            'label' => 'API Key',
            'helper' => 'Your Resend API key (encrypted)',
        ],
    ],

    'sendmail' => [
        'label' => 'Sendmail',
        'description' => 'Configure Sendmail for local email delivery',
        'path' => [
            'label' => 'Sendmail Path',
            'helper' => 'Path to the sendmail binary',
        ],
    ],

    // Social authentication
    'google' => [
        'label' => 'Google OAuth',
        'description' => 'Configure Google social authentication',
        'enabled' => [
            'label' => 'Enable Google Login',
            'helper' => 'Allow users to sign in with their Google account',
        ],
        'client_id' => [
            'label' => 'Client ID',
            'helper' => 'Your Google OAuth client ID',
        ],
        'client_secret' => [
            'label' => 'Client Secret',
            'helper' => 'Your Google OAuth client secret (encrypted)',
        ],
        'redirect' => [
            'label' => 'Redirect URL',
            'helper' => 'OAuth redirect URL (configure this in Google Console)',
        ],
    ],

    'facebook' => [
        'label' => 'Facebook OAuth',
        'description' => 'Configure Facebook social authentication',
        'enabled' => [
            'label' => 'Enable Facebook Login',
            'helper' => 'Allow users to sign in with their Facebook account',
        ],
        'client_id' => [
            'label' => 'App ID',
            'helper' => 'Your Facebook App ID',
        ],
        'client_secret' => [
            'label' => 'App Secret',
            'helper' => 'Your Facebook App Secret (encrypted)',
        ],
        'redirect' => [
            'label' => 'Redirect URL',
            'helper' => 'OAuth redirect URL (configure this in Facebook Developer Console)',
        ],
    ],

    'github' => [
        'label' => 'GitHub OAuth',
        'description' => 'Configure GitHub social authentication',
        'enabled' => [
            'label' => 'Enable GitHub Login',
            'helper' => 'Allow users to sign in with their GitHub account',
        ],
        'client_id' => [
            'label' => 'Client ID',
            'helper' => 'Your GitHub OAuth client ID',
        ],
        'client_secret' => [
            'label' => 'Client Secret',
            'helper' => 'Your GitHub OAuth client secret (encrypted)',
        ],
        'redirect' => [
            'label' => 'Redirect URL',
            'helper' => 'OAuth redirect URL (configure this in GitHub Developer Settings)',
        ],
    ],

    // Fulfillment providers
    'downloader' => [
        'label' => 'File Downloader',
        'description' => 'Configure file download fulfillment settings',
        'enabled' => [
            'label' => 'Enable File Downloads',
            'helper' => 'Allow products to be fulfilled via file downloads',
        ],
        'link_expiry' => [
            'label' => 'Link Expiry (hours)',
            'helper' => 'How many hours download links remain valid',
        ],
        'max_downloads' => [
            'label' => 'Maximum Downloads',
            'helper' => 'Maximum number of times a file can be downloaded',
        ],
        'require_authentication' => [
            'label' => 'Require Authentication',
            'helper' => 'Users must be logged in to download files',
        ],
    ],

    'license_generator' => [
        'label' => 'License Generator',
        'description' => 'Configure license key generation settings',
        'enabled' => [
            'label' => 'Enable License Generation',
            'helper' => 'Allow products to be fulfilled via license keys',
        ],
        'prefix' => [
            'label' => 'License Prefix',
            'helper' => 'Prefix added to all generated license keys',
        ],
        'length' => [
            'label' => 'License Length',
            'helper' => 'Total length of the license key (including prefix)',
        ],
        'format' => [
            'label' => 'Format',
            'helper' => 'Character types to use in license keys',
            'options' => [
                'alphanumeric' => 'Alphanumeric (A-Z, 0-9)',
                'numeric' => 'Numeric only (0-9)',
                'alphabetic' => 'Alphabetic only (A-Z)',
            ],
        ],
        'separator' => [
            'label' => 'Separator',
            'helper' => 'Character used to separate license key segments',
            'options' => [
                'dash' => 'Dash (-)',
                'underscore' => 'Underscore (_)',
                'none' => 'None',
            ],
        ],
    ],

    // Notification settings
    'notifications' => [
        'label' => 'Notifications & Email Templates',
        'description' => 'Configure admin notification settings',
        'admin_email' => [
            'label' => 'Admin Email',
            'helper' => 'Email address for admin notifications',
        ],
        'notify_admin_new_order' => [
            'label' => 'New Order Notifications',
            'helper' => 'Notify admin when new orders are placed',
        ],
        'notify_admin_failed_payment' => [
            'label' => 'Failed Payment Notifications',
            'helper' => 'Notify admin when payments fail',
        ],
    ],
];
