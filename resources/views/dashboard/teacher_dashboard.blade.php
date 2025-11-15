@extends('layouts.master')
@section('content')

<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Title -->
        <div class="page-header mb-4">
            <h3 class="page-title fw-bold">Teachers</h3>
            <div class="search-bar position-relative w-50">
                <input type="text" class="form-control rounded-pill ps-4" placeholder="Search for students/teachers/documents...">
                <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y pe-3 text-muted"></i>
            </div>
        </div>

        <div class="row">

            <!-- Left Panel: Student List -->
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Teachers</h5>
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" placeholder="Search name or ID">
                        </div>
                        <ul class="list-group list-group-flush student-list">
                            @foreach ([
                                ['name'=>'Amara Olson','class'=>'Class VI','id'=>'E-8547','year'=>'2019'],
                                ['name'=>'Julie Von','class'=>'Class V','id'=>'D-4512','year'=>'2020'],
                                ['name'=>'Jocelyn Walker','class'=>'Class VI','id'=>'C-9514','year'=>'2016'],
                                ['name'=>'Trisha Berge','class'=>'Class VI','id'=>'F-6522','year'=>'2018'],
                                ['name'=>'Morris Mayert','class'=>'Class VI','id'=>'H-2787','year'=>'2016'],
                            ] as $s)
                            <li class="list-group-item d-flex align-items-center border-0 rounded-3 mb-2 {{ $s['name']=='Trisha Berge' ? 'bg-primary text-white' : 'bg-light' }}">
                                <img src="https://randomuser.me/api/portraits/women/{{ rand(1,80) }}.jpg" class="rounded-circle me-3" width="40" height="40" alt="student">
                                <div>
                                    <div class="fw-semibold">{{ $s['name'] }}</div>
                                    <small>{{ $s['class'] }}</small>
                                </div>
                                <div class="ms-auto text-end small">
                                    <div class="fw-bold">{{ $s['id'] }}</div>
                                    <span>{{ $s['year'] }}</span>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Profile & Chart -->
            <div class="col-md-8 col-lg-9">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body bg-primary text-white rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="rounded-circle me-3" width="80" height="80" alt="student">
                            <div>
                                <h4 class="mb-0">Trisha Berge</h4>
                                <div>Class VI | Student ID: F-6522</div>
                            </div>
                        </div>
                        <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center" style="width:70px;height:70px;">
                            <i class="fas fa-graduation-cap fs-3"></i>
                        </div>
                    </div>
                </div>

                <!-- Student Basic Details -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Basic Details</h5>
                        <div class="row g-3">
                            <div class="col-sm-6 col-lg-3">
                                <small class="text-muted">Gender</small>
                                <div class="fw-semibold">Female</div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <small class="text-muted">Date of Birth</small>
                                <div class="fw-semibold">29-04-2004</div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <small class="text-muted">Religion</small>
                                <div class="fw-semibold">Christian</div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <small class="text-muted">Blood Group</small>
                                <div class="fw-semibold">B+</div>
                            </div>
                            <div class="col-lg-6">
                                <small class="text-muted">Address</small>
                                <div class="fw-semibold">1962 Harrison Street, San Francisco, CA 94103</div>
                            </div>
                            <div class="col-lg-3">
                                <small class="text-muted">Father</small>
                                <div class="fw-semibold">Richard Berge<br><small class="text-muted">+1 603-965-4668</small></div>
                            </div>
                            <div class="col-lg-3">
                                <small class="text-muted">Mother</small>
                                <div class="fw-semibold">Maren Berge<br><small class="text-muted">+1 660-657-7027</small></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item"><a href="#" class="nav-link active">Progress</a></li>
                            <li class="nav-item"><a href="#" class="nav-link text-muted">Attendance</a></li>
                            <li class="nav-item"><a href="#" class="nav-link text-muted">Fees History</a></li>
                            <li class="nav-item"><a href="#" class="nav-link text-muted">School Bus</a></li>
                        </ul>

                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-sm btn-outline-primary me-2">All</button>
                            <button class="btn btn-sm btn-outline-primary">Maths</button>
                            <button class="btn btn-sm btn-outline-secondary">Science</button>
                            <button class="btn btn-sm btn-outline-secondary">English</button>
                            <button class="btn btn-sm btn-outline-secondary">History</button>
                        </div>

                        <div>
                            <canvas id="studentProgressChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('studentProgressChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Test 1', 'Test 2', 'Test 3', 'Test 4', 'Test 5', 'Test 6'],
        datasets: [{
            label: 'Progress',
            data: [55, 79, 63, 72, 65, 80],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#007bff'
        }]
    },
    options: {
        plugins: { legend: { display: false }},
        scales: {
            y: { beginAtZero: true, max: 100, ticks: { stepSize: 25 }}
        }
    }
});
</script>
@endsection
