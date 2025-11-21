@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Student Bus Assignments</h4>
                <h6>Manage student bus assignments</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('transport.assignments.create') }}" class="btn btn-primary">Assign Student</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Bus</th>
                                <th>Route</th>
                                <th>Stop</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->student->first_name }} {{ $assignment->student->last_name }}</td>
                                    <td>{{ $assignment->bus->bus_number }}</td>
                                    <td>{{ $assignment->route->route_name }}</td>
                                    <td>{{ $assignment->stop ? $assignment->stop->stop_name : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status == 'active' ? 'success' : ($assignment->status == 'suspended' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('transport.assignments.destroy', $assignment->id) }}" method="POST" style="display: inline;">
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
                    {{ $assignments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

