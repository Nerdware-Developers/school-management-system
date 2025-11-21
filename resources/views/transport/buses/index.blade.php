@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Buses</h4>
                <h6>Manage school buses</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('transport.buses.create') }}" class="btn btn-primary">Add Bus</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Bus Number</th>
                                <th>Driver</th>
                                <th>Capacity</th>
                                <th>Active Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($buses as $bus)
                                <tr>
                                    <td>{{ $bus->bus_number }}</td>
                                    <td>{{ $bus->driver_name }}<br><small>{{ $bus->driver_phone }}</small></td>
                                    <td>{{ $bus->capacity }}</td>
                                    <td>{{ $bus->active_assignments_count }}</td>
                                    <td>
                                        @if($bus->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('transport.buses.edit', $bus->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('transport.buses.destroy', $bus->id) }}" method="POST" style="display: inline;">
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
                    {{ $buses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

