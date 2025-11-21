@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Event Calendar</h4>
                <h6>View and manage school events</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('events.create') }}" class="btn btn-primary">Add Event</a>
                <button type="button" class="btn btn-secondary" onclick="toggleView()" id="toggleViewBtn">View List</button>
            </div>
        </div>

        <!-- Calendar View -->
        <div class="card" id="calendarView">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>

        <!-- List View -->
        <div class="card" id="listView" style="display: none;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($eventsList as $event)
                                <tr>
                                    <td>
                                        <strong>{{ $event['title'] }}</strong>
                                        @if($event['description'])
                                            <br><small class="text-muted">{{ strlen($event['description']) > 50 ? substr($event['description'], 0, 50) . '...' : $event['description'] }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $event['color'] }}; color: white;">
                                            {{ ucfirst($event['type']) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($event['start'])->format('M d, Y') }}</td>
                                    <td>{{ $event['end'] ? \Carbon\Carbon::parse($event['end'])->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $event['location'] ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('events.show', $event['id']) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No events found. <a href="{{ route('events.create') }}">Create one now</a></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<link rel="stylesheet" href="{{ URL::to('assets/plugins/fullcalendar/fullcalendar.min.css') }}">
<script src="{{ URL::to('assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ URL::to('assets/plugins/fullcalendar/fullcalendar.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: '{{ route("events.json") }}',
            eventClick: function(event) {
                if (event.url) {
                    window.location.href = event.url;
                }
            },
            dayClick: function(date, jsEvent, view) {
                var dateStr = date.format('YYYY-MM-DD');
                window.location.href = '{{ route("events.create") }}?date=' + dateStr;
            },
            eventRender: function(event, element) {
                element.css('background-color', event.color);
                element.css('border-color', event.color);
            }
        });
    });

    function toggleView() {
        var calendarView = document.getElementById('calendarView');
        var listView = document.getElementById('listView');
        var btn = document.getElementById('toggleViewBtn');
        
        if (calendarView.style.display === 'none') {
            calendarView.style.display = 'block';
            listView.style.display = 'none';
            btn.textContent = 'View List';
            $('#calendar').fullCalendar('render');
        } else {
            calendarView.style.display = 'none';
            listView.style.display = 'block';
            btn.textContent = 'View Calendar';
        }
    }
</script>
@endsection

