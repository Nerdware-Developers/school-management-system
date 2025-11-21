<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'student_fee_term_id',
        'transaction_id',
        'payment_gateway',
        'gateway_transaction_id',
        'gateway_payment_intent_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'description',
        'gateway_response',
        'failure_reason',
        'paid_at',
        'refunded_at',
        'receipt_url',
        'receipt_number',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeTerm()
    {
        return $this->belongsTo(StudentFeeTerm::class, 'student_fee_term_id');
    }

    /**
     * Generate unique transaction ID
     */
    public static function generateTransactionId()
    {
        do {
            $transactionId = 'TXN' . date('Ymd') . strtoupper(substr(uniqid(), -8));
        } while (self::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    /**
     * Generate receipt number
     */
    public static function generateReceiptNumber()
    {
        do {
            $receiptNumber = 'RCP' . date('Ymd') . strtoupper(substr(uniqid(), -6));
        } while (self::where('receipt_number', $receiptNumber)->exists());

        return $receiptNumber;
    }
}
