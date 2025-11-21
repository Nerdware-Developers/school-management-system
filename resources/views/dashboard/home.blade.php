@extends('layouts.master')

@section('content')
<div class="page-wrapper" style="background-color: #f9f9ff; min-height: 100vh;">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Admin Dashboard</h4>
                <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}!</p>
            </div>
            <div>
                <div class="btn-group" role="group">
                    <a href="{{ route('student/add/page') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Student
                    </a>
                    <a href="{{ route('teacher/add/page') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-user-plus"></i> Add Teacher
                    </a>
                    <a href="{{ route('events.create') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-calendar-plus"></i> Add Event
                    </a>
                </div>
            </div>
        </div>

        <!-- Top Stats Row 1 -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#f3e8ff; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Students</h6>
                            <h3 class="fw-bold">{{ $totalStudents }}</h3>
                            <small class="text-muted">
                                <i class="fas fa-male"></i> {{ $boys }} Male | 
                                <i class="fas fa-female"></i> {{ $girls }} Female
                            </small>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#a855f7;">
                            <i class="fas fa-graduation-cap text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#e0f2fe; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Teachers</h6>
                            <h3 class="fw-bold">{{ $totalTeachers }}</h3>
                            <small class="text-muted">{{ $totalDepartments }} Departments</small>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#3b82f6;">
                            <i class="fas fa-chalkboard-teacher text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#dcfce7; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Earnings</h6>
                            <h4 class="fw-bold">Ksh {{ number_format($totalEarnings, 2) }}</h4>
                            <small class="text-success">
                                <i class="fas fa-check-circle"></i> {{ $paidStudents }} Paid
                            </small>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#22c55e;">
                            <i class="fas fa-money-bill-wave text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#fef3c7; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Pending Fees</h6>
                            <h4 class="fw-bold">Ksh {{ number_format($pendingFees, 2) }}</h4>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-triangle"></i> {{ $pendingFeeStudents }} Students
                            </small>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#f59e0b;">
                            <i class="fas fa-exclamation-circle text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Stats Row 2 -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#fce7f3; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Today's Attendance</h6>
                            <h3 class="fw-bold">{{ $attendanceRate }}%</h3>
                            <small class="text-muted">
                                {{ $todayPresent }} Present | {{ $todayAbsent }} Absent
                            </small>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#ec4899;">
                            <i class="fas fa-user-check text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#e0e7ff; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Active Exams</h6>
                            <h3 class="fw-bold">{{ $totalExams }}</h3>
                            <small class="text-muted">{{ $totalSubjects }} Subjects</small>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#6366f1;">
                            <i class="fas fa-file-alt text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#f0fdf4; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Upcoming Events</h6>
                            <h3 class="fw-bold">{{ $totalEvents }}</h3>
                            <small class="text-muted">{{ $upcomingEvents->count() }} This Week</small>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#10b981;">
                            <i class="fas fa-calendar-alt text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#fff7ed; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Transport</h6>
                            <h3 class="fw-bold">{{ $studentsWithTransport }}</h3>
                            <small class="text-muted">{{ $totalBuses }} Buses | {{ $activeRoutes }} Routes</small>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#f97316;">
                            <i class="fas fa-bus text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="row g-3 mb-4">
            <!-- Monthly Growth Chart -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold">Monthly Growth</h6>
                            <select class="form-select form-select-sm" style="width:auto;" id="chartPeriod">
                                <option value="students">Students</option>
                                <option value="teachers">Teachers</option>
                                <option value="fees">Fee Collection</option>
                            </select>
                        </div>
                        <div style="position: relative; height: 300px;">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Gender Chart -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body text-center position-relative">
                        <h6 class="fw-bold mb-3 text-start">Students by Gender</h6>
                        <div class="position-relative d-flex justify-content-center align-items-center" style="height:220px;">
                            <canvas id="studentsChart" width="220" height="220"></canvas>
                            <div class="position-absolute">
                                <h6 class="fw-bold text-secondary mb-0">Total</h6>
                                <h4 class="fw-bold mb-0">{{ $totalStudents }}</h4>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center gap-4 mt-3">
                            <div class="d-flex align-items-center gap-1">
                                <span class="rounded-circle" style="width:10px; height:10px; background-color:#a855f7;"></span>
                                <small>Male ({{ $boys }})</small>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="rounded-circle" style="width:10px; height:10px; background-color:#f59e0b;"></span>
                                <small>Female ({{ $girls }})</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Row -->
        <div class="row g-3">
            <!-- Recent Activity & Notifications -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold">Recent Activity</h6>
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link">View All</a>
                        </div>
                        <div class="activity-feed">
                            @forelse($recentNotifications as $notification)
                                <div class="d-flex mb-3 align-items-start">
                                    <div class="p-2 rounded-circle me-3" style="background-color: {{ $notification->type === 'success' ? '#dcfce7' : ($notification->type === 'warning' ? '#fef3c7' : ($notification->type === 'error' ? '#fee2e2' : '#e0f2fe')) }};">
                                        <i class="fas fa-{{ $notification->type === 'success' ? 'check-circle' : ($notification->type === 'warning' ? 'exclamation-triangle' : ($notification->type === 'error' ? 'times-circle' : 'info-circle')) }} text-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'warning' ? 'warning' : ($notification->type === 'error' ? 'danger' : 'info')) }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-semibold">{{ $notification->title }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">No recent activity</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold">Upcoming Events</h6>
                            <a href="{{ route('events.index') }}" class="btn btn-sm btn-link">View All</a>
                        </div>
                        <div class="events-list">
                            @forelse($upcomingEvents as $event)
                                <div class="d-flex mb-3 align-items-start">
                                    <div class="p-2 rounded me-3" style="background-color: {{ $event->color }}20; border-left: 3px solid {{ $event->color }};">
                                        <i class="fas fa-calendar text-white" style="color: {{ $event->color }} !important;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 fw-semibold">{{ $event->title }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $event->start_date->format('M d, Y') }}
                                            @if($event->location)
                                                <br><i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted text-center">No upcoming events</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Students -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold">Top Performing Students</h6>
                            <a href="{{ route('exams.page') }}" class="btn btn-sm btn-link">View All</a>
                        </div>
                        <div class="top-students-list">
                            @forelse($topStudents as $index => $result)
                                @if($result->student)
                                    <div class="d-flex mb-3 align-items-center">
                                        <div class="rank-badge me-3" style="width: 30px; height: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-0 fw-semibold">{{ $result->student->first_name }} {{ $result->student->last_name }}</p>
                                            <small class="text-muted">Avg: {{ number_format($result->avg_marks, 1) }}%</small>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="text-muted text-center">No exam results yet</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Students & Teachers -->
        <div class="row g-3 mt-3">
            <!-- Recent Students -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold">Recent Students</h6>
                            <a href="{{ route('student/list') }}" class="btn btn-sm btn-link">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <tbody>
                                    @forelse($recentStudents as $student)
                                        <tr>
                                            <td>
                                                <img src="{{ $student->image ? asset('images/' . $student->image) : asset('images/photo_defaults.jpg') }}" 
                                                     class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                                <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                            </td>
                                            <td><span class="badge bg-info">{{ $student->class }}</span></td>
                                            <td><small class="text-muted">{{ $student->created_at->diffForHumans() }}</small></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No recent students</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Fee Payments -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold">Recent Fee Payments</h6>
                            <a href="{{ route('account/fees/collections/page') }}" class="btn btn-sm btn-link">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <tbody>
                                    @forelse($recentPayments as $payment)
                                        <tr>
                                            <td>
                                                <strong>{{ $payment->student_name ?? ($payment->student ? $payment->student->first_name . ' ' . $payment->student->last_name : 'N/A') }}</strong>
                                                <br><small class="text-muted">{{ $payment->fees_type }}</small>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-success">Ksh {{ number_format($payment->fees_amount, 2) }}</strong>
                                                <br><small class="text-muted">{{ $payment->paid_date ? \Carbon\Carbon::parse($payment->paid_date)->format('M d, Y') : 'N/A' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">No recent payments</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
$(document).ready(function() {
    // Ensure Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        return;
    }

    // Monthly Growth Chart
    const monthlyCanvas = document.getElementById('monthlyChart');
    if (!monthlyCanvas) {
        console.error('Monthly chart canvas not found');
        return;
    }

    const monthlyCtx = monthlyCanvas.getContext('2d');
    let monthlyChart = null;

    try {
        monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json($monthLabels ?? []),
                datasets: [{
                    label: 'Students',
                    data: @json($studentData ?? []),
                    borderColor: '#a855f7',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    } catch (error) {
        console.error('Error creating monthly chart:', error);
    }

    // Update chart on period change
    const chartPeriodSelect = document.getElementById('chartPeriod');
    if (chartPeriodSelect && monthlyChart) {
        chartPeriodSelect.addEventListener('change', function() {
            const period = this.value;
            let data, label, color;
            
            if (period === 'students') {
                data = @json($studentData ?? []);
                label = 'Students';
                color = '#a855f7';
            } else if (period === 'teachers') {
                data = @json($teacherData ?? []);
                label = 'Teachers';
                color = '#3b82f6';
            } else {
                data = @json($feeData ?? []);
                label = 'Fee Collection (Ksh)';
                color = '#22c55e';
            }
            
            if (monthlyChart && data) {
                monthlyChart.data.datasets[0].data = data;
                monthlyChart.data.datasets[0].label = label;
                monthlyChart.data.datasets[0].borderColor = color;
                monthlyChart.data.datasets[0].backgroundColor = color + '1A';
                monthlyChart.update('active');
            }
        });
    }

    // Students Gender Chart
    const studentsCanvas = document.getElementById('studentsChart');
    if (studentsCanvas) {
        try {
            const ctx = studentsCanvas.getContext('2d');
            const boysCount = {{ $boys ?? 0 }};
            const girlsCount = {{ $girls ?? 0 }};
            
            if (boysCount > 0 || girlsCount > 0) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Male', 'Female'],
                        datasets: [{
                            data: [boysCount, girlsCount],
                            backgroundColor: ['#a855f7', '#f59e0b'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { 
                                display: false 
                            },
                            tooltip: { 
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        label += context.parsed + ' (' + percentage + '%)';
                                        return label;
                                    }
                                }
                            }
                        },
                        cutout: '70%',
                    }
                });
            }
        } catch (error) {
            console.error('Error creating students chart:', error);
        }
    }
});
</script>

<!-- Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection
