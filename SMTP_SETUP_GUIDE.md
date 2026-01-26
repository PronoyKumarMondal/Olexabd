# SMTP Setup for Email Verification

For email verification to work, you must configure your SMTP settings in the `.env` file on your server.

## Google/Gmail SMTP (Easiest)
If you are using Gmail, you need an **App Password**.
1. Go to Google Account > Security.
2. Enable **2-Step Verification**.
3. Search for **"App Passwords"**.
4. Create one named "Laravel".
5. Use that password in `.env`.

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password-here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@olexabd.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Hostinger / cPanel SMTP
If using your business email:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=admin@olexabd.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=ssl
```

After updating `.env`, run:
```bash
php artisan config:clear
```
