@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Notifications</h4>
                <h6>Manage your notifications</h6>
            </div>
            <div class="page-btn">
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">Mark All as Read</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($notifications->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                    <tr class="{{ $notification->is_read ? '' : 'table-warning' }}">
                                        <td>
                                            <span class="badge bg-{{ $notification->type === 'error' ? 'danger' : ($notification->type === 'success' ? 'success' : 'info') }}">
                                                {{ ucfirst($notification->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $notification->title }}</td>
                                        <td>{{ Str::limit($notification->message, 50) }}</td>
                                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                                        <td>
                                            @if($notification->is_read)
                                                <span class="badge bg-success">Read</span>
                                            @else
                                                <span class="badge bg-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($notification->link)
                                                <a href="{{ $notification->link }}" class="btn btn-sm btn-primary">View</a>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center p-5">
                        <p class="text-muted">No notifications found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

