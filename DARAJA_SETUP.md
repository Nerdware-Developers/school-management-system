# M-Pesa Daraja API Setup Guide

## Overview
This system integrates M-Pesa Daraja API for mobile money payments. Follow these steps to set it up.

## Prerequisites
1. Safaricom Developer Account (https://developer.safaricom.co.ke)
2. Daraja API credentials (Consumer Key, Consumer Secret, Shortcode, Passkey)

## Setup Steps

### 1. Register on Safaricom Developer Portal
1. Go to https://developer.safaricom.co.ke
2. Sign up for a developer account
3. Create an app to get your credentials

### 2. Get Your Credentials

**For Sandbox (Testing):**
- Consumer Key
- Consumer Secret
- Shortcode (Test Credentials)
- Passkey (Test Credentials)

**For Production:**
- Apply for production credentials through Safaricom
- Get your production Consumer Key, Secret, Shortcode, and Passkey

### 3. Configure Environment Variables

Add these to your `.env` file:

```env
# M-Pesa Daraja Configuration
DARAJA_CONSUMER_KEY=SuBfnpAXWxpGymIjbnGdowGiHPIQFJzP8FOJP8MdjKxCD8mO
DARAJA_CONSUMER_SECRET=HvhBfWo9YfCxKKrKTNk2PrUNGvZi53jRaNO4pCNlGAVodJV4utoe9kzAFGasUqmU
DARAJA_SHORTCODE=174379
DARAJA_PASSKEY=MTc0Mzc5YmZiMjc5ZjlhYTliZGJjZjE1OGU5N2RkNzFhNDY3Y2QyZTBjODkzMDU5YjEwZjc4ZTZiNzJhZGExZWQyYzkxOTIwMjEwNjI4MDkyNDA4
DARAJA_BASE_URL=https://sandbox.safaricom.co.ke
DARAJA_CALLBACK_URL=https://yourdomain.com/payments/daraja/callback
DARAJA_ENVIRONMENT=sandbox
```

**For Production:**
```env
DARAJA_BASE_URL=https://api.safaricom.co.ke
DARAJA_ENVIRONMENT=production
```

### 4. Configure Callback URL

1. In your Safaricom Developer Portal, set the callback URL
2. The callback URL should be: `https://yourdomain.com/payments/daraja/callback`
3. This endpoint must be publicly accessible (HTTPS required for production)

### 5. Testing

**Sandbox Testing:**
- Use test phone numbers provided by Safaricom
- Test with small amounts (KES 1-100)
- Check callback logs in your application

**Test Phone Numbers (Sandbox):**
- 254708374149 (Test number)
- Use any number starting with 2547 for testing

### 6. Phone Number Format

The system accepts phone numbers in these formats:
- `0712345678` (will be converted to 254712345678)
- `254712345678` (direct format)
- `+254712345678` (will be converted to 254712345678)

## Payment Flow

1. User selects M-Pesa payment method
2. Enters phone number
3. System sends STK Push to phone
4. User enters M-Pesa PIN on phone
5. Payment is processed
6. Callback is received and transaction is updated
7. User is redirected to receipt page

## Features

✅ STK Push payment initiation
✅ Automatic payment status checking
✅ Callback handling for payment confirmation
✅ Transaction tracking
✅ Receipt generation
✅ Error handling

## Security Notes

- Never commit credentials to version control
- Use environment variables for all sensitive data
- Enable HTTPS in production
- Validate callback signatures (implement if needed)
- Regularly rotate credentials
- Monitor callback logs for suspicious activity

## Troubleshooting

**Payment not received:**
- Check phone number format
- Verify Daraja credentials
- Check callback URL is accessible
- Review application logs

**Callback not working:**
- Ensure callback URL is publicly accessible
- Check HTTPS is enabled (production)
- Verify callback URL in Safaricom portal
- Check application logs for errors

## Support

For Daraja API issues:
- Safaricom Developer Portal: https://developer.safaricom.co.ke
- Documentation: https://developer.safaricom.co.ke/docs

