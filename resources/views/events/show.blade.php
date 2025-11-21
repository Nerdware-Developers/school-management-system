@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Event Details</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('events.index') }}" class="btn btn-secondary">Back</a>
                @if($event->created_by == Auth::id() || Auth::user()->role_name == 'Admin')
                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary">Edit</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3>{{ $event->title }}</h3>
                        <p class="text-muted">Created by: {{ $event->creator->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Type:</strong> {{ ucfirst($event->type) }}</p>
                        <p><strong>Start Date:</strong> {{ $event->start_date->format('M d, Y') }}</p>
                        @if($event->end_date)
                            <p><strong>End Date:</strong> {{ $event->end_date->format('M d, Y') }}</p>
                        @endif
                        @if(!$event->is_all_day && $event->start_time)
                            <p><strong>Time:</strong> {{ Carbon\Carbon::parse($event->start_time)->format('h:i A') }}
                                @if($event->end_time)
                                    - {{ Carbon\Carbon::parse($event->end_time)->format('h:i A') }}
                                @endif
                            </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($event->location)
                            <p><strong>Location:</strong> {{ $event->location }}</p>
                        @endif
                        <p><strong>Visibility:</strong> {{ ucfirst($event->visibility) }}</p>
                    </div>
                    @if($event->description)
                        <div class="col-md-12">
                            <p><strong>Description:</strong></p>
                            <p>{{ $event->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

