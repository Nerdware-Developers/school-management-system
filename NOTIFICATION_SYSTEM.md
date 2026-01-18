# Notification System Documentation

## Overview

The notification system allows you to send notifications to users and parents through multiple channels:
- **In-app notifications** - Displayed in the application
- **External channels** - SMS, Email, WhatsApp via n8n integration

## Features

1. **User Notifications** - Send notifications to specific users
2. **Broadcast Notifications** - Send notifications to all users
3. **Parent Notifications** - Send notifications to parents via student records
4. **Automatic Triggers** - Notifications are automatically sent for:
   - Exam results when marks are saved
   - Events when created (for parents/public events)
   - Fee reminders (via scheduled tasks)

## API Endpoints for n8n

All API endpoints require an API key in the `X-API-Key` header.

### Base URL
```
http://your-domain.com/api/n8n/notifications
```

### 1. Get Pending Parent Notifications

Get notifications that need to be sent externally (SMS/Email/WhatsApp).

**Endpoint:** `GET /pending`

**Headers:**
```
X-API-Key: your-secret-api-key-here
Accept: application/json
```

**Query Parameters:**
- `limit` (optional): Maximum number of notifications to return (default: 100)

**Response:**
```json
{
  "success": true,
  "count": 5,
  "data": [
    {
      "notification_id": 1,
      "type": "parent_exam",
      "title": "Exam Results - Mid-Term - Term 1",
      "message": "Dear John Doe,\n\nJane Doe's exam results...",
      "link": null,
      "created_at": "2025-01-15 10:30:00",
      "student_id": 123,
      "student_name": "Jane Doe",
      "parent_name": "John Doe",
      "parent_phone": "+254712345678",
      "parent_email": "john@example.com",
      "guardian_name": null,
      "guardian_phone": null,
      "guardian_email": null,
      "metadata": {
        "student_id": 123,
        "student_name": "Jane Doe",
        "parent_name": "John Doe",
        "parent_phone": "+254712345678",
        "parent_email": "john@example.com",
        "exam_id": 45,
        "exam_name": "Mid-Term - Term 1",
        "term": "Term 1",
        "results": [...]
      }
    }
  ]
}
```

### 2. Mark Notification as Sent

Mark a notification as sent externally after delivery.

**Endpoint:** `POST /mark-sent`

**Headers:**
```
X-API-Key: your-secret-api-key-here
Content-Type: application/json
```

**Body:**
```json
{
  "notification_id": 1,
  "channel": "sms"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Notification marked as sent"
}
```

### 3. Create Notification

Create a new notification via API.

**Endpoint:** `POST /create`

**Headers:**
```
X-API-Key: your-secret-api-key-here
Content-Type: application/json
```

**Body:**
```json
{
  "user_id": 1,           // Optional: Send to specific user
  "student_id": 123,       // Optional: Send to parent of student
  "type": "info",          // Required: Notification type
  "title": "Notification Title",  // Required
  "message": "Notification message",  // Required
  "link": "/some/link",    // Optional
  "metadata": {}           // Optional: Additional data
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "user_id": null,
    "type": "parent_info",
    "title": "Notification Title",
    "message": "Notification message",
    ...
  }
}
```

### 4. Get Notification Statistics

Get statistics about notifications.

**Endpoint:** `GET /stats`

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 150,
    "unread": 45,
    "read": 105,
    "pending_parent": 12,
    "by_type": {
      "info": 50,
      "parent_exam": 30,
      "parent_event": 20,
      ...
    }
  }
}
```

## n8n Workflow Examples

### Example 1: Send Pending Notifications via SMS

1. **HTTP Request Node** - Get pending notifications
   - Method: `GET`
   - URL: `http://your-domain.com/api/n8n/notifications/pending?limit=50`
   - Headers: `X-API-Key: your-key`

2. **Split In Batches Node** - Process notifications in batches

