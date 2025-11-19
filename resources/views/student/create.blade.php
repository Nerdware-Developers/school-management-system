@extends('layouts.master')

@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="page-wrapper">
    <div class="container mt-4">
        <h2 class="mb-4">Student Registration Form</h2>
        <form action="{{ route('student/add/save') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="studentFormTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="legal-tab" data-bs-toggle="tab" href="#legal" role="tab">Legal Section</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="activities-tab" data-bs-toggle="tab" href="#activities" role="tab">Co-Activities</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="medical-tab" data-bs-toggle="tab" href="#medical" role="tab">Medical Section</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="finance-tab" data-bs-toggle="tab" href="#finance" role="tab">Financial Information</a>
                </li>

            </ul>

            <div class="tab-content mt-3" id="studentFormTabsContent">
                <!-- Legal Section -->
                <div class="tab-pane fade show active" id="legal" role="tabpanel">
                    @include('student.partials.legal')
                </div>

                <!-- Co-Activities Section -->
                <div class="tab-pane fade" id="activities" role="tabpanel">
                    @include('student.partials.activities')
                </div>

                <!-- Medical Section -->
                <div class="tab-pane fade" id="medical" role="tabpanel">
                    @include('student.partials.medical')
                </div>
                <div class="tab-pane fade" id="finance" role="tabpanel">
                    @include('student.partials.finance')
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
