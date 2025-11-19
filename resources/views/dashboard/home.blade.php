@extends('layouts.master')

@section('content')
<div class="page-wrapper" style="background-color: #f9f9ff; min-height: 100vh;">
    <div class="container py-4">
        <h4 class="fw-bold mb-4">Admin Dashboard</h4>

        <!-- Top Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#f3e8ff; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Students</h6>
                            <h3 class="fw-bold">{{ $totalStudents }}</h3>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#a855f7;">
                            <i class="bi bi-mortarboard text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#e0f2fe; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Teachers</h6>
                            <h3 class="fw-bold">{{ $totalTeachers }}</h3>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#3b82f6;">
                            <i class="bi bi-person text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#ffedd5; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Departments</h6>
                            <h3 class="fw-bold">{{ $totalDepartments }}</h3>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#fb923c;">
                            <i class="bi bi-people text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0" style="background-color:#dcfce7; border-radius:15px;">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Earnings</h6>
                            <h4 class="fw-bold">Ksh{{ number_format($totalEarnings, 2) }}</h4>
                        </div>
                        <div class="p-3 rounded-circle" style="background-color:#22c55e;">
                            <i class="bi bi-cash-coin text-white fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Stats -->
        <div class="row g-3 mb-4">
            <!-- Exam Results Graph -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold">All Exam Results</h6>
                            <select class="form-select form-select-sm" style="width:auto;">
                                <option>Monthly</option>
                                <option>Yearly</option>
                            </select>
                        </div>
                        <canvas id="examResultsChart" height="120"></canvas>
                    </div>
                </div>
            </div>

            <!-- Students Gender Chart -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body text-center position-relative">
                        <h6 class="fw-bold mb-3 text-start">Students</h6>

                        <!-- Two-layer doughnut chart -->
                        <div class="position-relative d-flex justify-content-center align-items-center" style="height:220px;">
                            <canvas id="studentsChart" width="220" height="220"></canvas>
                            <div class="position-absolute">
                                <h6 class="fw-bold text-secondary mb-0">Total</h6>
                                <h4 class="fw-bold mb-0">{{ $totalStudents }}</h4>
                            </div>
                        </div>

                        <!-- Gender legend -->
                        <div class="d-flex justify-content-center gap-4 mt-3">
                            <div class="d-flex align-items-center gap-1">
                                <span class="rounded-circle" style="width:10px; height:10px; background-color:#a855f7;"></span>
                                <small>Male</small>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="rounded-circle" style="width:10px; height:10px; background-color:#f59e0b;"></span>
                                <small>Female</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Star Students + Notifications -->
        <div class="row g-3">
            <!-- Star Students Table -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Star Students</h6>
                        <table class="table table-borderless align-middle">
                            <thead>
                                <tr class="text-muted">
                                    <th>Name</th>
                                    <th>ID</th>
                                    <th>Marks</th>
                                    <th>Percent</th>
                                    <th>Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><img src="https://i.pravatar.cc/40?img=1" class="rounded-circle me-2" width="35"> Evelyn Harper</td>
                                    <td>PRE43178</td>
                                    <td>1185</td>
                                    <td>98%</td>
                                    <td>2014</td>
                                </tr>
                                <tr>
                                    <td><img src="https://i.pravatar.cc/40?img=2" class="rounded-circle me-2" width="35"> Diana Plenty</td>
                                    <td>PRE43174</td>
                                    <td>1165</td>
                                    <td>91%</td>
                                    <td>2014</td>
                                </tr>
                                <tr>
                                    <td><img src="https://i.pravatar.cc/40?img=3" class="rounded-circle me-2" width="35"> John Millar</td>
                                    <td>PRE43187</td>
                                    <td>1175</td>
                                    <td>92%</td>
                                    <td>2014</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0" style="border-radius:15px;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">All Exam Results</h6>
                        <div class="d-flex mb-3 align-items-center">
                            <div class="p-2 bg-primary bg-opacity-25 rounded-circle me-3">
                                <i class="bi bi-person-plus text-primary fs-5"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">New Teacher</p>
                                <small class="text-muted">Just now</small>
                            </div>
                        </div>
                        <div class="d-flex mb-3 align-items-center">
                            <div class="p-2 bg-warning bg-opacity-25 rounded-circle me-3">
                                <i class="bi bi-receipt text-warning fs-5"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">Fees Structure</p>
                                <small class="text-muted">Today</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="p-2 bg-success bg-opacity-25 rounded-circle me-3">
                                <i class="bi bi-book text-success fs-5"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">New Course</p>
                                <small class="text-muted">24 Sep 2023</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const examResultsCtx = document.getElementById('examResultsChart').getContext('2d');
new Chart(examResultsCtx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [
            {
                label: 'Teachers',
                data: [50000, 40000, 60000, 55000, 70000, 75000, 50000],
                borderColor: '#a855f7',
                fill: false,
                tension: 0.4
            },
            {
                label: 'Students',
                data: [40000, 55000, 45000, 60000, 65000, 70000, 45000],
                borderColor: '#3b82f6',
                fill: false,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        }
    }
});

const ctx = document.getElementById('studentsChart').getContext('2d');

new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Male', 'Female'],
        datasets: [
            {
                label: 'Male',
                data: [9000, 10000], // outer ring
                backgroundColor: ['#a855f7', '#e9d5ff'], // purple + light background
                cutout: '70%',
                radius: '100%',
                borderWidth: 0,
            },
            {
                label: 'Female',
                data: [8000, 10000], // inner ring
                backgroundColor: ['#f59e0b', '#fef3c7'], // orange + light background
                cutout: '50%',
                radius: '100%',
                borderWidth: 0,
            }
        ]
    },
    options: {
        plugins: {
            legend: { display: false },
            tooltip: { enabled: false }
        },
        rotation: -Math.PI / 2,
        circumference: 360,
    }
});
</script>

<!-- Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endsection
