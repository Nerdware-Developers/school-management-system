<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class N8nWebhookService
{
    protected $baseUrl;
    protected $enabled;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.n8n.webhook_url');
        $this->enabled = config('services.n8n.enabled', false);
        $this->timeout = config('services.n8n.timeout', 10);
    }

    /**
     * Send webhook to n8n
     *
     * @param string $eventType
     * @param array $data
     * @param string|null $webhookUrl Override default webhook URL
     * @return bool
     */
    public function sendWebhook(string $eventType, array $data, ?string $webhookUrl = null): bool
    {
        if (!$this->enabled || !$this->baseUrl) {
            Log::debug('N8n webhook disabled or URL not configured', [
                'event_type' => $eventType,
                'enabled' => $this->enabled,
                'base_url' => $this->baseUrl
            ]);
            return false;
        }

        $url = $webhookUrl ?? $this->baseUrl;
        
        $payload = [
            'event_type' => $eventType,
            'timestamp' => now()->toIso8601String(),
            'data' => $data,
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info('N8n webhook sent successfully', [
                    'event_type' => $eventType,
                    'status' => $response->status()
                ]);
                return true;
            } else {
                Log::warning('N8n webhook failed', [
                    'event_type' => $eventType,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('N8n webhook exception', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send webhook asynchronously (fire and forget)
     */
    public function sendWebhookAsync(string $eventType, array $data, ?string $webhookUrl = null): void
    {
        // Dispatch to queue if queue is configured, otherwise send synchronously
        if (config('queue.default') !== 'sync') {
            dispatch(function () use ($eventType, $data, $webhookUrl) {
                $this->sendWebhook($eventType, $data, $webhookUrl);
            })->afterResponse();
        } else {
            $this->sendWebhook($eventType, $data, $webhookUrl);
        }
    }

    // Convenience methods for common events

    /**
     * Send webhook when student is created
     */
    public function studentCreated($student): bool
    {
        return $this->sendWebhook('student.created', [
            'student_id' => $student->id,
            'admission_number' => $student->admission_number,
            'name' => "{$student->first_name} {$student->last_name}",
            'class' => $student->class,
            'parent_name' => $student->parent_name,
            'parent_phone' => $student->parent_number,
            'parent_email' => $student->parent_email,
            'created_at' => $student->created_at->toIso8601String(),
        ]);
    }

    /**
     * Send webhook when student is updated
     */
    public function studentUpdated($student): bool
    {
        return $this->sendWebhook('student.updated', [
            'student_id' => $student->id,
            'admission_number' => $student->admission_number,
            'name' => "{$student->first_name} {$student->last_name}",
            'class' => $student->class,
            'updated_at' => $student->updated_at->toIso8601String(),
        ]);
    }

    /**
     * Send webhook when teacher is created
     */
    public function teacherCreated($teacher): bool
    {
        return $this->sendWebhook('teacher.created', [
            'teacher_id' => $teacher->id,
            'name' => $teacher->full_name,
            'phone' => $teacher->phone_number,
            'class_teacher_id' => $teacher->class_teacher_id,
            'created_at' => $teacher->created_at->toIso8601String(),
        ]);
    }

    /**
     * Send webhook when payment is received
     */
    public function paymentReceived($payment): bool
    {
        $student = $payment->student ?? null;
        
        return $this->sendWebhook('payment.received', [
            'payment_id' => $payment->id,
            'student_id' => $payment->student_id ?? null,
            'student_name' => $student ? "{$student->first_name} {$student->last_name}" : null,
            'amount' => $payment->fees_amount ?? $payment->amount ?? 0,
            'payment_method' => $payment->fees_type ?? $payment->payment_method ?? 'unknown',
            'transaction_id' => $payment->transaction_id ?? null,
            'paid_date' => $payment->paid_date ?? $payment->payment_date ?? now()->toDateString(),
            'created_at' => ($payment->created_at ?? now())->toIso8601String(),
        ]);
    }

    /**
     * Send webhook when exam results are published
     */
    public function examResultsPublished($exam, $studentId, $results): bool
    {
        $student = \App\Models\Student::find($studentId);
        
        return $this->sendWebhook('exam.results_published', [
            'exam_id' => $exam->id,
            'exam_name' => $exam->exam_name ?? $exam->name ?? 'Unknown Exam',
            'student_id' => $studentId,
            'student_name' => $student ? "{$student->first_name} {$student->last_name}" : null,
            'results' => $results,
            'published_at' => now()->toIso8601String(),
        ]);
    }

    /**
     * Send webhook when attendance is marked
     */
    public function attendanceMarked($attendance): bool
    {
        return $this->sendWebhook('attendance.marked', [
            'attendance_id' => $attendance->id,
            'student_id' => $attendance->student_id,
            'date' => $attendance->date ?? $attendance->attendance_date ?? null,
            'status' => $attendance->status ?? 'present',
            'class_id' => $attendance->class_id ?? null,
            'marked_at' => ($attendance->created_at ?? now())->toIso8601String(),
        ]);
    }

    /**
     * Send webhook when event is created
     */
    public function eventCreated($event): bool
    {
        return $this->sendWebhook('event.created', [
            'event_id' => $event->id,
            'title' => $event->title,
            'type' => $event->type,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
            'visibility' => $event->visibility,
            'target_class' => $event->target_class,
            'created_at' => $event->created_at->toIso8601String(),
        ]);
    }

    /**
     * Send webhook when fee reminder is due
     */
    public function feeReminderDue($student, $feeTerm): bool
    {
        return $this->sendWebhook('fee.reminder_due', [
            'student_id' => $student->id,
            'student_name' => "{$student->first_name} {$student->last_name}",
            'admission_number' => $student->admission_number,
            'parent_name' => $student->parent_name,
            'parent_phone' => $student->parent_number,
            'parent_email' => $student->parent_email,
            'fee_term_id' => $feeTerm->id,
            'term_name' => $feeTerm->term_name,
            'outstanding_balance' => $feeTerm->closing_balance ?? 0,
            'reminder_date' => now()->toIso8601String(),
        ]);
    }

    /**
     * Send webhook for custom events
     */
    public function customEvent(string $eventType, array $data): bool
    {
        return $this->sendWebhook($eventType, $data);
    }
}

