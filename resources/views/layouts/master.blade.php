<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Admin Dashboard</title>
    <link rel="shortcut icon" href="{{ URL::to('assets/img/favicon.png') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/icons/flags/flags.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/icons/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/simple-calendar/simple-calendar.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('assets/css/style.css') }}">
	{{-- message toastr --}}
	<link rel="stylesheet" href="{{ URL::to('assets/css/toastr.min.css') }}">
	<script src="{{ URL::to('assets/js/toastr_jquery.min.js') }}"></script>
	<script src="{{ URL::to('assets/js/toastr.min.js') }}"></script>
	<style>
		.notification-message.unread {
			background-color: #f8f9fa;
			border-left: 3px solid #0d6efd;
		}
		.notification-message.unread:hover {
			background-color: #e9ecef;
		}
		.notification-message.read {
			opacity: 0.8;
		}
		.notification-message a {
			text-decoration: none;
			color: inherit;
		}
		.notification-message a:hover {
			color: inherit;
		}
		.noti-content {
			max-height: 400px;
			overflow-y: auto;
		}
		.notification-list {
			list-style: none;
			padding: 0;
			margin: 0;
		}
		.notification-list li {
			border-bottom: 1px solid #e9ecef;
		}
		.notification-list li:last-child {
			border-bottom: none;
		}
		#notificationBadge {
			animation: pulse 2s infinite;
		}
		@keyframes pulse {
			0% { transform: scale(1); }
			50% { transform: scale(1.1); }
			100% { transform: scale(1); }
		}
	</style>
