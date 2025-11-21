# Online Payment Gateway Setup Guide

## Stripe Configuration

This system uses Stripe as the payment gateway. Follow these steps to set it up:

### 1. Get Stripe API Keys

1. Sign up for a Stripe account at https://stripe.com
2. Go to Developers > API keys
3. Copy your **Publishable key** and **Secret key**
4. For webhooks, go to Developers > Webhooks and create an endpoint

### 2. Configure Environment Variables

Add these to your `.env` file:

```env
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

**For Production:**
- Use `pk_live_...` and `sk_live_...` keys
- Update webhook endpoint to production URL

### 3. Webhook Setup

1. In Stripe Dashboard, go to Developers > Webhooks
2. Click "Add endpoint"
3. Enter your webhook URL: `https://yourdomain.com/payments/webhook`
4. Select events to listen to:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
5. Copy the webhook signing secret to `.env`

### 4. Testing

**Test Mode:**
- Use test API keys (pk_test_... and sk_test_...)
- Use test card numbers from Stripe documentation
- Example test card: `4242 4242 4242 4242` (any future expiry, any CVC)

**Test Cards:**
- Success: `4242 4242 4242 4242`
- Decline: `4000 0000 0000 0002`
- Requires 3D Secure: `4000 0025 0000 3155`

### 5. Currency Support

The system is configured for Kenyan Shilling (KES). Stripe supports KES natively.

To change currency:
1. Update `currency` field in `PaymentController.php`
2. Update default currency in `PaymentTransaction` model
3. Update currency display in views

## Features

✅ Secure payment processing with Stripe
✅ Payment receipt generation (PDF)
✅ Transaction tracking and history
✅ Automatic fee balance updates
✅ Payment notifications
✅ Webhook support for payment confirmations
✅ Support for multiple payment methods (cards, etc.)

## Usage

1. Navigate to Fees Collections
2. Select a student
3. Click "Pay Online" button
4. Enter payment amount
5. Complete payment on Stripe checkout
6. Receive receipt automatically

## Security Notes

- Never commit API keys to version control
- Use environment variables for all sensitive data
- Enable webhook signature verification
- Use HTTPS in production
- Regularly rotate API keys

