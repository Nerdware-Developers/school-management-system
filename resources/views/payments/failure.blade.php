@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Payment Failed</h4>
            </div>
            <div class="page-btn">
                <a href="{{ route('payments.create', $transaction->student_id) }}" class="btn btn-primary">Try Again</a>
                <a href="{{ route('account/fees/collections/page') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-times-circle text-danger" style="font-size: 64px;"></i>
                        </div>
                        <h3 class="text-danger mb-3">Payment Failed</h3>
                        <p class="text-muted mb-3">
                            Your payment could not be processed. Please try again.
                        </p>
                        @if($transaction->failure_reason)
                            <div class="alert alert-warning">
                                <strong>Reason:</strong> {{ $transaction->failure_reason }}
                            </div>
                        @endif
                        <div class="mt-4">
                            <a href="{{ route('payments.create', $transaction->student_id) }}" class="btn btn-primary">
                                <i class="fas fa-redo"></i> Try Again
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

