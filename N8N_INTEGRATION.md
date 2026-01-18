# N8N Integration Guide

## Overview

The n8n integration system allows you to automate workflows between the School Management System and external services. This includes:

- **Outbound Webhooks**: Automatically send events to n8n when actions occur in the system
- **Inbound Webhooks**: Receive data from n8n workflows
- **Notification Processing**: Process notifications through n8n for SMS, Email, WhatsApp, etc.
- **Event-Driven Automation**: Trigger workflows based on system events

## Features

1. **Automatic Event Webhooks**: System events automatically trigger webhooks to n8n
2. **Notification API**: Get pending notifications and mark them as sent
3. **Bidirectional Communication**: Send and receive data from n8n
4. **Secure Authentication**: API key-based authentication
5. **Async Processing**: Webhooks sent asynchronously to avoid blocking requests

## Configuration

### 1. Environment Variables

Add these to your `.env` file:

```env
# Enable/disable n8n integration
N8N_ENABLED=true

# Your n8n webhook URL (the webhook endpoint in n8n)
N8N_WEBHOOK_URL=http://localhost:5678/webhook/your-webhook-id

# API key for authenticating n8n requests (same as notification API key)
N8N_API_KEY=your-secret-api-key-here

# Webhook timeout in seconds
N8N_TIMEOUT=10
```

### 2. Update Configuration

After adding to `.env`, clear config cache:

```bash
php artisan config:clear
```

## API Endpoints

All endpoints require the `X-API-Key` header with your API key.

### Base URLs

- **Notifications API**: `/api/n8n/notifications`
- **Webhook Receiver**: `/api/n8n/webhook`

### 1. Get Pending Notifications

**Endpoint**: `GET /api/n8n/notifications/pending`

**Headers**:
```
X-API-Key: your-secret-api-key-here
Accept: application/json
```

**Query Parameters**:
- `limit` (optional): Maximum notifications to return (default: 100)

**Response**:
```json
{
  "success": true,
  "count": 5,
  "data": [
    {
      "notification_id": 1,
      "type": "parent_exam",
      "title": "Exam Results",
      "message": "...",
      "parent_phone": "+254712345678",
      "parent_email": "parent@example.com",
      "metadata": {...}
    }
  ]
}
```

### 2. Mark Notification as Sent

**Endpoint**: `POST /api/n8n/notifications/mark-sent`

**Body**:
```json
{
  "notification_id": 1,
  "channel": "sms"
}
```

### 3. Create Notification

**Endpoint**: `POST /api/n8n/notifications/create`

**Body**:
```json
{
  "student_id": 123,
  "type": "info",
  "title": "Notification Title",
  "message": "Notification message",
  "metadata": {}
}
```

### 4. Get Statistics

**Endpoint**: `GET /api/n8n/notifications/stats`

### 5. Receive Webhook from n8n

**Endpoint**: `POST /api/n8n/webhook/receive`

**Body**:
```json
{
  "event_type": "notification.delivered",
  "data": {
    "notification_id": 1,
    "channel": "sms"
  }
}
```

### 6. Health Check

**Endpoint**: `GET /api/n8n/webhook/health`

## Event Types

The system automatically sends webhooks for these events:

### 1. Student Events

#### `student.created`
Triggered when a new student is registered.

**Payload**:
```json
{
  "event_type": "student.created",
  "timestamp": "2025-11-24T10:30:00Z",
  "data": {
    "student_id": 123,
    "admission_number": "STU001",
    "name": "John Doe",
    "class": "Form 1A",
    "parent_name": "Jane Doe",
    "parent_phone": "+254712345678",
    "parent_email": "parent@example.com"
  }
}
```

#### `student.updated`
Triggered when student information is updated.

### 2. Payment Events

#### `payment.received`
Triggered when a payment is received.

**Payload**:
```json
{
  "event_type": "payment.received",
  "timestamp": "2025-11-24T10:30:00Z",
  "data": {
    "payment_id": 456,
    "student_id": 123,
    "student_name": "John Doe",
    "amount": 5000.00,
    "payment_method": "M-pesa",
    "transaction_id": "MP123456789",
    "paid_date": "2025-11-24"
  }
}
```

### 3. Exam Events

#### `exam.results_published`
Triggered when exam results are published for a student.

**Payload**:
```json
{
  "event_type": "exam.results_published",
  "timestamp": "2025-11-24T10:30:00Z",
  "data": {
    "exam_id": 789,
    "exam_name": "Mid-Term - Term 1",
    "student_id": 123,
    "student_name": "John Doe",
    "results": [
      {
        "subject": "Math",
        "marks": 85,
        "total_marks": 100
      }
    ]
  }
}
```

### 4. Attendance Events

#### `attendance.marked`
Triggered when attendance is marked for a student.

### 5. Event Events

#### `event.created`
Triggered when a school event is created.

### 6. Fee Events

#### `fee.reminder_due`
Triggered when a fee reminder is due for a student.

## N8N Workflow Examples

### Example 1: Send SMS Notifications for Exam Results

1. **Webhook Trigger** - Receive webhook from system
   - Event type: `exam.results_published`

2. **IF Node** - Check if parent phone exists
   - Condition: `{{ $json.data.parent_phone }}` exists

