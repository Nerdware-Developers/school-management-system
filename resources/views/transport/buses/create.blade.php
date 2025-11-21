@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Add Bus</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('transport.buses') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('transport.buses.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bus Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="bus_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bus Name</label>
                                <input type="text" class="form-control" name="bus_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Driver Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="driver_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Driver Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="driver_phone" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Driver License</label>
                                <input type="text" class="form-control" name="driver_license">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vehicle Type <span class="text-danger">*</span></label>
                                <select class="form-control" name="vehicle_type" required>
                                    <option value="bus">Bus</option>
                                    <option value="van">Van</option>
                                    <option value="minibus">Minibus</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Registration Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="registration_number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Capacity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="capacity" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Conductor Name</label>
                                <input type="text" class="form-control" name="conductor_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Conductor Phone</label>
                                <input type="text" class="form-control" name="conductor_phone">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Notes</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Save Bus</button>
                            <a href="{{ route('transport.buses') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

