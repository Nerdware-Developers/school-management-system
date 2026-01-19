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
	
	<!-- Dark Mode - Apply immediately to prevent flash -->
	<script>
		(function() {
			// Check for dark mode preference immediately, before page renders
			var darkMode = localStorage.getItem('darkMode') === 'true';
			var html = document.documentElement;
			
			if (darkMode) {
				html.classList.add('dark-mode');
			}
			
			// Add a data attribute that CSS can use, and wait for body to exist
			html.setAttribute('data-dark-mode', darkMode ? 'true' : 'false');
			
			// Wait for body to exist, then apply class
			if (document.body) {
				if (darkMode) {
					document.body.classList.add('dark-mode');
				}
				document.body.classList.add('dark-mode-initialized');
			} else {
				// If body doesn't exist yet, wait for it
				document.addEventListener('DOMContentLoaded', function() {
					if (darkMode) {
						document.body.classList.add('dark-mode');
					}
					document.body.classList.add('dark-mode-initialized');
				});
			}
		})();
	</script>
	
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

		/* ============================================
		   NUCLEAR DARK MODE - CATCHES EVERYTHING
		   ============================================ */
		
		/* Step 1: Override ALL backgrounds first (nuclear) */
		html.dark-mode *,
		html[data-dark-mode="true"] *,
		body.dark-mode * {
			background-color: transparent !important;
		}
		
		/* Step 1b: Immediately restore dark backgrounds for UI components */
		body.dark-mode .card,
		body.dark-mode .card-body,
		body.dark-mode .modal,
		body.dark-mode .modal-content,
		body.dark-mode .dropdown-menu,
		body.dark-mode .select2-dropdown,
		body.dark-mode .toast,
		body.dark-mode .badge,
		body.dark-mode [class*="card"],
		body.dark-mode [class*="modal"],
		body.dark-mode [class*="dropdown"],
		body.dark-mode [class*="select2"] {
			background-color: rgba(30, 41, 59, 0.8) !important;
			background: rgba(30, 41, 59, 0.8) !important;
		}

		/* Step 2: Apply dark background to body and html */
		html[data-dark-mode="true"] body,
		body.dark-mode,
		html.dark-mode body,
		html.dark-mode,
		html[data-dark-mode="true"] {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
			background-color: #0f172a !important;
			color: #e2e8f0;
		}
		
		/* Step 3: Apply dark backgrounds to main structural elements */
		body.dark-mode .main-wrapper,
		body.dark-mode .page-wrapper,
		body.dark-mode .content,
		body.dark-mode .content.container-fluid,
		body.dark-mode .container,
		body.dark-mode .container-fluid {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
			background-color: transparent !important;
		}

		/* Override external CSS white backgrounds */
		body.dark-mode .main-wrapper,
		body.dark-mode .content,
		body.dark-mode .content.container-fluid,
		body.dark-mode .student-group-form,
		body.dark-mode .card-table,
		body.dark-mode .table-responsive {
			background-color: transparent !important;
			background: transparent !important;
		}

		/* Specific overrides for common white background elements */
		body.dark-mode [style*="background-color: white"],
		body.dark-mode [style*="background-color:#fff"],
		body.dark-mode [style*="background-color: #fff"],
		body.dark-mode [style*="background-color:#ffffff"],
		body.dark-mode [style*="background-color: #ffffff"],
		body.dark-mode [style*="background: white"],
		body.dark-mode [style*="background:#fff"],
		body.dark-mode [style*="background: #fff"],
		body.dark-mode [style*="background:#ffffff"],
		body.dark-mode [style*="background: #ffffff"],
		body.dark-mode [style*="background-color: #f7f7fa"],
		body.dark-mode [style*="background-color:#f7f7fa"],
		body.dark-mode [style*="background: #f7f7fa"],
		body.dark-mode [style*="background:#f7f7fa"] {
			background-color: transparent !important;
			background: transparent !important;
		}

		/* Override any element with class that might have white background */
		body.dark-mode .bg-white,
		body.dark-mode .white-bg,
		body.dark-mode [class*="white"],
		body.dark-mode [class*="bg-light"],
		body.dark-mode [class*="light-bg"] {
			background-color: rgba(30, 41, 59, 0.8) !important;
			background: rgba(30, 41, 59, 0.8) !important;
		}

		body.dark-mode .main-wrapper {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
			background-color: #0f172a !important;
		}

		/* Override external CSS body background - Most aggressive */
		body.dark-mode,
		html.dark-mode body,
		html[data-dark-mode="true"] body {
			background-color: #0f172a !important;
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
		}

		/* Now apply dark backgrounds to specific structural elements */
		body.dark-mode .main-wrapper,
		body.dark-mode .page-wrapper,
		body.dark-mode .content,
		body.dark-mode .content.container-fluid,
		body.dark-mode .container,
		body.dark-mode .container-fluid,
		body.dark-mode section,
		body.dark-mode .wrapper,
		body.dark-mode .content-wrapper,
		body.dark-mode .content-area {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
			background-color: transparent !important;
		}

		/* Override ALL divs except specific UI components */
		body.dark-mode div:not(.card):not(.modal-content):not(.dropdown-menu):not(.select2-dropdown):not(.toast):not(.badge):not(.btn) {
			background-color: transparent !important;
			background: transparent !important;
		}

		/* Apply dark backgrounds to UI components */
		body.dark-mode .card,
		body.dark-mode .card-body,
		body.dark-mode .modal-content,
		body.dark-mode .dropdown-menu,
		body.dark-mode .select2-dropdown,
		body.dark-mode .panel,
		body.dark-mode .well,
		body.dark-mode .box,
		body.dark-mode .widget,
		body.dark-mode [class*="panel"],
		body.dark-mode [class*="widget"],
		body.dark-mode [class*="box"] {
			background-color: rgba(30, 41, 59, 0.8) !important;
			background: rgba(30, 41, 59, 0.8) !important;
		}

		/* Override inline styles with white backgrounds */
		body.dark-mode [style*="background-color: white"],
		body.dark-mode [style*="background-color:#fff"],
		body.dark-mode [style*="background-color: #fff"],
		body.dark-mode [style*="background-color:#ffffff"],
		body.dark-mode [style*="background-color: #ffffff"],
		body.dark-mode [style*="background: white"],
		body.dark-mode [style*="background:#fff"],
		body.dark-mode [style*="background: #fff"],
		body.dark-mode [style*="background:#ffffff"],
		body.dark-mode [style*="background: #ffffff"],
		body.dark-mode [style*="background-color: #f7f7fa"],
		body.dark-mode [style*="background-color:#f7f7fa"],
		body.dark-mode [style*="background: #f7f7fa"],
		body.dark-mode [style*="background:#f7f7fa"],
		body.dark-mode [style*="background-color: #f9f9ff"],
		body.dark-mode [style*="background-color:#f9f9ff"],
		body.dark-mode [style*="background: #f9f9ff"],
		body.dark-mode [style*="background:#f9f9ff"] {
			background-color: transparent !important;
			background: transparent !important;
		}

		/* Header - Sleek dark with subtle gradient */
		body.dark-mode .header {
			background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
			border-bottom: 1px solid rgba(148, 163, 184, 0.1);
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
		}

		/* Header Left - Remove any white backgrounds - Aggressive override */
		body.dark-mode .header,
		body.dark-mode .header *,
		body.dark-mode .header > *,
		body.dark-mode .header div,
		body.dark-mode .header a {
			background-color: transparent !important;
			background: transparent !important;
		}

		body.dark-mode .header-left {
			background: transparent !important;
			background-color: transparent !important;
		}

		body.dark-mode .header-left * {
			background: transparent !important;
			background-color: transparent !important;
		}

		body.dark-mode .header-left .logo {
			background: transparent !important;
			background-color: transparent !important;
		}

		body.dark-mode .header-left .logo,
		body.dark-mode .header-left .logo * {
			background: transparent !important;
			background-color: transparent !important;
		}

		body.dark-mode .header-left .logo span {
			color: #ffffff !important;
			text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
			background: transparent !important;
		}

		body.dark-mode .header-left .logo span[style*="color: #333"],
		body.dark-mode .header-left .logo span[style*="color:#333"],
		body.dark-mode .header-left .logo span[style*="color: #333;"],
		body.dark-mode .header-left .logo span[style*="color:#333;"] {
			color: #ffffff !important;
		}

		body.dark-mode .logo-small,
		body.dark-mode .logo-small * {
			background: transparent !important;
			background-color: transparent !important;
		}

		body.dark-mode .logo-small span {
			color: #ffffff !important;
			background: transparent !important;
		}

		body.dark-mode .logo-small span[style*="color: #333"],
		body.dark-mode .logo-small span[style*="color:#333"],
		body.dark-mode .logo-small span[style*="color: #333;"],
		body.dark-mode .logo-small span[style*="color:#333;"] {
			color: #ffffff !important;
		}

		/* Menu toggle and other header elements */
		body.dark-mode .menu-toggle,
		body.dark-mode .menu-toggle * {
			background: transparent !important;
		}

		body.dark-mode .menu-toggle a {
			color: #cbd5e1;
		}

		body.dark-mode .menu-toggle a:hover {
			color: #60a5fa;
		}

		/* Sidebar - Modern dark with accent */
		body.dark-mode .sidebar {
			background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
			border-right: 1px solid rgba(148, 163, 184, 0.1);
		}

		body.dark-mode .sidebar-menu ul li a {
			color: #cbd5e1;
			transition: all 0.3s ease;
		}

		body.dark-mode .sidebar-menu ul li a:hover {
			background: linear-gradient(90deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
			color: #60a5fa;
			border-left: 3px solid #3b82f6;
		}

		body.dark-mode .sidebar-menu ul li.active > a {
			background: linear-gradient(90deg, rgba(59, 130, 246, 0.2) 0%, rgba(59, 130, 246, 0.1) 100%);
			color: #60a5fa;
			border-left: 3px solid #3b82f6;
			box-shadow: inset 0 0 10px rgba(59, 130, 246, 0.1);
		}

		/* Page Wrapper - Elegant dark background */
		body.dark-mode .page-wrapper {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
			color: #e2e8f0;
		}

		body.dark-mode .page-wrapper[style*="background-color"],
		body.dark-mode .page-wrapper[style*="background-color: #f9f9ff"],
		body.dark-mode .page-wrapper[style*="background-color:#f9f9ff"],
		body.dark-mode .page-wrapper[style*="background: #f9f9ff"],
		body.dark-mode .page-wrapper[style*="background:#f9f9ff"] {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
		}

		body.dark-mode .container,
		body.dark-mode .container-fluid {
			background-color: transparent !important;
		}

		/* Override any content container that might have white background */
		body.dark-mode .content,
		body.dark-mode .content * {
			background-color: transparent !important;
		}

		/* Typography - Better contrast */
		body.dark-mode h1,
		body.dark-mode h2,
		body.dark-mode h3,
		body.dark-mode h4,
		body.dark-mode h5,
		body.dark-mode h6 {
			color: #f1f5f9 !important;
			font-weight: 600;
		}

		body.dark-mode p {
			color: #cbd5e1;
		}

		/* Cards - Premium glassmorphism effect */
		body.dark-mode .card {
			background: rgba(30, 41, 59, 0.8) !important;
			backdrop-filter: blur(10px);
			border: 1px solid rgba(148, 163, 184, 0.1);
			border-radius: 12px;
			color: #e2e8f0;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
			transition: all 0.3s ease;
		}

		body.dark-mode .card:hover {
			transform: translateY(-2px);
			box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
			border-color: rgba(59, 130, 246, 0.3);
		}

		body.dark-mode .card[style*="background-color"] {
			background: rgba(30, 41, 59, 0.8) !important;
			backdrop-filter: blur(10px);
		}

		/* Dashboard cards with subtle color hints */
		body.dark-mode .card[style*="background-color:#f3e8ff"],
		body.dark-mode .card[style*="background-color:#e0f2fe"],
		body.dark-mode .card[style*="background-color:#dcfce7"],
		body.dark-mode .card[style*="background-color:#fef3c7"],
		body.dark-mode .card[style*="background-color:#fce7f3"],
		body.dark-mode .card[style*="background-color:#e0e7ff"],
		body.dark-mode .card[style*="background-color:#f0fdf4"],
		body.dark-mode .card[style*="background-color:#fff7ed"],
		body.dark-mode .card[style*="background-color:#e0f2fe"],
		body.dark-mode .card[style*="background-color:#fee2e2"],
		body.dark-mode .card[style*="background-color:#fff7e6"] {
			background: rgba(30, 41, 59, 0.9) !important;
			border: 1px solid rgba(148, 163, 184, 0.15) !important;
			backdrop-filter: blur(10px);
		}

		body.dark-mode .card-header {
			background: rgba(15, 23, 42, 0.5);
			border-bottom: 1px solid rgba(148, 163, 184, 0.1);
			color: #f1f5f9;
			border-radius: 12px 12px 0 0;
		}

		/* Form Controls - Modern input styling */
		body.dark-mode .form-control {
			background: rgba(15, 23, 42, 0.6);
			border: 1px solid rgba(148, 163, 184, 0.2);
			color: #e2e8f0;
			transition: all 0.3s ease;
		}

		body.dark-mode .form-control:focus {
			background: rgba(15, 23, 42, 0.8);
			border-color: #3b82f6;
			color: #f1f5f9;
			box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
		}

		body.dark-mode .form-control::placeholder {
			color: #94a3b8;
		}

		/* Tables - Elegant dark styling */
		body.dark-mode .table {
			color: #e2e8f0;
		}

		body.dark-mode .table thead th {
			background: rgba(15, 23, 42, 0.8);
			color: #f1f5f9;
			border-color: rgba(148, 163, 184, 0.1);
			font-weight: 600;
		}

		body.dark-mode .table tbody tr {
			border-color: rgba(148, 163, 184, 0.1);
			transition: all 0.2s ease;
		}

		body.dark-mode .table tbody tr:hover {
			background: rgba(59, 130, 246, 0.1);
			transform: scale(1.01);
		}

		/* Dropdowns - Modern dark menu */
		body.dark-mode .dropdown-menu {
			background: rgba(30, 41, 59, 0.95);
			backdrop-filter: blur(10px);
			border: 1px solid rgba(148, 163, 184, 0.1);
			box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
			border-radius: 8px;
		}

		body.dark-mode .dropdown-item {
			color: #cbd5e1;
			transition: all 0.2s ease;
		}

		body.dark-mode .dropdown-item:hover {
			background: rgba(59, 130, 246, 0.2);
			color: #60a5fa;
		}

		/* Breadcrumbs */
		body.dark-mode .breadcrumb-item a {
			color: #94a3b8;
			transition: color 0.2s ease;
		}

		body.dark-mode .breadcrumb-item a:hover {
			color: #60a5fa;
		}

		body.dark-mode .breadcrumb-item.active {
			color: #e2e8f0;
		}

		body.dark-mode .text-muted {
			color: #94a3b8 !important;
		}

		body.dark-mode .fw-bold {
			color: #f1f5f9;
		}

		/* Shadows - Enhanced depth */
		body.dark-mode .shadow-sm {
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3) !important;
		}

		body.dark-mode .border-0 {
			border-color: rgba(148, 163, 184, 0.1) !important;
		}

		body.dark-mode footer {
			background-color: #2d2d2d;
			color: #e0e0e0;
			border-top: 1px solid #404040;
		}

		/* Buttons - Modern gradient effects */
		body.dark-mode .btn-primary {
			background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
			border: none;
			box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
			transition: all 0.3s ease;
		}

		body.dark-mode .btn-primary:hover {
			background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
			box-shadow: 0 6px 12px -2px rgba(59, 130, 246, 0.4);
			transform: translateY(-1px);
		}

		body.dark-mode .btn-secondary {
			background: linear-gradient(135deg, #64748b 0%, #475569 100%);
			border: none;
			box-shadow: 0 4px 6px -1px rgba(100, 116, 139, 0.3);
		}

		body.dark-mode .btn-secondary:hover {
			background: linear-gradient(135deg, #475569 0%, #334155 100%);
			transform: translateY(-1px);
		}

		body.dark-mode .btn-danger {
			background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
			border: none;
			box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
		}

		body.dark-mode .btn-danger:hover {
			background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
			transform: translateY(-1px);
		}

		body.dark-mode .btn-success {
			background: linear-gradient(135deg, #10b981 0%, #059669 100%);
			border: none;
			box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
		}

		body.dark-mode .btn-success:hover {
			background: linear-gradient(135deg, #059669 0%, #047857 100%);
			transform: translateY(-1px);
		}

		body.dark-mode .btn-info {
			background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
			border: none;
			box-shadow: 0 4px 6px -1px rgba(6, 182, 212, 0.3);
		}

		body.dark-mode .btn-info:hover {
			background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%);
			transform: translateY(-1px);
		}

		/* Modals - Premium dark styling */
		body.dark-mode .modal-content {
			background: rgba(30, 41, 59, 0.95);
			backdrop-filter: blur(10px);
			border: 1px solid rgba(148, 163, 184, 0.1);
			border-radius: 12px;
			box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
		}

		body.dark-mode .modal-header {
			border-bottom: 1px solid rgba(148, 163, 184, 0.1);
			background: rgba(15, 23, 42, 0.5);
		}

		body.dark-mode .modal-footer {
			border-top: 1px solid rgba(148, 163, 184, 0.1);
			background: rgba(15, 23, 42, 0.3);
		}

		/* Badges - Vibrant colors */
		body.dark-mode .badge {
			color: #ffffff;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
		}

		/* Select2 - Modern dark styling */
		body.dark-mode .select2-container--default .select2-selection--single {
			background: rgba(15, 23, 42, 0.6);
			border: 1px solid rgba(148, 163, 184, 0.2);
			border-radius: 6px;
		}

		body.dark-mode .select2-container--default .select2-selection--single .select2-selection__rendered {
			color: #e2e8f0;
		}

		body.dark-mode .select2-dropdown {
			background: rgba(30, 41, 59, 0.95);
			backdrop-filter: blur(10px);
			border: 1px solid rgba(148, 163, 184, 0.1);
			border-radius: 8px;
			box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
		}

		body.dark-mode .select2-results__option {
			color: #cbd5e1;
			transition: all 0.2s ease;
		}

		body.dark-mode .select2-results__option--highlighted {
			background: rgba(59, 130, 246, 0.2);
			color: #60a5fa;
		}

		body.dark-mode .page-header {
			background: rgba(15, 23, 42, 0.5);
			border-bottom: 1px solid rgba(148, 163, 184, 0.1);
			border-radius: 8px;
			padding: 1rem;
			margin-bottom: 1.5rem;
		}

		body.dark-mode .breadcrumb {
			background-color: transparent;
		}

		/* Chart containers and graph areas */
		body.dark-mode .card-body {
			background: rgba(30, 41, 59, 0.6);
		}

		body.dark-mode .row {
			background-color: transparent;
		}

		/* Override any light background divs */
		body.dark-mode div[style*="background-color:#f9f9ff"],
		body.dark-mode div[style*="background-color: #f9f9ff"],
		body.dark-mode div[style*="background:#f9f9ff"],
		body.dark-mode div[style*="background: #f9f9ff"],
		body.dark-mode div[style*="background-color:white"],
		body.dark-mode div[style*="background-color:#fff"],
		body.dark-mode div[style*="background-color: #fff"],
		body.dark-mode div[style*="background:white"],
		body.dark-mode div[style*="background:#fff"],
		body.dark-mode div[style*="background: #fff"] {
			background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
			background-color: transparent !important;
		}

		/* Icon circles - Enhanced with glow */
		body.dark-mode .rounded-circle[style*="background-color"] {
			opacity: 0.9;
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
		}

		body.dark-mode .student-thread th {
			background: rgba(15, 23, 42, 0.8);
			color: #f1f5f9;
			font-weight: 600;
		}

		body.dark-mode .comman-shadow {
			box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
		}

		body.dark-mode .nav-link {
			color: #cbd5e1;
			transition: color 0.2s ease;
		}

		body.dark-mode .nav-link:hover {
			color: #60a5fa;
		}

		body.dark-mode .top-nav-search .form-control {
			background: rgba(15, 23, 42, 0.6);
			border: 1px solid rgba(148, 163, 184, 0.2);
			color: #e2e8f0;
			border-radius: 8px;
		}

		body.dark-mode .top-nav-search .form-control:focus {
			border-color: #3b82f6;
			box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
		}

		body.dark-mode .top-nav-search .form-control::placeholder {
			color: #94a3b8;
		}

		body.dark-mode .user-text h6 {
			color: #f1f5f9;
		}

		body.dark-mode .user-text p {
			color: #94a3b8;
		}

		body.dark-mode .notification-message {
			background: rgba(30, 41, 59, 0.8);
			border: 1px solid rgba(148, 163, 184, 0.1);
			transition: all 0.2s ease;
		}

		body.dark-mode .notification-message.unread {
			background: rgba(59, 130, 246, 0.1);
			border-left: 3px solid #3b82f6;
		}

		body.dark-mode .notification-message.unread:hover {
			background: rgba(59, 130, 246, 0.15);
			transform: translateX(2px);
		}

		/* Dark Mode Toggle Icon - Animated */
		#darkModeIcon {
			font-size: 18px;
			transition: all 0.3s ease;
			color: #64748b;
		}

		body.dark-mode #darkModeIcon {
			transform: rotate(180deg);
			color: #fbbf24;
			text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
		}

		body.dark-mode .dark-mode-toggle .nav-link:hover #darkModeIcon {
			color: #fcd34d;
			text-shadow: 0 0 15px rgba(251, 191, 36, 0.7);
			transform: rotate(180deg) scale(1.1);
		}

		/* Footer */
		body.dark-mode footer {
			background: rgba(15, 23, 42, 0.8);
			color: #94a3b8;
			border-top: 1px solid rgba(148, 163, 184, 0.1);
		}

		/* Smooth transitions for all elements */
		body.dark-mode * {
			transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
		}

		body.dark-mode *:not(.no-transition) {
			transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
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

                <li class="nav-item dark-mode-toggle me-2">
                    <a href="javascript:void(0);" class="nav-link header-nav-list" id="darkModeToggle" title="Toggle Dark Mode">
                        <i class="fas fa-moon" id="darkModeIcon"></i>
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

        // Dark Mode Toggle Functionality
        $(document).ready(function() {
            // Check for saved theme preference (already applied in head, but sync icon)
            var darkMode = localStorage.getItem('darkMode') === 'true';
            
            // Update icon based on current state
            if (darkMode) {
                $('#darkModeIcon').removeClass('fa-moon').addClass('fa-sun');
            } else {
                $('#darkModeIcon').removeClass('fa-sun').addClass('fa-moon');
            }

            // Toggle dark mode
            $('#darkModeToggle').on('click', function(e) {
                e.preventDefault();
                
                var html = document.documentElement;
                var isDarkMode = $('body').hasClass('dark-mode') || html.classList.contains('dark-mode');
                
                if (isDarkMode) {
                    // Switching to light mode
                    $('body').removeClass('dark-mode');
                    html.classList.remove('dark-mode');
                    html.setAttribute('data-dark-mode', 'false');
                    localStorage.setItem('darkMode', 'false');
                    $('#darkModeIcon').removeClass('fa-sun').addClass('fa-moon');
                } else {
                    // Switching to dark mode
                    $('body').addClass('dark-mode');
                    html.classList.add('dark-mode');
                    html.setAttribute('data-dark-mode', 'true');
                    localStorage.setItem('darkMode', 'true');
                    $('#darkModeIcon').removeClass('fa-moon').addClass('fa-sun');
                }
            });
        });
    </script>
</body>
</html>