</head>
<body>
    <div class="main-wrapper">
        <div class="header">
            <div class="header-left">
                <a href="{{ route('home') }}" class="logo">
                    <span style="font-size: 24px; font-weight: bold; color: #333;">studex</span>
                </a>
                <a href="{{ route('home') }}" class="logo logo-small">
                    <span style="font-size: 18px; font-weight: bold; color: #333;">studex</span>
                </a>
            </div>
            <div class="menu-toggle">
                <a href="javascript:void(0);" id="toggle_btn">
                    <i class="fas fa-bars"></i>
                </a>
            </div>

            <div class="top-nav-search">
                <form>
                    <input type="text" class="form-control" placeholder="Search here">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <a class="mobile_btn" id="mobile_btn">
                <i class="fas fa-bars"></i>
            </a>
            <ul class="nav user-menu">
                <li class="nav-item dropdown noti-dropdown me-2">
                    <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown" id="notificationDropdown" style="position: relative;">
                        <img src="{{ URL::to('assets/img/icons/header-icon-05.svg') }}" alt="">
                        <span class="badge rounded-pill bg-danger" id="notificationBadge" style="display: none; position: absolute; top: 0; right: 0; font-size: 10px; min-width: 18px; height: 18px; line-height: 18px; padding: 0 5px;">0</span>
                    </a>
                    <div class="dropdown-menu notifications" style="width: 350px; max-height: 500px; overflow-y: auto;">
                        <div class="topnav-dropdown-header d-flex justify-content-between align-items-center">
                            <span class="notification-title">Notifications</span>
                            <a href="javascript:void(0)" class="clear-noti" id="markAllRead" style="font-size: 12px;"> Clear All </a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list" id="notificationList">
                                <li class="text-center p-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mt-2 mb-0">Loading notifications...</p>
                                </li>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="{{ route('notifications.index') }}">View all Notifications</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item zoom-screen me-2">
                    <a href="#" class="nav-link header-nav-list win-maximize">
                        <img src="{{ URL::to('assets/img/icons/header-icon-04.svg') }}" alt="">
                    </a>
                </li>

                <li class="nav-item dropdown has-arrow new-user-menus">
                    <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="/images/{{ Session::get('avatar') }}" width="31"alt="">
                            <div class="user-text">
                                <h6>{{ Session::get('name') }}</h6>
                                <p class="text-muted mb-0">{{ Session::get('role_name') }}</p>
                            </div>
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <div class="user-header">
                            <div class="avatar avatar-sm">
                                <img src="/images/{{ Session::get('avatar') }}" alt="" class="avatar-img rounded-circle">
                            </div>
                            <div class="user-text">
                                <h6>{{ Session::get('name') }}</h6>
                                <p class="text-muted mb-0">{{ Session::get('role_name') }}</p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="{{ route('user/profile/page') }}">My Profile</a>
                        <a class="dropdown-item" href="{{ route('notifications.index') }}">Inbox</a>
                        <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
		{{-- side bar --}}
		@include('sidebar.sidebar')
		{{-- content page --}}
        @yield('content')
        <footer>
            <p>Copyright ©  <?php echo date('Y'); ?> Nerdware Developers.</p>
        </footer>
    
    </div>

    <script src="{{ URL::to('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ URL::to('assets/js/feather.min.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/apexchart/apexcharts.min.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/apexchart/chart-data.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/simple-calendar/jquery.simple-calendar.js') }}"></script>
    <script src="{{ URL::to('assets/js/calander.js') }}"></script>
    <script src="{{ URL::to('assets/js/circle-progress.min.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ URL::to('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::to('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::to('assets/js/script.js') }}"></script>
    @yield('script')
    <script>
        $(document).ready(function() {
            $('.select2s-hidden-accessible').select2({
                closeOnSelect: false
            });

            // Load notifications on page load
            loadNotifications();
            updateBadgeCount();

            // Refresh notifications every 30 seconds
            setInterval(function() {
                loadNotifications();
                updateBadgeCount();
            }, 30000);

            // Mark all as read
            $('#markAllRead').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '{{ route("notifications.mark-all-read") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        loadNotifications();
                        updateBadgeCount();
                        toastr.success('All notifications marked as read');
                    },
                    error: function() {
                        toastr.error('Failed to mark notifications as read');
                    }
                });
            });

            // Mark as read when clicking on notification
            $(document).on('click', '.notification-item', function(e) {
                var notificationId = $(this).data('id');
                var link = $(this).data('link');
                
                if (notificationId && !$(this).hasClass('read')) {
                    markAsRead(notificationId, $(this));
                }
                
                if (link && link !== '#') {
                    window.location.href = link;
                }
            });
        });

        function loadNotifications() {
            $.ajax({
                url: '{{ route("notifications.recent") }}',
                method: 'GET',
                success: function(notifications) {
                    var html = '';
                    if (!notifications || notifications.length === 0) {
                        html = '<li class="text-center p-4"><span class="text-muted"><i class="fas fa-bell-slash me-2"></i>No notifications</span></li>';
                    } else {
                        notifications.forEach(function(notif) {
                            var timeAgo = getTimeAgo(notif.created_at);
                            var icon = getNotificationIcon(notif.type);
                            var readClass = notif.is_read ? 'read' : 'unread';
                            var link = notif.link || '#';
                            
                            html += '<li class="notification-message ' + readClass + '">';
                            html += '<a href="javascript:void(0)" class="notification-item d-block" data-id="' + notif.id + '" data-link="' + link + '">';
                            html += '<div class="media d-flex">';
                            html += '<span class="avatar avatar-sm flex-shrink-0 me-2">';
                            html += '<div class="avatar-img rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: ' + getNotificationColor(notif.type) + ';">';
                            html += '<i class="' + icon + '" style="font-size: 18px;"></i>';
                            html += '</div>';
                            html += '</span>';
                            html += '<div class="media-body flex-grow-1">';
                            html += '<p class="noti-details mb-1"><span class="noti-title fw-bold">' + escapeHtml(notif.title) + '</span></p>';
                            html += '<p class="noti-message text-muted mb-1" style="font-size: 12px;">' + escapeHtml(notif.message.substring(0, 60)) + (notif.message.length > 60 ? '...' : '') + '</p>';
                            html += '<p class="noti-time mb-0"><span class="notification-time" style="font-size: 11px;">' + timeAgo + '</span></p>';
                            html += '</div>';
                            if (!notif.is_read) {
                                html += '<span class="badge bg-danger rounded-circle" style="width: 8px; height: 8px; padding: 0;"></span>';
                            }
                            html += '</div></a></li>';
                        });
                    }
                    $('#notificationList').html(html);
                },
                error: function() {
                    $('#notificationList').html('<li class="text-center p-3"><span class="text-danger">Failed to load notifications</span></li>');
                }
            });
        }

        function updateBadgeCount() {
            $.ajax({
                url: '{{ route("notifications.unread-count") }}',
                method: 'GET',
                success: function(data) {
                    if (data.count > 0) {
                        $('#notificationBadge').text(data.count > 99 ? '99+' : data.count).show();
                    } else {
                        $('#notificationBadge').hide();
                    }
                }
            });
        }

        function markAsRead(id, element) {
            $.ajax({
                url: '/notifications/' + id + '/read',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    if (element) {
                        element.closest('.notification-message').removeClass('unread').addClass('read');
                        element.find('.badge').remove();
                    }
                    updateBadgeCount();
                }
            });
        }

        function getTimeAgo(dateString) {
            var date = new Date(dateString);
            var now = new Date();
            var seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return 'Just now';
            var minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + (minutes === 1 ? ' min ago' : ' mins ago');
            var hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + (hours === 1 ? ' hour ago' : ' hours ago');
            var days = Math.floor(hours / 24);
            if (days < 7) return days + (days === 1 ? ' day ago' : ' days ago');
            var weeks = Math.floor(days / 7);
            if (weeks < 4) return weeks + (weeks === 1 ? ' week ago' : ' weeks ago');
            var months = Math.floor(days / 30);
            return months + (months === 1 ? ' month ago' : ' months ago');
        }

        function getNotificationIcon(type) {
            var icons = {
                'info': 'fas fa-info-circle',
                'success': 'fas fa-check-circle',
                'warning': 'fas fa-exclamation-triangle',
                'error': 'fas fa-times-circle',
                'fee': 'fas fa-money-bill-wave',
                'attendance': 'fas fa-user-check',
                'exam': 'fas fa-file-alt',
                'event': 'fas fa-calendar-alt',
                'school_date': 'fas fa-school',
                'parent_exam': 'fas fa-graduation-cap',
                'parent_event': 'fas fa-calendar-check',
                'parent_fee': 'fas fa-credit-card'
            };
            return icons[type] || 'fas fa-bell';
        }

        function getNotificationColor(type) {
            var colors = {
                'info': '#0dcaf0',
                'success': '#198754',
                'warning': '#ffc107',
                'error': '#dc3545',
                'fee': '#0d6efd',
                'attendance': '#20c997',
                'exam': '#fd7e14',
                'event': '#6f42c1',
                'school_date': '#e83e8c',
                'parent_exam': '#20c997',
                'parent_event': '#0dcaf0',
                'parent_fee': '#ffc107'
            };
            return colors[type] || '#6c757d';
        }

        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
</body>
</html>