<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Student;
use App\Models\StudentBusAssignment;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    // ==================== BUSES ====================

    /**
     * Display list of buses
     */
    public function buses()
    {
        $buses = Bus::withCount('activeAssignments')->orderBy('bus_number')->paginate(15);
        return view('transport.buses.index', compact('buses'));
    }

    /**
     * Show form to create bus
     */
    public function createBus()
    {
        return view('transport.buses.create');
    }

    /**
     * Store new bus
     */
    public function storeBus(Request $request)
    {
        $request->validate([
            'bus_number' => 'required|string|unique:buses,bus_number',
            'bus_name' => 'nullable|string|max:255',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'driver_license' => 'nullable|string|max:100',
            'conductor_name' => 'nullable|string|max:255',
            'conductor_phone' => 'nullable|string|max:20',
            'capacity' => 'required|integer|min:1',
            'vehicle_type' => 'required|in:bus,van,minibus',
            'registration_number' => 'required|string|unique:buses,registration_number',
            'notes' => 'nullable|string',
        ]);

        Bus::create($request->all());

        Toastr::success('Bus added successfully', 'Success');
        return redirect()->route('transport.buses');
    }

    /**
     * Show form to edit bus
     */
    public function editBus($id)
    {
        $bus = Bus::findOrFail($id);
        return view('transport.buses.edit', compact('bus'));
    }

    /**
     * Update bus
     */
    public function updateBus(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        $request->validate([
            'bus_number' => 'required|string|unique:buses,bus_number,' . $id,
            'bus_name' => 'nullable|string|max:255',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'driver_license' => 'nullable|string|max:100',
            'conductor_name' => 'nullable|string|max:255',
            'conductor_phone' => 'nullable|string|max:20',
            'capacity' => 'required|integer|min:1',
            'vehicle_type' => 'required|in:bus,van,minibus',
            'registration_number' => 'required|string|unique:buses,registration_number,' . $id,
            'notes' => 'nullable|string',
        ]);

        $bus->update($request->all());

        Toastr::success('Bus updated successfully', 'Success');
        return redirect()->route('transport.buses');
    }

    /**
     * Delete bus
     */
    public function destroyBus($id)
    {
        $bus = Bus::findOrFail($id);
        
        if ($bus->activeAssignments()->count() > 0) {
            Toastr::error('Cannot delete bus with active student assignments', 'Error');
            return redirect()->back();
        }

        $bus->delete();
        Toastr::success('Bus deleted successfully', 'Success');
        return redirect()->route('transport.buses');
    }

    // ==================== ROUTES ====================

    /**
     * Display list of routes
     */
    public function routes()
    {
        $routes = Route::with('stops')->withCount('studentAssignments')->orderBy('route_name')->paginate(15);
        return view('transport.routes.index', compact('routes'));
    }

    /**
     * Show form to create route
     */
    public function createRoute()
    {
        return view('transport.routes.create');
    }

    /**
     * Store new route
     */
    public function storeRoute(Request $request)
    {
        $request->validate([
            'route_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fare' => 'required|numeric|min:0',
            'distance_km' => 'nullable|integer|min:0',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $route = Route::create($request->all());

        Toastr::success('Route created successfully', 'Success');
        return redirect()->route('transport.routes.edit', $route->id);
    }

    /**
     * Show form to edit route
     */
    public function editRoute($id)
    {
        $route = Route::with('stops')->findOrFail($id);
        $buses = Bus::where('is_active', true)->get();
        $assignedBuses = $route->buses()->pluck('buses.id')->toArray();
        return view('transport.routes.edit', compact('route', 'buses', 'assignedBuses'));
    }

    /**
     * Update route
     */
    public function updateRoute(Request $request, $id)
    {
        $route = Route::findOrFail($id);

        $request->validate([
            'route_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fare' => 'required|numeric|min:0',
            'distance_km' => 'nullable|integer|min:0',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'bus_ids' => 'nullable|array',
            'bus_ids.*' => 'exists:buses,id',
        ]);

        $route->update($request->except('bus_ids'));

        // Sync buses
        if ($request->has('bus_ids')) {
            $route->buses()->sync($request->bus_ids);
        } else {
            $route->buses()->detach();
        }

        Toastr::success('Route updated successfully', 'Success');
        return redirect()->route('transport.routes');
    }

    /**
     * Delete route
     */
    public function destroyRoute($id)
    {
        $route = Route::findOrFail($id);
        
        if ($route->studentAssignments()->count() > 0) {
            Toastr::error('Cannot delete route with active student assignments', 'Error');
            return redirect()->back();
        }

        $route->delete();
        Toastr::success('Route deleted successfully', 'Success');
        return redirect()->route('transport.routes');
    }

    // ==================== ROUTE STOPS ====================

    /**
     * Store route stop
     */
    public function storeStop(Request $request, $routeId)
    {
        $route = Route::findOrFail($routeId);

        $request->validate([
            'stop_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'pickup_time' => 'nullable|date_format:H:i',
            'dropoff_time' => 'nullable|date_format:H:i',
            'stop_order' => 'required|integer|min:1',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        RouteStop::create([
            'route_id' => $routeId,
            'stop_name' => $request->stop_name,
            'address' => $request->address,
            'pickup_time' => $request->pickup_time,
            'dropoff_time' => $request->dropoff_time,
            'stop_order' => $request->stop_order,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        Toastr::success('Stop added successfully', 'Success');
        return redirect()->back();
    }

    /**
     * Update route stop
     */
    public function updateStop(Request $request, $id)
    {
        $stop = RouteStop::findOrFail($id);

        $request->validate([
            'stop_name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'pickup_time' => 'nullable|date_format:H:i',
            'dropoff_time' => 'nullable|date_format:H:i',
            'stop_order' => 'required|integer|min:1',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $stop->update($request->all());

        Toastr::success('Stop updated successfully', 'Success');
        return redirect()->back();
    }

    /**
     * Delete route stop
     */
    public function destroyStop($id)
    {
        $stop = RouteStop::findOrFail($id);
        
        if ($stop->studentAssignments()->count() > 0) {
            Toastr::error('Cannot delete stop with active student assignments', 'Error');
            return redirect()->back();
        }

        $stop->delete();
        Toastr::success('Stop deleted successfully', 'Success');
        return redirect()->back();
    }

    // ==================== STUDENT ASSIGNMENTS ====================

    /**
     * Display student bus assignments
     */
    public function assignments()
    {
        $assignments = StudentBusAssignment::with(['student', 'bus', 'route', 'stop'])
            ->orderBy('assigned_date', 'desc')
            ->paginate(20);

        return view('transport.assignments.index', compact('assignments'));
    }

    /**
     * Show form to assign student to bus
     */
    public function createAssignment()
    {
        $students = Student::orderBy('first_name')->get();
        $buses = Bus::where('is_active', true)->with('routes')->get();
        $routes = Route::where('is_active', true)->with('stops')->get();
        
        return view('transport.assignments.create', compact('students', 'buses', 'routes'));
    }

    /**
     * Store student bus assignment
     */
    public function storeAssignment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'stop_id' => 'nullable|exists:route_stops,id',
            'assigned_date' => 'required|date',
            'end_date' => 'nullable|date|after:assigned_date',
            'notes' => 'nullable|string',
        ]);

        // Check if student already has active assignment
        $existing = StudentBusAssignment::where('student_id', $request->student_id)
            ->where('status', 'active')
            ->first();

        if ($existing) {
            Toastr::warning('Student already has an active bus assignment. Please end the current assignment first.', 'Warning');
            return redirect()->back()->withInput();
        }

        // Check bus capacity
        $bus = Bus::findOrFail($request->bus_id);
        $activeCount = StudentBusAssignment::where('bus_id', $request->bus_id)
            ->where('status', 'active')
            ->count();

        if ($activeCount >= $bus->capacity) {
            Toastr::error('Bus is at full capacity', 'Error');
            return redirect()->back()->withInput();
        }

        StudentBusAssignment::create([
            'student_id' => $request->student_id,
            'bus_id' => $request->bus_id,
            'route_id' => $request->route_id,
            'stop_id' => $request->stop_id,
            'assigned_date' => $request->assigned_date,
            'end_date' => $request->end_date,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        Toastr::success('Student assigned to bus successfully', 'Success');
        return redirect()->route('transport.assignments');
    }

    /**
     * Update student bus assignment
     */
    public function updateAssignment(Request $request, $id)
    {
        $assignment = StudentBusAssignment::findOrFail($id);

        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'stop_id' => 'nullable|exists:route_stops,id',
            'status' => 'required|in:active,inactive,suspended',
            'end_date' => 'nullable|date|after:assigned_date',
            'notes' => 'nullable|string',
        ]);

        $assignment->update($request->all());

        Toastr::success('Assignment updated successfully', 'Success');
        return redirect()->route('transport.assignments');
    }

    /**
     * Delete student bus assignment
     */
    public function destroyAssignment($id)
    {
        $assignment = StudentBusAssignment::findOrFail($id);
        $assignment->delete();

        Toastr::success('Assignment deleted successfully', 'Success');
        return redirect()->route('transport.assignments');
    }

    /**
     * Get stops for a route (AJAX)
     */
    public function getRouteStops($routeId)
    {
        $stops = RouteStop::where('route_id', $routeId)
            ->orderBy('stop_order')
            ->get();

        return response()->json($stops);
    }
}
