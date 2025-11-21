@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Edit Route</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('transport.routes') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('transport.routes.update', $route->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Route Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="route_name" value="{{ $route->route_name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fare <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="fare" value="{{ $route->fare }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" name="start_time" value="{{ $route->start_time }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Time</label>
                                <input type="time" class="form-control" name="end_time" value="{{ $route->end_time }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Distance (km)</label>
                                <input type="number" class="form-control" name="distance_km" value="{{ $route->distance_km }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="3">{{ $route->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Assign Buses</label>
                                <select class="form-control select2" name="bus_ids[]" multiple>
                                    @foreach($buses as $bus)
                                        <option value="{{ $bus->id }}" {{ in_array($bus->id, $assignedBuses) ? 'selected' : '' }}>
                                            {{ $bus->bus_number }} - {{ $bus->driver_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h5>Route Stops</h5>
                            <div id="stopsContainer">
                                @foreach($route->stops as $stop)
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>{{ $stop->stop_name }}</strong>
                                                </div>
                                                <div class="col-md-3">
                                                    <small>Order: {{ $stop->stop_order }}</small>
                                                </div>
                                                <div class="col-md-5 text-end">
                                                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                                    <form action="{{ route('transport.stops.destroy', $stop->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="addStop()">Add Stop</button>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update Route</button>
                            <a href="{{ route('transport.routes') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

