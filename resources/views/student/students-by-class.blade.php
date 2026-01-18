@extends('layouts.master')
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-sub-header">
                            <h3 class="page-title">Students by Class</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('student/list') }}">Student</a></li>
                                <li class="breadcrumb-item active">Students by Class</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-auto">
                        <a href="#" id="downloadByClassBtn" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Filtered List
                        </a>
                    </div>
                </div>
            </div>
            {{-- message --}}
            {!! Toastr::message() !!}
            
            <div class="student-group-form mb-4">
                <form id="searchForm" method="GET" action="{{ route('student/list-by-class') }}">
                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label>Filter by Class</label>
                                <select name="class" class="form-control">
                                    <option value="">All Classes</option>
                                    @foreach($allClasses as $class)
                                        <option value="{{ $class }}" {{ request('class') == $class ? 'selected' : '' }}>
                                            {{ $class }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label>Search by Name</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="Search by name..." value="{{ request('name') }}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label>Balance Filter</label>
                                <select name="balance_operator" class="form-control">
                                    <option value="">Operator</option>
                                    <option value="greater" {{ request('balance_operator') == 'greater' ? 'selected' : '' }}>> Greater Than</option>
                                    <option value="greater_equal" {{ request('balance_operator') == 'greater_equal' ? 'selected' : '' }}>>= Greater or Equal</option>
                                    <option value="less" {{ request('balance_operator') == 'less' ? 'selected' : '' }}>< Less Than</option>
                                    <option value="less_equal" {{ request('balance_operator') == 'less_equal' ? 'selected' : '' }}><= Less or Equal</option>
                                    <option value="equal" {{ request('balance_operator') == 'equal' ? 'selected' : '' }}>= Equal To</option>
                                    <option value="not_zero" {{ request('balance_operator') == 'not_zero' ? 'selected' : '' }}>≠ Not Zero</option>
                                    <option value="zero" {{ request('balance_operator') == 'zero' ? 'selected' : '' }}>= Zero</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label>Balance Amount</label>
                                <input type="number" name="balance_amount" class="form-control"
                                    placeholder="Amount..." value="{{ request('balance_amount') }}" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </div>
                        <div class="col-lg-1 col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <a href="{{ route('student/list-by-class') }}" class="btn btn-secondary w-100">Clear</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if(request('class') || request('name') || request('balance_operator'))
                <div class="alert alert-info">
                    <strong>Filtered Results:</strong>
                    @if(request('class'))
                        Class: <strong>{{ request('class') }}</strong>
                    @endif
                    @if(request('name'))
                        @if(request('class')) | @endif
                        Name: <strong>{{ request('name') }}</strong>
                    @endif
                    @if(request('balance_operator') && request('balance_amount'))
                        @if(request('class') || request('name')) | @endif
                        Balance: <strong>
                            @php
                                $operator = request('balance_operator');
                                $amount = request('balance_amount');
                                $operatorText = [
                                    'greater' => '>',
                                    'greater_equal' => '>=',
                                    'less' => '<',
                                    'less_equal' => '<=',
                                    'equal' => '=',
                                    'not_zero' => '≠ 0',
                                    'zero' => '= 0'
                                ];
                                $displayText = isset($operatorText[$operator]) ? $operatorText[$operator] : $operator;
                            @endphp
                            {{ $displayText }}@if(!in_array($operator, ['not_zero', 'zero'])) Ksh {{ number_format($amount, 2) }}@endif
                        </strong>
                    @endif
                </div>
            @endif

            @if($studentsByClass->isEmpty())
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> No students found matching your criteria.
                </div>
            @else
                @foreach($studentsByClass as $class => $students)
                    <div class="card card-table comman-shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-users me-2"></i>{{ $class }}
                                        <span class="badge bg-light text-dark ms-2">{{ $students->count() }} {{ $students->count() == 1 ? 'Student' : 'Students' }}</span>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Admission Number</th>
                                            <th>Name</th>
                                            <th>Section</th>
                                            <th>Gender</th>
                                            <th>Parent Name</th>
                                            <th>Parent Number</th>
                                            <th>Fee Balance</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $student)
                                        <tr>
                                            <td>{{ $student->admission_number }}</td>
                                            <td>
                                                <h2 class="table-avatar">
                                                    <a href="{{ url('student/profile/' . $student->id) }}" class="avatar avatar-sm me-2">
                                                        <img src="{{ $student->image ? route('student.photo', $student->image) : asset('images/photo_defaults.jpg') }}"
                                                            alt="Student Image" class="avatar-img rounded-circle">
                                                    </a>
                                                    <a href="{{ url('student/profile/' . $student->id) }}">
                                                        {{ $student->first_name }} {{ $student->last_name }}
                                                    </a>
                                                </h2>
                                            </td>
                                            <td>{{ $student->section ?? '-' }}</td>
                                            <td>{{ $student->gender ?? '-' }}</td>
                                            <td>{{ $student->parent_name ?? '-' }}</td>
                                            <td>{{ $student->parent_number ?? '-' }}</td>
                                            <td>
                                                <span class="badge {{ $student->balance > 0 ? 'bg-danger' : 'bg-success' }}">
                                                    Ksh {{ number_format($student->balance ?? 0, 2) }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="actions">
                                                    <a href="{{ url('student/edit/'.$student->id) }}" class="btn btn-sm bg-danger-light" title="Edit">
                                                        <i class="far fa-edit"></i>
                                                    </a>
                                                    <a href="{{ url('student/profile/'.$student->id) }}" class="btn btn-sm bg-info-light" title="View Profile">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Auto-submit on class select change
    $('select[name="class"]').on('change', function() {
        $('#searchForm').submit();
    });

    // Download button - preserve current filters
    $('#downloadByClassBtn').on('click', function(e) {
        e.preventDefault();
        
        // Get current filter values from form
        var classFilter = $('select[name="class"]').val() || '';
        var nameFilter = $('input[name="name"]').val() || '';
        var balanceOperator = $('select[name="balance_operator"]').val() || '';
        var balanceAmount = $('input[name="balance_amount"]').val() || '';
        
        // Build query string
        var params = {};
        if (classFilter) {
            params.class = classFilter;
        }
        if (nameFilter) {
            params.name = nameFilter;
        }
        if (balanceOperator) {
            params.balance_operator = balanceOperator;
        }
        if (balanceAmount) {
            params.balance_amount = balanceAmount;
        }
        
        // Build URL with query parameters
        var url = '{{ route("student/export-by-class") }}';
        if (Object.keys(params).length > 0) {
            url += '?' + $.param(params);
        }
        
        // Navigate to export URL
        window.location.href = url;
    });
});
</script>
@endsection

