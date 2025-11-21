@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>Payment Receipt</h4>
                <h6>Transaction completed successfully</h6>
            </div>
            <div class="page-btn">
                <a href="{{ route('payments.receipt.download', $transaction->id) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </a>
                <a href="{{ route('account/fees/collections/page') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="text-success"><i class="fas fa-check-circle"></i> Payment Successful</h3>
                            <p class="text-muted">Receipt Number: <strong>{{ $transaction->receipt_number }}</strong></p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Student Information</h6>
                                <p><strong>Name:</strong> {{ $transaction->student->first_name }} {{ $transaction->student->last_name }}</p>
                                <p><strong>Admission Number:</strong> {{ $transaction->student->admission_number }}</p>
                                <p><strong>Class:</strong> {{ $transaction->student->class }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Payment Details</h6>
                                <p><strong>Transaction ID:</strong> {{ $transaction->transaction_id }}</p>
                                @if($transaction->payment_gateway === 'mpesa' && $transaction->gateway_transaction_id)
                                    <p><strong>M-Pesa Receipt:</strong> {{ $transaction->gateway_transaction_id }}</p>
                                @endif
                                <p><strong>Payment Date:</strong> {{ $transaction->paid_at->format('M d, Y h:i A') }}</p>
                                <p><strong>Payment Method:</strong> 
                                    @if($transaction->payment_gateway === 'mpesa')
                                        <span class="badge bg-success">M-Pesa</span>
                                    @else
                                        <span class="badge bg-primary">{{ ucfirst($transaction->payment_method ?? 'Card') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $transaction->description }}</td>
                                        <td class="text-end"><strong>Ksh {{ number_format($transaction->amount, 2) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Paid</strong></td>
                                        <td class="text-end"><strong class="text-success">Ksh {{ number_format($transaction->amount, 2) }}</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-success mt-4">
                            <i class="fas fa-info-circle"></i> 
                            This is your official receipt. Please keep it for your records.
                        </div>

                        <div class="text-center mt-4">
                            <button onclick="window.print()" class="btn btn-outline-primary">
                                <i class="fas fa-print"></i> Print Receipt
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .page-header, .page-btn, .btn {
        display: none !important;
    }
}
</style>
@endsection

