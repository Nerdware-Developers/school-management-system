@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Routes</h4>
                <h6>Manage bus routes</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('transport.routes.create') }}" class="btn btn-primary">Add Route</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Route Name</th>
                                <th>Fare</th>
                                <th>Stops</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                                <tr>
                                    <td>{{ $route->route_name }}</td>
                                    <td>${{ number_format($route->fare, 2) }}</td>
                                    <td>{{ $route->stops->count() }}</td>
                                    <td>{{ $route->student_assignments_count }}</td>
                                    <td>
                                        @if($route->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('transport.routes.edit', $route->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('transport.routes.destroy', $route->id) }}" method="POST" style="display: inline;">
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
                    {{ $routes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

