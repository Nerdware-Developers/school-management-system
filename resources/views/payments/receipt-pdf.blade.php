<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $transaction->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .receipt-info {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PAYMENT RECEIPT</h1>
        <p>Receipt Number: <strong>{{ $transaction->receipt_number }}</strong></p>
        <p>Date: {{ $transaction->paid_at->format('M d, Y h:i A') }}</p>
    </div>

    <div class="receipt-info">
        <h3>Student Information</h3>
        <div class="info-row">
            <span><strong>Name:</strong> {{ $transaction->student->first_name }} {{ $transaction->student->last_name }}</span>
        </div>
        <div class="info-row">
            <span><strong>Admission Number:</strong> {{ $transaction->student->admission_number }}</span>
        </div>
        <div class="info-row">
            <span><strong>Class:</strong> {{ $transaction->student->class }}</span>
        </div>
    </div>

    <div class="receipt-info">
        <h3>Payment Details</h3>
        <div class="info-row">
            <span><strong>Transaction ID:</strong> {{ $transaction->transaction_id }}</span>
        </div>
        @if($transaction->payment_gateway === 'mpesa' && $transaction->gateway_transaction_id)
        <div class="info-row">
            <span><strong>M-Pesa Receipt:</strong> {{ $transaction->gateway_transaction_id }}</span>
        </div>
        @endif
        <div class="info-row">
            <span><strong>Payment Method:</strong> 
                @if($transaction->payment_gateway === 'mpesa')
                    M-Pesa
                @else
                    {{ ucfirst($transaction->payment_method ?? 'Card') }}
                @endif
            </span>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount (KES)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $transaction->description }}</td>
                <td class="text-right">{{ number_format($transaction->amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Paid</strong></td>
                <td class="text-right"><strong>{{ number_format($transaction->amount, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This is an official receipt. Please keep it for your records.</p>
        <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</body>
</html>

