<?php
/**
 * Quick script to check if Daraja credentials are configured
 * Run: php check-daraja-config.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Daraja Configuration Check ===\n\n";

$consumerKey = config('services.daraja.consumer_key');
$consumerSecret = config('services.daraja.consumer_secret');
$shortCode = config('services.daraja.shortcode');
$passkey = config('services.daraja.passkey');
$baseUrl = config('services.daraja.base_url');
$callbackUrl = config('services.daraja.callback_url');

echo "Consumer Key: " . ($consumerKey ? "✓ Set (" . substr($consumerKey, 0, 10) . "...)" : "✗ NOT SET") . "\n";
echo "Consumer Secret: " . ($consumerSecret ? "✓ Set (" . substr($consumerSecret, 0, 10) . "...)" : "✗ NOT SET") . "\n";
echo "Shortcode: " . ($shortCode ? "✓ Set ($shortCode)" : "✗ NOT SET") . "\n";
echo "Passkey: " . ($passkey ? "✓ Set (" . substr($passkey, 0, 10) . "...)" : "✗ NOT SET") . "\n";
echo "Base URL: " . ($baseUrl ?: "Not set") . "\n";
echo "Callback URL: " . ($callbackUrl ?: "Not set") . "\n\n";

if (empty($consumerKey) || empty($consumerSecret) || empty($shortCode) || empty($passkey)) {
    echo "❌ ERROR: Missing required Daraja credentials!\n\n";
    echo "Please add these to your .env file:\n";
    echo "DARAJA_CONSUMER_KEY=your_key_here\n";
    echo "DARAJA_CONSUMER_SECRET=your_secret_here\n";
    echo "DARAJA_SHORTCODE=your_shortcode_here\n";
    echo "DARAJA_PASSKEY=your_passkey_here\n";
    echo "\nThen run: php artisan config:clear\n";
    exit(1);
} else {
    echo "✓ All Daraja credentials are configured!\n";
    exit(0);
}

