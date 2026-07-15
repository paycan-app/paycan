-- Migration Script: Flatten Settings Group Structure
-- This script migrates from nested groups to flat service-based groups
-- Run this AFTER deploying the new settings providers

-- ============================================================================
-- EMAIL SETTINGS MIGRATION
-- ============================================================================

-- Migrate general email config from 'mail.*' to 'email.*'
UPDATE settings SET key = 'email.default_provider' WHERE key = 'mail.mailer';
UPDATE settings SET key = 'email.from_address' WHERE key = 'mail.from_address';
UPDATE settings SET key = 'email.from_name' WHERE key = 'mail.from_name';

-- Migrate SMTP settings from 'mail.*' to 'smtp.*'
UPDATE settings SET key = 'smtp.host' WHERE key = 'mail.host';
UPDATE settings SET key = 'smtp.port' WHERE key = 'mail.port';
UPDATE settings SET key = 'smtp.encryption' WHERE key = 'mail.encryption';
UPDATE settings SET key = 'smtp.username' WHERE key = 'mail.username';
UPDATE settings SET key = 'smtp.password' WHERE key = 'mail.password';

-- Migrate Mailgun settings from 'mail.mailgun_*' to 'mailgun.*'
UPDATE settings SET key = 'mailgun.domain' WHERE key = 'mail.mailgun_domain';
UPDATE settings SET key = 'mailgun.api_key' WHERE key = 'mail.mailgun_secret';
UPDATE settings SET key = 'mailgun.endpoint' WHERE key = 'mail.mailgun_endpoint';

-- Migrate Postmark settings from 'mail.postmark_*' to 'postmark.*'
UPDATE settings SET key = 'postmark.token' WHERE key = 'mail.postmark_token';
UPDATE settings SET key = 'postmark.stream_id' WHERE key = 'mail.postmark_message_stream_id';

-- Migrate SES settings from 'mail.ses_*' to 'ses.*'
UPDATE settings SET key = 'ses.access_key' WHERE key = 'mail.ses_key';
UPDATE settings SET key = 'ses.secret_key' WHERE key = 'mail.ses_secret';
UPDATE settings SET key = 'ses.region' WHERE key = 'mail.ses_region';
UPDATE settings SET key = 'ses.configuration_set' WHERE key = 'mail.ses_configuration_set';

-- Migrate Resend settings from 'mail.resend_*' to 'resend.*'
UPDATE settings SET key = 'resend.api_key' WHERE key = 'mail.resend_key';

-- Migrate Sendmail settings from 'mail.sendmail_*' to 'sendmail.*'
UPDATE settings SET key = 'sendmail.path' WHERE key = 'mail.sendmail_path';

-- ============================================================================
-- SOCIAL AUTH SETTINGS MIGRATION
-- ============================================================================

-- Migrate Google OAuth from 'social.google_*' to 'google.*'
UPDATE settings SET key = 'google.enabled' WHERE key = 'social.google_enabled';
UPDATE settings SET key = 'google.client_id' WHERE key = 'social.google_client_id';
UPDATE settings SET key = 'google.client_secret' WHERE key = 'social.google_client_secret';
UPDATE settings SET key = 'google.redirect' WHERE key = 'social.google_redirect';

-- Migrate Facebook OAuth from 'social.facebook_*' to 'facebook.*'
UPDATE settings SET key = 'facebook.enabled' WHERE key = 'social.facebook_enabled';
UPDATE settings SET key = 'facebook.client_id' WHERE key = 'social.facebook_client_id';
UPDATE settings SET key = 'facebook.client_secret' WHERE key = 'social.facebook_client_secret';
UPDATE settings SET key = 'facebook.redirect' WHERE key = 'social.facebook_redirect';

-- Migrate GitHub OAuth from 'social.github_*' to 'github.*'
UPDATE settings SET key = 'github.enabled' WHERE key = 'social.github_enabled';
UPDATE settings SET key = 'github.client_id' WHERE key = 'social.github_client_id';
UPDATE settings SET key = 'github.client_secret' WHERE key = 'social.github_client_secret';
UPDATE settings SET key = 'github.redirect' WHERE key = 'social.github_redirect';

-- ============================================================================
-- FULFILLMENT SETTINGS MIGRATION (if old structure exists)
-- ============================================================================

