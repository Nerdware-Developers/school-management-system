@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Assign Student to Bus</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('transport.assignments') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('transport.assignments.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Student <span class="text-danger">*</span></label>
                                <select class="form-control" name="student_id" required>
                                    <option value="">Select Student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}">
                                            {{ $student->first_name }} {{ $student->last_name }} - {{ $student->class }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bus <span class="text-danger">*</span></label>
                                <select class="form-control" name="bus_id" id="busSelect" required>
                                    <option value="">Select Bus</option>
                                    @foreach($buses as $bus)
                                        <option value="{{ $bus->id }}" data-routes="{{ $bus->routes->pluck('id')->toJson() }}">
                                            {{ $bus->bus_number }} - {{ $bus->driver_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Route <span class="text-danger">*</span></label>
                                <select class="form-control" name="route_id" id="routeSelect" required>
                                    <option value="">Select Route</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id }}">
                                            {{ $route->route_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Stop</label>
                                <select class="form-control" name="stop_id" id="stopSelect">
                                    <option value="">Select Stop</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Assigned Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="assigned_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Assign Student</button>
                            <a href="{{ route('transport.assignments') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#routeSelect').change(function() {
            var routeId = $(this).val();
            if (routeId) {
                $.get('/transport/routes/' + routeId + '/stops', function(stops) {
                    var html = '<option value="">Select Stop</option>';
                    stops.forEach(function(stop) {
                        html += '<option value="' + stop.id + '">' + stop.stop_name + '</option>';
                    });
                    $('#stopSelect').html(html);
                });
            } else {
                $('#stopSelect').html('<option value="">Select Stop</option>');
            }
        });
    });
</script>
@endsection