3. **HTTP Request Node** - Get notification details
   - Method: `GET`
   - URL: `http://your-domain.com/api/n8n/notifications/pending?limit=1`

4. **SMS Node** - Send SMS via your SMS provider
   - Phone: `{{ $json.data.parent_phone }}`
   - Message: Format exam results message

5. **HTTP Request Node** - Mark notification as sent
   - Method: `POST`
   - URL: `http://your-domain.com/api/n8n/notifications/mark-sent`
   - Body: `{"notification_id": {{ $json.notification_id }}, "channel": "sms"}`

### Example 2: Send Email Notifications

Similar to Example 1, but:
- Use Email node instead of SMS
- Use `parent_email` instead of `parent_phone`
- Set channel to `"email"` when marking as sent

### Example 3: Automatic Payment Confirmation

1. **Webhook Trigger** - Receive payment webhook
   - Event type: `payment.received`

2. **IF Node** - Check payment amount
   - Condition: `{{ $json.data.amount }} > 0`

3. **Email Node** - Send confirmation email
   - To: `{{ $json.data.parent_email }}`
   - Subject: "Payment Received"
   - Body: Include payment details

4. **SMS Node** - Send SMS confirmation
   - To: `{{ $json.data.parent_phone }}`
   - Message: "Payment of Ksh {{ $json.data.amount }} received. Thank you!"

### Example 4: New Student Welcome Workflow

1. **Webhook Trigger** - Receive student created webhook
   - Event type: `student.created`

2. **Code Node** - Format welcome message
   ```javascript
   const message = `Welcome ${$json.data.name}! 
   Admission Number: ${$json.data.admission_number}
   Class: ${$json.data.class}
   
   Login details will be sent separately.`;

   return {
     message: message,
     parent_phone: $json.data.parent_phone,
     parent_email: $json.data.parent_email
   };
   ```

3. **Send SMS & Email** - Welcome messages

### Example 5: Fee Reminder Workflow

1. **Cron Trigger** - Run daily at 9 AM

2. **HTTP Request Node** - Get pending fee reminders
   - Method: `GET`
   - URL: `http://your-domain.com/api/n8n/notifications/pending?limit=50`
   - Headers: `X-API-Key: your-key`

3. **Filter Node** - Filter fee reminders
   - Condition: `{{ $json.type }}` contains "fee"

4. **Loop Over Items** - Process each reminder

5. **Send SMS/Email** - Fee reminder messages

6. **Mark as Sent** - After successful delivery

## Manual Webhook Triggering

You can also manually trigger webhooks from your code:

```php
use App\Services\N8nWebhookService;

$webhookService = app(N8nWebhookService::class);

// Send custom webhook
$webhookService->sendWebhook('custom.event', [
    'key' => 'value',
    'data' => [...]
]);

// Or use convenience methods
$webhookService->studentCreated($student);
$webhookService->paymentReceived($payment);
$webhookService->examResultsPublished($exam, $studentId, $results);
```

## Event Dispatching

To trigger automatic webhooks, dispatch events in your controllers:

```php
use App\Events\StudentCreated;
use App\Events\PaymentReceived;

// In your controller after creating a student
event(new StudentCreated($student));

// After receiving a payment
event(new PaymentReceived($payment));
```

## Testing

### Test Webhook Endpoint

Create a test endpoint in n8n that logs received webhooks:

1. In n8n, create a new workflow
2. Add a Webhook node
3. Set it to receive POST requests
4. Add a Code node to log the data
5. Copy the webhook URL to your `.env`

### Test Notification API

```bash
# Get pending notifications
curl -X GET "http://localhost:8000/api/n8n/notifications/pending" \
  -H "X-API-Key: your-secret-api-key-here" \
  -H "Accept: application/json"

# Mark notification as sent
curl -X POST "http://localhost:8000/api/n8n/notifications/mark-sent" \
  -H "X-API-Key: your-secret-api-key-here" \
  -H "Content-Type: application/json" \
  -d '{"notification_id": 1, "channel": "sms"}'
```

### Test Health Check

```bash
curl -X GET "http://localhost:8000/api/n8n/webhook/health" \
  -H "X-API-Key: your-secret-api-key-here"
```

## Security Best Practices

1. **Use HTTPS in Production**: Always use HTTPS for webhook URLs
2. **Rotate API Keys**: Regularly rotate your API keys
3. **Validate Webhooks**: Verify webhook signatures if your n8n instance supports it
4. **Rate Limiting**: Consider implementing rate limiting for webhook endpoints
5. **Logging**: Monitor webhook logs for suspicious activity

## Troubleshooting

### Webhooks Not Being Sent

1. Check if n8n is enabled: `N8N_ENABLED=true`
2. Verify webhook URL is correct
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure events are being dispatched

### API Authentication Failing

1. Verify API key matches in `.env` and n8n workflow
2. Check `X-API-Key` header is being sent
3. Clear config cache: `php artisan config:clear`

### Timeout Issues

1. Increase timeout: `N8N_TIMEOUT=30`
2. Use async webhooks (default behavior)
3. Check n8n server response time

## Next Steps

1. Set up your n8n instance
2. Create webhook workflows
3. Configure environment variables
4. Test webhook endpoints
5. Monitor logs and adjust as needed

For more information about n8n, visit: https://n8n.io

