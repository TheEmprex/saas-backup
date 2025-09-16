<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CryptoPaymentService
{
    protected $apiKey;

    protected $apiSecret;

    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.crypto.api_key');
        $this->apiSecret = config('services.crypto.api_secret');
        $this->baseUrl = config('services.crypto.base_url', 'https://api.coinbase.com/v2');
    }

    /**
     * Create a crypto payment request
     */
    public function createPaymentRequest(SubscriptionPlan $plan, User $user, string $cryptocurrency = 'BTC'): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'/charges', [
                'name' => 'Subscription: '.$plan->name,
                'description' => 'Monthly subscription for '.$plan->name,
                'local_price' => [
                    'amount' => $plan->price,
                    'currency' => 'USD',
                ],
                'pricing_type' => 'fixed_price',
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'subscription_type' => 'monthly',
                ],
                'redirect_url' => route('subscription.crypto.success'),
                'cancel_url' => route('subscription.plans'),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'payment_url' => $response->json()['data']['hosted_url'],
                    'payment_id' => $response->json()['data']['id'],
                    'addresses' => $response->json()['data']['addresses'],
                ];
            }

            Log::error('Crypto payment creation failed', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to create payment request',
            ];

        } catch (Exception $exception) {
            Log::error('Crypto payment service error', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Payment service temporarily unavailable',
            ];
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(string $paymentId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
            ])->get($this->baseUrl.'/charges/'.$paymentId);

            if ($response->successful()) {
                $data = $response->json()['data'];

                return [
                    'success' => true,
                    'status' => $data['timeline'][0]['status'] ?? 'pending',
                    'confirmed' => in_array($data['timeline'][0]['status'] ?? '', ['COMPLETED', 'RESOLVED']),
                    'amount' => $data['pricing']['local']['amount'],
                    'currency' => $data['pricing']['local']['currency'],
                    'metadata' => $data['metadata'] ?? [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Payment verification failed',
            ];

        } catch (Exception $exception) {
            Log::error('Payment verification error', [
                'payment_id' => $paymentId,
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Verification service unavailable',
            ];
        }
    }

    /**
     * Get supported cryptocurrencies
     */
    public function getSupportedCurrencies()
    {
        return [
            'BTC' => [
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'icon' => '₿',
                'network' => 'bitcoin',
            ],
            'ETH' => [
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'icon' => 'Ξ',
                'network' => 'ethereum',
            ],
            'USDT' => [
                'name' => 'Tether',
                'symbol' => 'USDT',
                'icon' => '₮',
                'network' => 'ethereum',
            ],
            'USDC' => [
                'name' => 'USD Coin',
                'symbol' => 'USDC',
                'icon' => '$',
                'network' => 'ethereum',
            ],
            'LTC' => [
                'name' => 'Litecoin',
                'symbol' => 'LTC',
                'icon' => 'Ł',
                'network' => 'litecoin',
            ],
            'BCH' => [
                'name' => 'Bitcoin Cash',
                'symbol' => 'BCH',
                'icon' => '₿',
                'network' => 'bitcoin-cash',
            ],
        ];
    }

    /**
     * Get current crypto exchange rates
     */
    public function getExchangeRates(): array
    {
        try {
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price', [
                'ids' => 'bitcoin,ethereum,tether,usd-coin,litecoin,bitcoin-cash',
                'vs_currencies' => 'usd',
                'include_24hr_change' => 'true',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'rates' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to fetch exchange rates',
            ];

        } catch (Exception $exception) {
            Log::error('Exchange rate fetch error', [
                'error' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Exchange rate service unavailable',
            ];
        }
    }

    /**
     * Generate payment address for specific cryptocurrency
     */
    public function generatePaymentAddress(string $cryptocurrency, SubscriptionPlan $plan, User $user)
    {
        // This would typically integrate with a crypto payment processor
        // For demo purposes, we'll generate a mock address
        $addresses = [
            'BTC' => '1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2',
            'ETH' => '0x742d35Cc6634C0532925a3b8D0b4eE7C1E8E7A1e',
            'USDT' => '0x742d35Cc6634C0532925a3b8D0b4eE7C1E8E7A1e',
            'USDC' => '0x742d35Cc6634C0532925a3b8D0b4eE7C1E8E7A1e',
            'LTC' => 'LbT8HLPqDqmKK3gzrckzYGN4cCNhDjrvRg',
            'BCH' => 'bitcoincash:qp2ruuv5zp8gzc5jdq8xfvzqxnmcg5vwqz6qwwqvwq',
        ];

        return [
            'address' => $addresses[$cryptocurrency] ?? null,
            'amount_usd' => $plan->price,
            'expires_at' => now()->addHours(2)->timestamp,
            'payment_id' => 'crypto_'.Str::random(20),
        ];
    }

    /**
     * Create QR code for payment
     */
    public function generatePaymentQR(string $address, float $amount, string $cryptocurrency): string
    {
        // Generate QR code (you would use a QR code library here)
        // For demo purposes, return a placeholder
        return 'data:image/svg+xml;base64,'.base64_encode(
            '<svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <rect width="200" height="200" fill="#FFFFFF"/>
                <rect x="20" y="20" width="20" height="20" fill="#000000"/>
                <rect x="40" y="20" width="20" height="20" fill="#000000"/>
                <rect x="80" y="20" width="20" height="20" fill="#000000"/>
                <rect x="100" y="20" width="20" height="20" fill="#000000"/>
                <rect x="120" y="20" width="20" height="20" fill="#000000"/>
                <text x="100" y="110" font-family="Arial" font-size="12" fill="#666" text-anchor="middle">
                    Payment QR Code
                </text>
            </svg>'
        );
    }
}
