# How to Add Daraja Credentials to .env File

## Step 1: Open or Create .env File

The `.env` file is located in the root directory of your project:
```
C:\Users\travs\Downloads\school-management-system\.env
```

If the file doesn't exist, copy `.env.example` to `.env`:
```bash
copy .env.example .env
```

## Step 2: Add Daraja Credentials

Open the `.env` file and add these lines at the end:

```env
# M-Pesa Daraja API Configuration
DARAJA_CONSUMER_KEY=your_consumer_key_here
DARAJA_CONSUMER_SECRET=your_consumer_secret_here
DARAJA_SHORTCODE=your_shortcode_here
DARAJA_PASSKEY=your_passkey_here
DARAJA_BASE_URL=https://sandbox.safaricom.co.ke
DARAJA_CALLBACK_URL=https://yourdomain.com/payments/daraja/callback
DARAJA_ENVIRONMENT=sandbox
```

## Step 3: Replace with Your Actual Credentials

### For Sandbox (Testing):
1. Go to https://developer.safaricom.co.ke
2. Sign up/Login
3. Go to "My Apps" → Create an app or select existing app
4. Copy the credentials:
   - **Consumer Key**: Found in your app details
   - **Consumer Secret**: Found in your app details
   - **Shortcode**: Test credentials (usually provided in sandbox)
   - **Passkey**: Test credentials (usually provided in sandbox)

### Example .env entries:
```env
DARAJA_CONSUMER_KEY=abc123xyz456def789
DARAJA_CONSUMER_SECRET=xyz789abc123def456
DARAJA_SHORTCODE=174379
DARAJA_PASSKEY=bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919
DARAJA_BASE_URL=https://sandbox.safaricom.co.ke
DARAJA_CALLBACK_URL=http://localhost:8000/payments/daraja/callback
DARAJA_ENVIRONMENT=sandbox
```

## Step 4: For Local Development

If testing locally, you need to make your callback URL publicly accessible:

### Option 1: Use ngrok
```bash
ngrok http 8000
```
Then update `DARAJA_CALLBACK_URL` to the ngrok URL:
```env
DARAJA_CALLBACK_URL=https://your-ngrok-url.ngrok.io/payments/daraja/callback
```

### Option 2: Use Laravel Valet or similar
Make sure your local site is accessible via HTTPS.

## Step 5: Clear Config Cache

After adding credentials, clear Laravel's config cache:
```bash
php artisan config:clear
php artisan cache:clear
```

## Step 6: Verify Configuration

You can verify your configuration is loaded by checking:
```bash
php artisan tinker
```
Then run:
```php
config('services.daraja.consumer_key')
```

## Important Notes:

1. **Never commit .env to version control** - it contains sensitive credentials
2. **Use sandbox credentials for testing** - production credentials require approval
3. **Callback URL must be publicly accessible** - M-Pesa needs to reach your server
4. **Use HTTPS in production** - required by Safaricom

## Troubleshooting:

- If credentials don't work, double-check they're correct
- Make sure there are no extra spaces in the .env file
- Restart your Laravel server after changing .env
- Clear config cache: `php artisan config:clear`

