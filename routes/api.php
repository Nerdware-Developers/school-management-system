<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// N8N Notification API Routes (protected by API key)
Route::middleware(['api.key'])->prefix('n8n/notifications')->group(function () {
    // Get pending parent notifications for external delivery
    Route::get('/pending', [NotificationApiController::class, 'getPendingNotifications']);
    
    // Mark notification as sent externally
    Route::post('/mark-sent', [NotificationApiController::class, 'markAsSent']);
    
    // Create notification via API
    Route::post('/create', [NotificationApiController::class, 'createNotification']);
    
    // Get notification statistics
    Route::get('/stats', [NotificationApiController::class, 'getStats']);
});

// N8N Webhook Receiver Routes (for bidirectional communication)
Route::middleware(['api.key'])->prefix('n8n/webhook')->group(function () {
    Route::post('/receive', [\App\Http\Controllers\Api\N8nWebhookController::class, 'receiveWebhook']);
    Route::get('/health', [\App\Http\Controllers\Api\N8nWebhookController::class, 'healthCheck']);
});