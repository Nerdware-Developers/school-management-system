<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Classe;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    /**
     * Display calendar view
     */
    public function index()
    {
        $eventsQuery = Event::where('is_active', true)
            ->where(function($query) {
                $query->where('visibility', 'public')
                      ->orWhere('visibility', 'staff')
                      ->orWhere('created_by', Auth::id());
            })
            ->orderBy('start_date', 'asc');

        // Get events for list view
        $eventsList = $eventsQuery->get()->map(function($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date->format('Y-m-d'),
                'end' => $event->end_date ? $event->end_date->format('Y-m-d') : null,
                'color' => $event->color,
                'description' => $event->description,
                'location' => $event->location,
                'type' => $event->type,
            ];
        });

        return view('events.index', compact('eventsList'));
    }

    /**
     * Get events as JSON (for FullCalendar)
     */
    public function getEvents(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $events = Event::where('is_active', true)
            ->where(function($query) {
                $query->where('visibility', 'public')
                      ->orWhere('visibility', 'staff')
                      ->orWhere('created_by', Auth::id());
            })
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('start_date', '<=', $start)
                            ->where(function($q2) use ($end) {
                                $q2->whereNull('end_date')
                                   ->orWhere('end_date', '>=', $end);
                            });
                      });
            })
            ->get()
            ->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->is_all_day 
                        ? $event->start_date->format('Y-m-d')
                        : $event->start_date->format('Y-m-d') . 'T' . ($event->start_time ? Carbon::parse($event->start_time)->format('H:i:s') : '00:00:00'),
                    'end' => $event->end_date 
                        ? ($event->is_all_day 
                            ? Carbon::parse($event->end_date)->addDay()->format('Y-m-d')
                            : $event->end_date->format('Y-m-d') . 'T' . ($event->end_time ? Carbon::parse($event->end_time)->format('H:i:s') : '23:59:59'))
                        : null,
                    'color' => $event->color,
                    'description' => $event->description,
                    'location' => $event->location,
                    'type' => $event->type,
                    'allDay' => $event->is_all_day,
                    'url' => route('events.show', $event->id),
                ];
            });

        return response()->json($events);
    }

    /**
     * Show the form for creating a new event
     */
    public function create()
    {
        $classes = Classe::all();
        return view('events.create', compact('classes'));
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:holiday,exam,event,meeting,sports,cultural,other',
            'visibility' => 'required|in:public,staff,students,parents,specific_class',
            'target_class' => 'nullable|required_if:visibility,specific_class|exists:classes,id',
            'color' => 'nullable|string|max:7',
            'is_all_day' => 'nullable|boolean',
        ]);

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'type' => $request->type,
            'visibility' => $request->visibility,
            'target_class' => $request->target_class,
            'color' => $request->color ?? $this->getDefaultColor($request->type),
            'is_all_day' => $request->has('is_all_day'),
            'created_by' => Auth::id(),
        ]);

        // Send notifications if event is for parents or specific class
        if (in_array($request->visibility, ['parents', 'public', 'specific_class']) || 
            in_array($request->type, ['opening', 'closing'])) {
            $this->sendEventNotifications($event);
        }

        Toastr::success('Event created successfully', 'Success');
        return redirect()->route('events.index');
    }

    /**
     * Display the specified event
     */
    public function show($id)
    {
        $event = Event::with('creator')->findOrFail($id);
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);
        
        // Check if user can edit
        if ($event->created_by != Auth::id() && Auth::user()->role_name != 'Admin') {
            Toastr::error('Unauthorized to edit this event', 'Error');
            return redirect()->route('events.index');
        }

        $classes = Classe::all();
        return view('events.edit', compact('event', 'classes'));
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        // Check if user can edit
        if ($event->created_by != Auth::id() && Auth::user()->role_name != 'Admin') {
            Toastr::error('Unauthorized to edit this event', 'Error');
            return redirect()->route('events.index');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'type' => 'required|in:holiday,exam,event,meeting,sports,cultural,other',
            'visibility' => 'required|in:public,staff,students,parents,specific_class',
            'target_class' => 'nullable|required_if:visibility,specific_class|exists:classes,id',
            'color' => 'nullable|string|max:7',
            'is_all_day' => 'nullable|boolean',
        ]);

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'type' => $request->type,
            'visibility' => $request->visibility,
            'target_class' => $request->target_class,
            'color' => $request->color ?? $this->getDefaultColor($request->type),
            'is_all_day' => $request->has('is_all_day'),
        ]);

        Toastr::success('Event updated successfully', 'Success');
        return redirect()->route('events.index');
    }

    /**
     * Remove the specified event
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        
        // Check if user can delete
        if ($event->created_by != Auth::id() && Auth::user()->role_name != 'Admin') {
            Toastr::error('Unauthorized to delete this event', 'Error');
            return redirect()->route('events.index');
        }

        $event->update(['is_active' => false]);
        Toastr::success('Event deleted successfully', 'Success');
        return redirect()->route('events.index');
    }

    /**
     * Bulk delete events
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'event_ids' => 'required|array',
            'event_ids.*' => 'required|integer|exists:events,id',
        ]);

        try {
            $eventIds = $request->event_ids;
            $events = Event::whereIn('id', $eventIds)->get();
            $deletedCount = 0;

            foreach ($events as $event) {
                // Check if user can delete
                if ($event->created_by == Auth::id() || Auth::user()->role_name == 'Admin') {
                    $event->update(['is_active' => false]);
                    $deletedCount++;
                }
            }

            Toastr::success("Successfully deleted {$deletedCount} event(s)", 'Success');
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} event(s)",
                'deleted_count' => $deletedCount
            ]);
        } catch(\Exception $e) {
            \Log::error('Bulk delete events failed: ' . $e->getMessage());
            Toastr::error('Failed to delete events', 'Error');
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete events'
            ], 500);
        }
    }

    /**
     * Get default color for event type
     */
    private function getDefaultColor($type)
    {
        $colors = [
            'holiday' => '#f59e0b',
            'exam' => '#ef4444',
            'event' => '#3b82f6',
            'meeting' => '#8b5cf6',
            'sports' => '#10b981',
            'cultural' => '#ec4899',
            'other' => '#6b7280',
        ];

        return $colors[$type] ?? '#3b82f6';
    }

    /**
     * Send event notifications to parents
     */
    private function sendEventNotifications($event)
    {
        try {
            $students = collect();

            // If event is class-specific, get students in that class
            if ($event->target_class) {
                $class = Classe::find($event->target_class);
                if ($class) {
                    $students = Student::where('class', $class->class_name)->get();
                }
            } elseif (in_array($event->type, ['opening', 'closing'])) {
                // School-wide events - notify all students
                $students = Student::all();
            } elseif ($event->visibility === 'parents' || $event->visibility === 'public') {
                // Notify all students for parent/public events
                $students = Student::all();
            }

            foreach ($students as $student) {
                $this->notificationService->sendEventNotification($student->id, [
                    'event_id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'date' => $event->start_date->format('Y-m-d'),
                    'time' => $event->start_time,
                    'location' => $event->location,
                    'type' => $event->type,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send event notifications', [
                'error' => $e->getMessage(),
                'event_id' => $event->id ?? null,
            ]);
        }
    }
}
