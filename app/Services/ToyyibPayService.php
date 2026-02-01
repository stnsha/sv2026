<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibPayService
{
    private string $baseUrl;
    private string $secretKey;
    private string $categoryCode;
    private string $environment;

    public function __construct()
    {
        $this->environment = config('toyyibpay.environment');
        $env = config("toyyibpay.environments.{$this->environment}");

        $this->baseUrl = $env['base_url'];
        $this->secretKey = $env['secret_key'] ?? '';
        $this->categoryCode = $env['category_code'] ?? '';
    }

    // -------------------------------------------------------------------------
    // API Methods (ordered per toyyibPay API reference)
    // -------------------------------------------------------------------------

    public function createCategory(string $name, string $description): array
    {
        return $this->post('/index.php/api/createCategory', [
            'catname' => $name,
            'catdescription' => $description,
            'userSecretKey' => $this->secretKey,
        ]);
    }

    public function createBill(array $billData): array
    {
        $billData['userSecretKey'] = $this->secretKey;
        $billData['categoryCode'] = $billData['categoryCode'] ?? $this->categoryCode;

        return $this->post('/index.php/api/createBill', $billData);
    }

    public function getBillTransactions(string $billCode, ?int $paymentStatus = null): array
    {
        $data = [
            'billCode' => $billCode,
        ];

        if ($paymentStatus !== null) {
            $data['billpaymentStatus'] = $paymentStatus;
        }

        return $this->post('/index.php/api/getBillTransactions', $data);
    }

    public function getCategoryDetails(?string $categoryCode = null): array
    {
        return $this->post('/index.php/api/getCategoryDetails', [
            'categoryCode' => $categoryCode ?? $this->categoryCode,
            'userSecretKey' => $this->secretKey,
        ]);
    }

    public function inactiveBill(string $billCode): array
    {
        return $this->post('/index.php/api/inactiveBill', [
            'billCode' => $billCode,
            'userSecretKey' => $this->secretKey,
        ]);
    }

    public function getBank(): array
    {
        return $this->get('/index.php/api/getBank');
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    public function getPaymentUrl(string $billCode): string
    {
        return $this->baseUrl . '/' . $billCode;
    }

    public function parseCallback(array $data): array
    {
        $status = $data['status'] ?? null;

        return [
            'bill_code' => $data['billcode'] ?? null,
            'transaction_id' => $data['refno'] ?? null,
            'status' => $status,
            'reason' => $data['reason'] ?? null,
            'amount' => $data['amount'] ?? null,
            'transaction_time' => $data['transaction_time'] ?? null,
            'is_paid' => $status === '1',
            'is_pending' => $status === '2',
            'is_failed' => $status === '3',
        ];
    }

    public function parseRedirect(array $data): array
    {
        $statusId = $data['status_id'] ?? null;

        return [
            'bill_code' => $data['billcode'] ?? null,
            'status_id' => $statusId,
            'reason' => $data['msg'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'is_paid' => $statusId === '1',
            'is_pending' => $statusId === '2',
            'is_failed' => $statusId === '3',
        ];
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isSandbox(): bool
    {
        return $this->environment !== 'production';
    }

    // -------------------------------------------------------------------------
    // Private HTTP Helpers
    // -------------------------------------------------------------------------

    private function post(string $endpoint, array $data): array
    {
        try {
            $response = Http::asForm()->post($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('ToyyibPay API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment gateway request failed',
            ];
        } catch (Exception $e) {
            Log::error('ToyyibPay API exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment gateway connection failed',
            ];
        }
    }

    private function get(string $endpoint): array
    {
        try {
            $response = Http::get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('ToyyibPay API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment gateway request failed',
            ];
        } catch (Exception $e) {
            Log::error('ToyyibPay API exception', [
                'endpoint' => $endpoint,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment gateway connection failed',
            ];
        }
    }
}