3. **Loop Over Items Node** - For each notification:
   - Extract `parent_phone` and `message`
   - Send SMS via your SMS provider
   - Mark notification as sent

4. **HTTP Request Node** - Mark as sent
   - Method: `POST`
   - URL: `http://your-domain.com/api/n8n/notifications/mark-sent`
   - Body: `{"notification_id": {{ $json.notification_id }}, "channel": "sms"}`

### Example 2: Send Pending Notifications via Email

Similar to Example 1, but:
- Use Email node instead of SMS
- Extract `parent_email` instead of `parent_phone`
- Set `channel` to `"email"` when marking as sent

### Example 3: Send Pending Notifications via WhatsApp

Similar to Example 1, but:
- Use WhatsApp node (via Twilio, etc.)
- Set `channel` to `"whatsapp"` when marking as sent

### Example 4: Scheduled Fee Reminders

1. **Cron Trigger** - Run daily at 9 AM

2. **Code Node** - Query fee reminders
   ```javascript
   // You'll need to create a separate endpoint for this
   // Or use the ParentNotificationController endpoints
   ```

3. **HTTP Request Node** - Get fee reminders
   - Method: `GET`
   - URL: `http://your-domain.com/api/n8n/parents/fee-reminders`

4. **Loop and Send** - Send notifications to each parent

## Notification Types

- `info` - General information
- `success` - Success message
- `warning` - Warning message
- `error` - Error message
- `fee` - Fee-related notification
- `exam` - Exam-related notification
- `event` - Event notification
- `school_date` - School opening/closing dates
- `parent_exam` - Exam results for parents
- `parent_event` - Event notification for parents
- `parent_fee` - Fee reminder for parents

## Automatic Notification Triggers

### Exam Results
When exam marks are saved via `ExamController::saveMarks()`, notifications are automatically sent to parents with their child's exam results.

### Events
When events are created via `EventController::store()` and the event is:
- Type: `opening` or `closing`
- Visibility: `parents`, `public`, or `specific_class`

Notifications are automatically sent to relevant parents.

### Fee Reminders
Create a scheduled task (Laravel command) to send fee reminders. Example:

```php
// app/Console/Commands/SendFeeReminders.php
php artisan make:command SendFeeReminders
```

## Configuration

Add to your `.env` file:

```env
N8N_API_KEY=your-secret-api-key-change-this-in-production
```

## Usage in Code

### Send Notification to User
```php
use App\Services\NotificationService;

$notificationService = app(NotificationService::class);
$notificationService->sendToUser(
    $userId,
    'info',
    'Title',
    'Message',
    '/link',
    ['metadata' => 'value']
);
```

### Send Notification to Parent
```php
$notificationService->sendToParent(
    $studentId,
    'exam',
    'Exam Results',
    'Your child scored...',
    null,
    ['exam_id' => 1]
);
```

### Send Exam Results Notification
```php
$notificationService->sendExamResults($studentId, [
    'exam_id' => 1,
    'exam_name' => 'Mid-Term - Term 1',
    'term' => 'Term 1',
    'results' => [
        ['subject' => 'Math', 'marks' => 85, 'total_marks' => 100],
        ['subject' => 'English', 'marks' => 90, 'total_marks' => 100],
    ]
]);
```

### Send Fee Reminder
```php
$notificationService->sendFeeReminder($studentId, [
    'fee_term_id' => 1,
    'term_name' => 'Term 1',
    'academic_year' => '2025',
    'outstanding_balance' => 5000.00
]);
```

## Security

- All API endpoints require authentication via API key
- API key should be kept secret and rotated regularly
- Use HTTPS in production
- Consider rate limiting for API endpoints

## Testing

Test the API endpoints using curl:

```bash
curl -X GET "http://localhost:8000/api/n8n/notifications/pending" \
  -H "X-API-Key: your-secret-api-key-here" \
  -H "Accept: application/json"
```

