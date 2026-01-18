<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Send notification to a user
     */
    public function sendToUser($userId, $type, $title, $message, $link = null, $metadata = null)
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'metadata' => $metadata,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send notification to user', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Send notification to multiple users
     */
    public function sendToUsers($userIds, $type, $title, $message, $link = null, $metadata = null)
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notification = $this->sendToUser($userId, $type, $title, $message, $link, $metadata);
            if ($notification) {
                $notifications[] = $notification;
            }
        }
        
        return $notifications;
    }

    /**
     * Send broadcast notification (to all users)
     */
    public function sendBroadcast($type, $title, $message, $link = null, $metadata = null)
    {
        try {
            return Notification::create([
                'user_id' => null, // null = broadcast
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'metadata' => $metadata,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send broadcast notification', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Send notification to parent via student record
     */
    public function sendToParent($studentId, $type, $title, $message, $link = null, $metadata = null)
    {
        $student = Student::find($studentId);
        
        if (!$student) {
            Log::warning('Student not found for parent notification', ['student_id' => $studentId]);
            return null;
        }

        // Find user account linked to parent email or create notification metadata
        $user = null;
        if ($student->parent_email) {
            $user = User::where('email', $student->parent_email)->first();
        }

        $notificationData = [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'metadata' => array_merge($metadata ?? [], [
                'student_id' => $student->id,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'parent_name' => $student->parent_name,
                'parent_phone' => $student->parent_number,
                'parent_email' => $student->parent_email,
                'guardian_name' => $student->guardian_name,
                'guardian_phone' => $student->guardian_number,
                'guardian_email' => $student->guardian_email,
            ]),
        ];

        // If parent has user account, send to them
        if ($user) {
            return $this->sendToUser($user->id, $type, $title, $message, $link, $notificationData['metadata']);
        }

        // Otherwise, create a notification that can be sent via external channels (SMS/Email/WhatsApp)
        // This will be picked up by n8n or other external services
        return Notification::create([
            'user_id' => null, // No user account, but metadata contains parent info
            'type' => 'parent_' . $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'metadata' => $notificationData['metadata'],
            'is_read' => false,
        ]);
    }

    /**
     * Send notification to multiple parents
     */
    public function sendToParents($studentIds, $type, $title, $message, $link = null, $metadata = null)
    {
        $notifications = [];
        
        foreach ($studentIds as $studentId) {
            $notification = $this->sendToParent($studentId, $type, $title, $message, $link, $metadata);
            if ($notification) {
                $notifications[] = $notification;
            }
        }
        
        return $notifications;
    }

    /**
     * Send exam results notification to parent
     */
    public function sendExamResults($studentId, $examData)
    {
        $student = Student::find($studentId);
        if (!$student) return null;

        $title = "Exam Results - {$examData['exam_name']}";
        $message = "Dear {$student->parent_name},\n\n";
        $message .= "{$student->first_name} {$student->last_name}'s exam results for {$examData['exam_name']}:\n\n";
        
        if (isset($examData['results']) && is_array($examData['results'])) {
            foreach ($examData['results'] as $result) {
                $percentage = $result['total_marks'] > 0 
                    ? round(($result['marks'] / $result['total_marks']) * 100, 2) 
                    : 0;
                $message .= "{$result['subject']}: {$result['marks']}/{$result['total_marks']} ({$percentage}%)\n";
            }
        }
        
        $message .= "\nThank you.";

        return $this->sendToParent($studentId, 'exam', $title, $message, null, [
            'exam_id' => $examData['exam_id'] ?? null,
            'exam_name' => $examData['exam_name'] ?? null,
            'term' => $examData['term'] ?? null,
            'results' => $examData['results'] ?? [],
        ]);
    }

    /**
     * Send fee reminder notification to parent
     */
    public function sendFeeReminder($studentId, $feeData)
    {
        $student = Student::find($studentId);
        if (!$student) return null;

        $title = "Fee Payment Reminder";
        $message = "Dear {$student->parent_name},\n\n";
        $message .= "This is a reminder that {$student->first_name} {$student->last_name} ";
        $message .= "(Admission: {$student->admission_number}) has an outstanding fee balance of ";
        $message .= "Ksh " . number_format($feeData['outstanding_balance'], 2) . " ";
        $message .= "for {$feeData['term_name']} {$feeData['academic_year']}.\n\n";
        $message .= "Please make payment at your earliest convenience.\n\n";
        $message .= "Thank you.";

        return $this->sendToParent($studentId, 'fee', $title, $message, '/payments/student/' . $studentId, [
            'fee_term_id' => $feeData['fee_term_id'] ?? null,
            'term_name' => $feeData['term_name'] ?? null,
            'academic_year' => $feeData['academic_year'] ?? null,
            'outstanding_balance' => $feeData['outstanding_balance'] ?? 0,
        ]);
    }

    /**
     * Send event notification to parent
     */
    public function sendEventNotification($studentId, $eventData)
    {
        $student = Student::find($studentId);
        if (!$student) return null;

        $title = "Upcoming Event: {$eventData['title']}";
        $message = "Dear {$student->parent_name},\n\n";
        $message .= "Upcoming Event: {$eventData['title']}\n";
        $message .= "Date: {$eventData['date']}\n";
        
        if (isset($eventData['time'])) {
            $message .= "Time: {$eventData['time']}\n";
        }
        
        if (isset($eventData['location'])) {
            $message .= "Location: {$eventData['location']}\n";
        }
        
        $message .= "\n{$eventData['description']}\n\n";
        $message .= "Please mark your calendar.\n\n";
        $message .= "Thank you.";

        return $this->sendToParent($studentId, 'event', $title, $message, '/events/' . ($eventData['event_id'] ?? ''), [
            'event_id' => $eventData['event_id'] ?? null,
            'event_type' => $eventData['type'] ?? null,
        ]);
    }

    /**
     * Send school opening/closing date notification
     */
    public function sendSchoolDateNotification($studentId, $dateData)
    {
        $student = Student::find($studentId);
        if (!$student) return null;

        $eventType = $dateData['type'] === 'opening' ? 'Opening' : 'Closing';
        $title = "School {$eventType} Date";
        $message = "Dear {$student->parent_name},\n\n";
        $message .= "This is to inform you that the school {$dateData['type']} date is: ";
        $message .= "{$dateData['date']}\n";
        
        if (isset($dateData['time'])) {
            $message .= "Time: {$dateData['time']}\n";
        }
        
        if (isset($dateData['description'])) {
            $message .= "\n{$dateData['description']}\n";
        }
        
        $message .= "\nPlease make necessary arrangements.\n\n";
        $message .= "Thank you.";

        return $this->sendToParent($studentId, 'school_date', $title, $message, null, [
            'event_id' => $dateData['event_id'] ?? null,
            'date_type' => $dateData['type'] ?? null,
        ]);
    }

    /**
     * Get pending parent notifications (for external delivery via n8n)
     */
    public function getPendingParentNotifications($limit = 100)
    {
        return Notification::whereNull('user_id')
            ->where('type', 'like', 'parent_%')
            ->where('is_read', false)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark notification as sent externally
     */
    public function markAsSentExternally($notificationId, $channel = null)
    {
        $notification = Notification::find($notificationId);
        if (!$notification) return false;

        $metadata = $notification->metadata ?? [];
        $metadata['sent_externally'] = true;
        $metadata['external_channel'] = $channel;
        $metadata['sent_at'] = now()->toDateTimeString();

        $notification->update([
            'metadata' => $metadata,
        ]);

        return true;
    }
}

