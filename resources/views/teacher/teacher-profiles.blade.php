@extends('layouts.master')

@section('content')
<div class="page-wrapper" style="background-color:#f9f9ff; min-height:100vh;">
    <div class="content container-fluid py-4">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Teacher Profiles</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('teacher/list/page') }}">Teachers</a></li>
                        <li class="breadcrumb-item active">Profiles</li>
                    </ul>
                </div>
                <div class="col-auto">
                    <a href="{{ route('teacher/add/page') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Teacher
                    </a>
                </div>
            </div>
        </div>

        @if($teachers->isEmpty())
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted">
                    No teachers found.
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($teachers as $teacher)
                    <div class="col-xl-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="avatar avatar-lg bg-primary text-white fw-bold rounded-circle d-flex align-items-center justify-content-center">
                                        {{ strtoupper(\Illuminate\Support\Str::substr($teacher->full_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">{{ $teacher->full_name }}</h5>
                                        <p class="text-muted mb-0">{{ $teacher->phone_number ?? 'No phone' }}</p>
                                    </div>
                                </div>
                                <ul class="list-unstyled small mb-3 flex-grow-1">
                                    <li><strong>Classes:</strong> {{ $teacher->teaching_classes->implode(', ') ?: 'Not assigned' }}</li>
                                    <li><strong>Subjects:</strong> {{ $teacher->teaching_summary->implode(', ') ?: 'Not assigned' }}</li>
                                    @if($teacher->classTeacher)
                                        <li><strong>Class Teacher:</strong> {{ $teacher->classTeacher->class_name }}</li>
                                    @endif
                                </ul>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('teacher/profile', $teacher->id) }}" class="btn btn-outline-primary btn-sm">
                                        View Profile
                                    </a>
                                    <a href="{{ url('teacher/edit/'.$teacher->id) }}" class="btn btn-outline-secondary btn-sm">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-end mt-4">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

