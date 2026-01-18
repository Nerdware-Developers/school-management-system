<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExamResultsPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $exam;
    public $studentId;
    public $results;

    /**
     * Create a new event instance.
     */
    public function __construct($exam, $studentId, $results)
    {
        $this->exam = $exam;
        $this->studentId = $studentId;
        $this->results = $results;
    }
}

