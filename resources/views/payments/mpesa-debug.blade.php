@extends('layouts.master')

@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h4>M-Pesa Payment Debug</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Common Issues and Solutions</h5>
                        
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> If STK Push is not appearing on your phone:</h6>
                            <ol>
                                <li><strong>Check your phone number format:</strong> Use 0712345678 or 254712345678</li>
                                <li><strong>Verify Daraja credentials:</strong> Check your .env file has correct Consumer Key, Secret, Shortcode, and Passkey</li>
                                <li><strong>Check callback URL:</strong> Must be publicly accessible (use ngrok for local testing)</li>
                                <li><strong>Minimum amount:</strong> M-Pesa requires minimum Ksh 1.00</li>
                                <li><strong>Sandbox testing:</strong> Use test credentials from Safaricom Developer Portal</li>
                                <li><strong>Check logs:</strong> Review Laravel logs in storage/logs/laravel.log</li>
                            </ol>
                        </div>

                        <h6>Check Laravel Logs:</h6>
                        <pre class="bg-light p-3">tail -f storage/logs/laravel.log</pre>

                        <h6 class="mt-4">Test Your Configuration:</h6>
                        <ul>
                            <li>Consumer Key: {{ config('services.daraja.consumer_key') ? '✓ Set' : '✗ Missing' }}</li>
                            <li>Consumer Secret: {{ config('services.daraja.consumer_secret') ? '✓ Set' : '✗ Missing' }}</li>
                            <li>Shortcode: {{ config('services.daraja.shortcode') ? '✓ Set (' . config('services.daraja.shortcode') . ')' : '✗ Missing' }}</li>
                            <li>Passkey: {{ config('services.daraja.passkey') ? '✓ Set' : '✗ Missing' }}</li>
                            <li>Base URL: {{ config('services.daraja.base_url', 'Not set') }}</li>
                            <li>Callback URL: {{ config('services.daraja.callback_url', 'Not set') }}</li>
                        </ul>

                        <div class="mt-4">
                            <a href="{{ route('payments.create', request('student_id')) }}" class="btn btn-primary">Try Again</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

