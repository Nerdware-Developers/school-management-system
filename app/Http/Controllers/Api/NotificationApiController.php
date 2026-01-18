<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\Notification;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class NotificationApiController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get pending parent notifications for external delivery (n8n)
     */
    public function getPendingNotifications(Request $request)
    {
        $limit = $request->input('limit', 100);
        $notifications = $this->notificationService->getPendingParentNotifications($limit);

        $formatted = $notifications->map(function($notification) {
            $metadata = $notification->metadata ?? [];
            
            return [
                'notification_id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'link' => $notification->link,
                'created_at' => $notification->created_at->toDateTimeString(),
                'student_id' => $metadata['student_id'] ?? null,
                'student_name' => $metadata['student_name'] ?? null,
                'parent_name' => $metadata['parent_name'] ?? null,
                'parent_phone' => $metadata['parent_phone'] ?? null,
                'parent_email' => $metadata['parent_email'] ?? null,
                'guardian_name' => $metadata['guardian_name'] ?? null,
                'guardian_phone' => $metadata['guardian_phone'] ?? null,
                'guardian_email' => $metadata['guardian_email'] ?? null,
                'metadata' => $metadata,
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $formatted->count(),
            'data' => $formatted
        ]);
    }

    /**
     * Mark notification as sent externally
     */
    public function markAsSent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
            'channel' => 'nullable|string|in:sms,email,whatsapp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->notificationService->markAsSentExternally(
            $request->notification_id,
            $request->channel
        );

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as sent'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to mark notification as sent'
        ], 400);
    }

    /**
     * Create notification via API (for n8n to create notifications)
     */
    public function createNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'student_id' => 'nullable|exists:students,id',
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'link' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($request->has('student_id')) {
                // Send to parent
                $notification = $this->notificationService->sendToParent(
                    $request->student_id,
                    $request->type,
                    $request->title,
                    $request->message,
                    $request->link,
                    $request->metadata
                );
            } elseif ($request->has('user_id')) {
                // Send to user
                $notification = $this->notificationService->sendToUser(
                    $request->user_id,
                    $request->type,
                    $request->title,
                    $request->message,
                    $request->link,
                    $request->metadata
                );
            } else {
                // Broadcast
                $notification = $this->notificationService->sendBroadcast(
                    $request->type,
                    $request->title,
                    $request->message,
                    $request->link,
                    $request->metadata
                );
            }

            if ($notification) {
                return response()->json([
                    'success' => true,
                    'data' => $notification
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create notification'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(Request $request)
    {
        $stats = [
            'total' => Notification::count(),
            'unread' => Notification::where('is_read', false)->count(),
            'read' => Notification::where('is_read', true)->count(),
            'pending_parent' => Notification::whereNull('user_id')
                ->where('type', 'like', 'parent_%')
                ->where('is_read', false)
                ->count(),
            'by_type' => Notification::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