-- Migrate Downloader from 'fulfillment_providers.downloader_*' to 'downloader.*'
UPDATE settings SET key = 'downloader.enabled' WHERE key = 'fulfillment_providers.downloader_enabled';
UPDATE settings SET key = 'downloader.link_expiry' WHERE key = 'fulfillment_providers.downloader_link_expiry';
UPDATE settings SET key = 'downloader.max_downloads' WHERE key = 'fulfillment_providers.downloader_max_downloads';

-- Migrate License Generator from 'fulfillment_providers.license_generator_*' to 'license_generator.*'
UPDATE settings SET key = 'license_generator.enabled' WHERE key = 'fulfillment_providers.license_generator_enabled';
UPDATE settings SET key = 'license_generator.key_length' WHERE key = 'fulfillment_providers.license_generator_key_length';
UPDATE settings SET key = 'license_generator.prefix' WHERE key = 'fulfillment_providers.license_generator_prefix';

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Count settings by group (new structure)
-- Run these after migration to verify everything migrated correctly

-- SELECT
--     SUBSTRING_INDEX(key, '.', 1) as group_name,
--     COUNT(*) as setting_count
-- FROM settings
-- GROUP BY group_name
-- ORDER BY group_name;

-- Expected groups after migration:
-- app, stripe, paypal, email, smtp, mailgun, postmark, ses, resend, sendmail,
-- google, facebook, github, downloader, license_generator, notifications

-- ============================================================================
-- ROLLBACK SCRIPT (if needed)
-- ============================================================================

-- Uncomment and run if you need to rollback the migration

-- -- Email settings rollback
-- UPDATE settings SET key = 'mail.mailer' WHERE key = 'email.default_provider';
-- UPDATE settings SET key = 'mail.from_address' WHERE key = 'email.from_address';
-- UPDATE settings SET key = 'mail.from_name' WHERE key = 'email.from_name';
-- UPDATE settings SET key = 'mail.host' WHERE key = 'smtp.host';
-- UPDATE settings SET key = 'mail.port' WHERE key = 'smtp.port';
-- UPDATE settings SET key = 'mail.encryption' WHERE key = 'smtp.encryption';
-- UPDATE settings SET key = 'mail.username' WHERE key = 'smtp.username';
-- UPDATE settings SET key = 'mail.password' WHERE key = 'smtp.password';
-- UPDATE settings SET key = 'mail.mailgun_domain' WHERE key = 'mailgun.domain';
-- UPDATE settings SET key = 'mail.mailgun_secret' WHERE key = 'mailgun.api_key';
-- UPDATE settings SET key = 'mail.mailgun_endpoint' WHERE key = 'mailgun.endpoint';
-- UPDATE settings SET key = 'mail.postmark_token' WHERE key = 'postmark.token';
-- UPDATE settings SET key = 'mail.postmark_message_stream_id' WHERE key = 'postmark.stream_id';
-- UPDATE settings SET key = 'mail.ses_key' WHERE key = 'ses.access_key';
-- UPDATE settings SET key = 'mail.ses_secret' WHERE key = 'ses.secret_key';
-- UPDATE settings SET key = 'mail.ses_region' WHERE key = 'ses.region';
-- UPDATE settings SET key = 'mail.ses_configuration_set' WHERE key = 'ses.configuration_set';
-- UPDATE settings SET key = 'mail.resend_key' WHERE key = 'resend.api_key';
-- UPDATE settings SET key = 'mail.sendmail_path' WHERE key = 'sendmail.path';
--
-- -- Social auth rollback
-- UPDATE settings SET key = 'social.google_enabled' WHERE key = 'google.enabled';
-- UPDATE settings SET key = 'social.google_client_id' WHERE key = 'google.client_id';
-- UPDATE settings SET key = 'social.google_client_secret' WHERE key = 'google.client_secret';
-- UPDATE settings SET key = 'social.google_redirect' WHERE key = 'google.redirect';
-- UPDATE settings SET key = 'social.facebook_enabled' WHERE key = 'facebook.enabled';
-- UPDATE settings SET key = 'social.facebook_client_id' WHERE key = 'facebook.client_id';
-- UPDATE settings SET key = 'social.facebook_client_secret' WHERE key = 'facebook.client_secret';
-- UPDATE settings SET key = 'social.facebook_redirect' WHERE key = 'facebook.redirect';
-- UPDATE settings SET key = 'social.github_enabled' WHERE key = 'github.enabled';
-- UPDATE settings SET key = 'social.github_client_id' WHERE key = 'github.client_id';
-- UPDATE settings SET key = 'social.github_client_secret' WHERE key = 'github.client_secret';
-- UPDATE settings SET key = 'social.github_redirect' WHERE key = 'github.redirect';
