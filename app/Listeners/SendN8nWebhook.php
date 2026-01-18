<?php

namespace App\Listeners;

use App\Services\N8nWebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendN8nWebhook implements ShouldQueue
{
    use InteractsWithQueue;

    protected $webhookService;

    /**
     * Create the event listener.
     */
    public function __construct(N8nWebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Handle the event.
     */
    public function handle($event)
    {
        // Handle specific event types
        switch (get_class($event)) {
            case \App\Events\StudentCreated::class:
                $this->webhookService->studentCreated($event->student);
                break;
            
            case \App\Events\PaymentReceived::class:
                $this->webhookService->paymentReceived($event->payment);
                break;
            
            case \App\Events\ExamResultsPublished::class:
                $this->webhookService->examResultsPublished(
                    $event->exam,
                    $event->studentId,
                    $event->results
                );
                break;
            
            default:
                // Fallback: generic webhook
                $eventClass = get_class($event);
                $eventType = strtolower(str_replace(
                    ['App\\Events\\', '\\'],
                    ['', '.'],
                    $eventClass
                ));
                
                $data = $this->extractEventData($event);
                $this->webhookService->sendWebhookAsync($eventType, $data);
                break;
        }
    }

    /**
     * Extract data from event object
     */
    protected function extractEventData($event): array
    {
        // Default: try to get data from event properties
        $data = [];
        
        // If event has a model, extract model data
        if (isset($event->model)) {
            $model = $event->model;
            $data = [
                'id' => $model->id,
                'model_type' => get_class($model),
                'model_data' => $model->toArray(),
            ];
        }

        // If event has explicit data property
        if (isset($event->data)) {
            $data = array_merge($data, $event->data);
        }

        // Add timestamp
        $data['event_timestamp'] = now()->toIso8601String();

        return $data;
    }
}

