@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Create Event</h4>
                <h6>Add a new event to the calendar</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('events.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('events.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Event Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="type" required>
                                    <option value="holiday">Holiday</option>
                                    <option value="exam">Exam</option>
                                    <option value="event">Event</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="sports">Sports</option>
                                    <option value="cultural">Cultural</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" value="{{ request('date') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_all_day" value="1" checked> All Day Event
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6" id="timeFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <input type="time" class="form-control" name="start_time">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <input type="time" class="form-control" name="end_time">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" class="form-control" name="location">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Visibility <span class="text-danger">*</span></label>
                                <select class="form-control" name="visibility" required>
                                    <option value="public">Public</option>
                                    <option value="staff">Staff Only</option>
                                    <option value="students">Students</option>
                                    <option value="parents">Parents</option>
                                    <option value="specific_class">Specific Class</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="classField" style="display: none;">
                            <div class="form-group">
                                <label>Target Class</label>
                                <select class="form-control" name="target_class">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Color</label>
                                <input type="color" class="form-control" name="color" value="#3b82f6">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="4"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Create Event</button>
                            <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('input[name="is_all_day"]').change(function() {
            if ($(this).is(':checked')) {
                $('#timeFields').hide();
            } else {
                $('#timeFields').show();
            }
        });

        $('select[name="visibility"]').change(function() {
            if ($(this).val() === 'specific_class') {
                $('#classField').show();
            } else {
                $('#classField').hide();
            }
        });
    });
</script>
@endsection

