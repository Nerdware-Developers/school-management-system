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
                                    <tr class="{{ $notification->is_read ? '' : 'table-warning' }}" id="notification-row-{{ $notification->id }}">
                                        <td>
                                            @php
                                                $badgeColors = [
                                                    'info' => 'info',
                                                    'success' => 'success',
                                                    'warning' => 'warning',
                                                    'error' => 'danger',
                                                    'fee' => 'primary',
                                                    'attendance' => 'info',
                                                    'exam' => 'warning',
                                                    'event' => 'primary',
                                                    'school_date' => 'danger',
                                                    'parent_exam' => 'success',
                                                    'parent_event' => 'info',
                                                    'parent_fee' => 'warning'
                                                ];
                                                $badgeColor = $badgeColors[$notification->type] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badgeColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $notification->title }}</strong>
                                            @if(!$notification->is_read)
                                                <span class="badge bg-danger rounded-circle ms-1" style="width: 8px; height: 8px; padding: 0;"></span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($notification->message, 80) }}</td>
                                        <td>
                                            <small class="text-muted">{{ $notification->created_at->format('M d, Y') }}</small><br>
                                            <small class="text-muted">{{ $notification->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @if($notification->is_read)
                                                <span class="badge bg-success">Read</span>
                                            @else
                                                <span class="badge bg-warning">Unread</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$notification->is_read)
                                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" style="display: inline;" class="mark-read-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Mark as read">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($notification->link)
                                                <a href="{{ $notification->link }}" class="btn btn-sm btn-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" style="display: inline;" class="delete-notification-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this notification?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

@section('script')
<script>
    $(document).ready(function() {
        // Handle mark as read with AJAX
        $('.mark-read-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var notificationId = form.find('button').data('id') || form.attr('action').split('/').pop();
            var row = $('#notification-row-' + notificationId);
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function() {
                    row.removeClass('table-warning');
                    row.find('.badge.bg-warning').replaceWith('<span class="badge bg-success">Read</span>');
                    row.find('.mark-read-form').remove();
                    row.find('td:nth-child(2) .badge.bg-danger').remove();
                    toastr.success('Notification marked as read');
                },
                error: function() {
                    toastr.error('Failed to mark notification as read');
                }
            });
        });

        // Handle delete with AJAX
        $('.delete-notification-form').on('submit', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this notification?')) {
                return;
            }
            
            var form = $(this);
            var notificationId = form.attr('action').split('/').pop();
            var row = $('#notification-row-' + notificationId);
            
            $.ajax({
                url: form.attr('action'),
                method: 'DELETE',
                data: form.serialize(),
                success: function() {
                    row.fadeOut(300, function() {
                        $(this).remove();
                        // Check if table is empty
                        if ($('tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                    toastr.success('Notification deleted');
                },
                error: function() {
                    toastr.error('Failed to delete notification');
                }
            });
        });
    });
</script>
@endsection

