@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Edit Event</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('events.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('events.update', $event->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ $event->title }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Event Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="type" required>
                                    <option value="holiday" {{ $event->type == 'holiday' ? 'selected' : '' }}>Holiday</option>
                                    <option value="exam" {{ $event->type == 'exam' ? 'selected' : '' }}>Exam</option>
                                    <option value="event" {{ $event->type == 'event' ? 'selected' : '' }}>Event</option>
                                    <option value="meeting" {{ $event->type == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    <option value="sports" {{ $event->type == 'sports' ? 'selected' : '' }}>Sports</option>
                                    <option value="cultural" {{ $event->type == 'cultural' ? 'selected' : '' }}>Cultural</option>
                                    <option value="other" {{ $event->type == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="start_date" value="{{ $event->start_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" value="{{ $event->end_date ? $event->end_date->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_all_day" value="1" {{ $event->is_all_day ? 'checked' : '' }}> All Day Event
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6" id="timeFields" style="display: {{ $event->is_all_day ? 'none' : 'block' }};">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Start Time</label>
                                        <input type="time" class="form-control" name="start_time" value="{{ $event->start_time ? Carbon\Carbon::parse($event->start_time)->format('H:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>End Time</label>
                                        <input type="time" class="form-control" name="end_time" value="{{ $event->end_time ? Carbon\Carbon::parse($event->end_time)->format('H:i') : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Location</label>
                                <input type="text" class="form-control" name="location" value="{{ $event->location }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Visibility <span class="text-danger">*</span></label>
                                <select class="form-control" name="visibility" required>
                                    <option value="public" {{ $event->visibility == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="staff" {{ $event->visibility == 'staff' ? 'selected' : '' }}>Staff Only</option>
                                    <option value="students" {{ $event->visibility == 'students' ? 'selected' : '' }}>Students</option>
                                    <option value="parents" {{ $event->visibility == 'parents' ? 'selected' : '' }}>Parents</option>
                                    <option value="specific_class" {{ $event->visibility == 'specific_class' ? 'selected' : '' }}>Specific Class</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="classField" style="display: {{ $event->visibility == 'specific_class' ? 'block' : 'none' }};">
                            <div class="form-group">
                                <label>Target Class</label>
                                <select class="form-control" name="target_class">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ $event->target_class == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Color</label>
                                <input type="color" class="form-control" name="color" value="{{ $event->color }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="4">{{ $event->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update Event</button>
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

