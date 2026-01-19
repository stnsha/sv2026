<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibPayService
{
    private string $baseUrl;
    private string $secretKey;
    private string $categoryCode;

    public function __construct()
    {
        $this->baseUrl = config('services.toyyibpay.url');
        $this->secretKey = config('services.toyyibpay.secret_key');
        $this->categoryCode = config('services.toyyibpay.category_code');
    }

    public function createBill(Booking $booking): array
    {
        $customer = $booking->customer;

        $billData = [
            'userSecretKey' => $this->secretKey,
            'categoryCode' => $this->categoryCode,
            'billName' => 'Table Booking #' . $booking->id,
            'billDescription' => 'Restaurant table booking for ' . $booking->date->formatted_date,
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => (int) ($booking->total * 100),
            'billReturnUrl' => route('payment.redirect'),
            'billCallbackUrl' => route('payment.callback'),
            'billExternalReferenceNo' => 'BK' . str_pad($booking->id, 8, '0', STR_PAD_LEFT),
            'billTo' => $customer->name,
            'billEmail' => $customer->email,
            'billPhone' => $customer->phone_number,
            'billSplitPayment' => 0,
            'billSplitPaymentArgs' => '',
            'billPaymentChannel' => 0,
            'billContentEmail' => 'Thank you for your booking. Your payment has been received.',
            'billChargeToCustomer' => 1,
        ];

        try {
            $response = Http::asForm()->post($this->baseUrl . '/index.php/api/createBill', $billData);

            if ($response->successful()) {
                $result = $response->json();

                if (is_array($result) && isset($result[0]['BillCode'])) {
                    return [
                        'success' => true,
                        'bill_code' => $result[0]['BillCode'],
                    ];
                }

                Log::error('ToyyibPay createBill unexpected response', ['response' => $result]);

                return [
                    'success' => false,
                    'error' => 'Unexpected response from payment gateway',
                ];
            }

            Log::error('ToyyibPay createBill failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment gateway request failed',
            ];
        } catch (\Exception $e) {
            Log::error('ToyyibPay createBill exception', ['message' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => 'Payment gateway connection failed',
            ];
        }
    }

    public function getPaymentUrl(string $billCode): string
    {
        return $this->baseUrl . '/' . $billCode;
    }

    public function handleCallback(array $callbackData): array
    {
        $billCode = $callbackData['billcode'] ?? null;
        $transactionId = $callbackData['refno'] ?? null;
        $status = $callbackData['status'] ?? null;
        $reason = $callbackData['reason'] ?? null;

        if (empty($billCode)) {
            return [
                'success' => false,
                'error' => 'Missing bill code',
            ];
        }

        $booking = Booking::where('bill_code', $billCode)->first();

        if (!$booking) {
            return [
                'success' => false,
                'error' => 'Booking not found',
            ];
        }

        return [
            'success' => true,
            'booking' => $booking,
            'transaction_id' => $transactionId,
            'status' => $status,
            'reason' => $reason,
            'is_paid' => $status === '1',
        ];
    }

    public function validateSignature(array $data): bool
    {
        return true;
    }
}
