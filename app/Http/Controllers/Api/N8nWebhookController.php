<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ApiKeyAuth;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class N8nWebhookController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Receive webhook from n8n (for bidirectional communication)
     * 
     * This endpoint allows n8n to send data back to the system
     */
    public function receiveWebhook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required|string',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $eventType = $request->input('event_type');
            $data = $request->input('data');

            Log::info('N8n webhook received', [
                'event_type' => $eventType,
                'data' => $data
            ]);

            // Handle different event types from n8n
            switch ($eventType) {
                case 'notification.delivered':
                    return $this->handleNotificationDelivered($data);
                
                case 'notification.failed':
                    return $this->handleNotificationFailed($data);
                
                case 'external.data':
                    return $this->handleExternalData($data);
                
                default:
                    Log::warning('Unknown n8n webhook event type', ['event_type' => $eventType]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Unknown event type'
                    ], 400);
            }
        } catch (\Exception $e) {
            Log::error('N8n webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing webhook'
            ], 500);
        }
    }

    /**
     * Handle notification delivery confirmation from n8n
     */
    protected function handleNotificationDelivered(array $data)
    {
        if (isset($data['notification_id'])) {
            $this->notificationService->markAsSentExternally(
                $data['notification_id'],
                $data['channel'] ?? null
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification delivery status updated'
        ]);
    }

    /**
     * Handle notification failure from n8n
     */
    protected function handleNotificationFailed(array $data)
    {
        Log::warning('Notification delivery failed via n8n', [
            'notification_id' => $data['notification_id'] ?? null,
            'channel' => $data['channel'] ?? null,
            'error' => $data['error'] ?? 'Unknown error'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification failure logged'
        ]);
    }

    /**
     * Handle external data from n8n (for syncing external data back)
     */
    protected function handleExternalData(array $data)
    {
        // This can be used for syncing data from external services
        // Example: Parent responses, external payment confirmations, etc.
        
        Log::info('External data received from n8n', ['data' => $data]);

        return response()->json([
            'success' => true,
            'message' => 'External data received'
        ]);
    }

    /**
     * Health check endpoint for n8n
     */
    public function healthCheck()
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0')
        ]);
    }
}

