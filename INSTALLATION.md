# PayCan Installation Guide

PayCan is a powerful payment processing platform built with Laravel and Filament. This guide will help you install and configure PayCan on your server.

## 🚀 Quick Installation

### Option 1: Web-based Installer (Recommended)

1. **Download and Extract**
   ```bash
   # Download PayCan
   git clone https://github.com/paycan-app/paycan.git
   cd paycan
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Set Permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

4. **Access Web Installer**
   - Open your browser and navigate to your domain
   - You'll be automatically redirected to `/install`
   - Follow the step-by-step installation wizard

### Option 2: Command Line Installation

1. **Run the Install Command**
   ```bash
   php artisan paycan:install
   ```

2. **Create Admin User**
   ```bash
   php artisan make:filament-user
   ```

3. **Access Admin Panel**
   - Visit `/admin` to access the admin panel

## 📋 System Requirements

### Server Requirements
- **PHP**: 8.2 or higher
- **Database**: SQLite (default), MySQL 8.0+, or PostgreSQL 13+
- **Web Server**: Apache or Nginx
- **Node.js**: 18+ (for building assets)
- **Composer**: Latest version

### PHP Extensions
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- Tokenizer
- XML
- GMP (for license generation)
- Mcrypt (for encryption)

### Directory Permissions
The following directories must be writable:
- `storage/`
- `bootstrap/cache/`
- `database/` (for SQLite)

## 🗄️ Database Configuration

### SQLite (Default - Recommended for Getting Started)
- No additional setup required
- Database file created automatically
- Perfect for development and small deployments

### MySQL
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=paycan
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### PostgreSQL
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=paycan
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 🔧 Manual Installation

If you prefer to install manually:

1. **Clone Repository**
   ```bash
   git clone https://github.com/paycan-app/paycan.git
   cd paycan
   ```

2. **Install Dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   # For SQLite (default)
   touch database/database.sqlite
   
   # Run migrations
   php artisan migrate --force
   ```

5. **Storage Setup**
   ```bash
   php artisan storage:link
   ```

6. **Create Admin User**
   ```bash
   php artisan make:filament-user
   ```

7. **Optimize Application**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 🌐 Web Server Configuration

### Apache
Create a `.htaccess` file in your document root:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/paycan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🔐 Security Considerations

1. **Environment File**
   - Ensure `.env` is not accessible via web
   - Use strong, unique `APP_KEY`

2. **Database Security**
   - Use strong database passwords
   - Restrict database access to application only

3. **File Permissions**
   - Set appropriate file permissions (755 for directories, 644 for files)
   - Ensure web server can't write to application files

4. **HTTPS**
   - Always use HTTPS in production
   - Configure SSL certificates

## 📧 Email Configuration

PayCan supports multiple email providers:

### SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain
MAILGUN_SECRET=your-secret
```

### Other Providers
- Amazon SES
- Postmark
- Resend
- Sendmail

Configure these through the admin panel at `/admin/settings/mail`.

## 🎯 Post-Installation Steps

1. **Access Admin Panel**
   - Visit `/admin`
   - Login with your admin credentials

2. **Configure Payment Providers**
   - Go to Settings → Payment Providers
   - Configure Stripe, PayPal, or other providers

3. **Set Up Email**
   - Go to Settings → Email
   - Configure your email provider

4. **Customize Application**
   - Update application settings
   - Configure notifications

5. **Set Up Webhooks (required for payments to complete)**
   - Stripe: create a webhook endpoint pointing to `https://your-domain/api/webhooks/stripe`
     (events: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`,
     `customer.subscription.*`, `invoice.payment_succeeded`, `invoice.payment_failed`) and paste the
     signing secret into Settings → Stripe → Webhook Secret. Signature verification is always
     enforced — for local development use `stripe listen --forward-to localhost:8000/api/webhooks/stripe`
     and use the secret it prints.
   - PayPal: create a webhook for `https://your-domain/api/webhooks/paypal`
     (events: `CHECKOUT.ORDER.APPROVED`, `PAYMENT.CAPTURE.COMPLETED`, `PAYMENT.CAPTURE.DENIED`,
     `PAYMENT.SALE.COMPLETED`, `BILLING.SUBSCRIPTION.*`) and paste the Webhook ID into
     Settings → PayPal → Webhook ID.

6. **Tax**
   - PayCan does not calculate tax; the payment gateway does. Enable
     **Automatic Tax (Stripe Tax)** in Settings → Stripe (requires Stripe Tax set up in your
     Stripe dashboard), or configure tax collection in your PayPal account.

## 🔄 Updates

To update PayCan:

1. **Backup Database**
   ```bash
   # For SQLite
   cp database/database.sqlite database/database.sqlite.backup
   
   # For MySQL/PostgreSQL
   # Use your preferred backup method
   ```

2. **Update Code**
   ```bash
   git pull origin main
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Clear Caches**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

## 🆘 Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   chmod -R 755 storage bootstrap/cache
   ```

2. **Database Connection Issues**
   - Check database credentials in `.env`
   - Ensure database server is running
   - Verify database exists

3. **Email Not Working**
   - Check email configuration in admin panel
   - Verify SMTP credentials
   - Check server firewall settings

4. **Assets Not Loading**
   ```bash
   npm run build
   php artisan storage:link
   ```

### Getting Help

- Check the [documentation](https://docs.paycan.com)
- Visit our [support forum](https://forum.paycan.com)
- Contact support at support@paycan.com

## 📝 License

PayCan is licensed under the [MIT License](LICENSE).