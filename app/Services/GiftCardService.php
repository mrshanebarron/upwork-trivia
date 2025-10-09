<?php

namespace App\Services;

use App\Models\Winner;
use App\Models\GiftCard;
use App\Models\GiftCardDeliveryLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GiftCardService
{
    protected string $apiUrl;
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.tremendous.api_url', 'https://api.tremendous.com/api/v2');
        $this->apiKey = config('services.tremendous.api_key');
    }

    /**
     * Deliver gift card to winner
     */
    public function deliverGiftCard(Winner $winner): GiftCard
    {
        $user = $winner->user;
        $orderId = 'ORDER-' . Str::uuid();
        $rewardId = 'REWARD-' . Str::uuid();

        // Create gift card record
        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => $orderId,
            'reward_id' => $rewardId,
            'amount' => $winner->prize_amount,
            'currency' => 'USD',
            'status' => 'pending',
            'provider' => 'tremendous',
        ]);

        // Attempt delivery
        try {
            $response = $this->sendToTremendous($user, $winner->prize_amount, $orderId, $rewardId);

            if ($response['success']) {
                $giftCard->markAsDelivered($response['redemption_link']);
                $giftCard->update(['provider_response' => $response['data']]);

                $this->logDelivery($giftCard, 1, 'success', null, $response['data']);

                Log::info('Gift card delivered', [
                    'winner_id' => $winner->id,
                    'gift_card_id' => $giftCard->id,
                ]);
            } else {
                throw new \Exception($response['error']);
            }
        } catch (\Exception $e) {
            $giftCard->markAsFailed($e->getMessage());
            $this->logDelivery($giftCard, 1, 'failed', $e->getMessage());

            Log::error('Gift card delivery failed', [
                'winner_id' => $winner->id,
                'gift_card_id' => $giftCard->id,
                'error' => $e->getMessage(),
            ]);

            // Queue for retry
            dispatch(function () use ($giftCard) {
                $this->retryDelivery($giftCard);
            })->delay(now()->addMinutes(5));
        }

        return $giftCard;
    }

    /**
     * Send gift card request to Tremendous API
     */
    protected function sendToTremendous(
        $user,
        float $amount,
        string $orderId,
        string $rewardId
    ): array {
        // Check if in sandbox mode
        if (app()->environment('local') || !$this->apiKey) {
            return $this->mockTremendousResponse($orderId, $rewardId, $amount);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/orders', [
                'external_id' => $orderId,
                'payment' => [
                    'funding_source_id' => config('services.tremendous.funding_source_id'),
                ],
                'reward' => [
                    'value' => [
                        'denomination' => $amount,
                        'currency_code' => 'USD',
                    ],
                    'recipient' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'delivery' => [
                        'method' => 'EMAIL',
                    ],
                    'products' => ['CATALOG'],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'redemption_link' => $data['reward']['redemption_link'] ?? null,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mock Tremendous response for local development
     */
    protected function mockTremendousResponse(string $orderId, string $rewardId, float $amount): array
    {
        return [
            'success' => true,
            'redemption_link' => 'https://tremendous.com/redeem/mock-' . Str::random(16),
            'data' => [
                'order' => [
                    'id' => $orderId,
                    'external_id' => $orderId,
                ],
                'reward' => [
                    'id' => $rewardId,
                    'value' => [
                        'denomination' => $amount,
                        'currency_code' => 'USD',
                    ],
                    'redemption_link' => 'https://tremendous.com/redeem/mock-' . Str::random(16),
                ],
            ],
        ];
    }

    /**
     * Retry failed delivery
     */
    public function retryDelivery(GiftCard $giftCard, int $attemptNumber = 2): void
    {
        if ($attemptNumber > 5) {
            Log::error('Gift card delivery max retries exceeded', [
                'gift_card_id' => $giftCard->id,
            ]);
            return;
        }

        $user = $giftCard->user;
        $winner = $giftCard->winner;

        try {
            $response = $this->sendToTremendous(
                $user,
                $giftCard->amount,
                $giftCard->order_id,
                $giftCard->reward_id
            );

            if ($response['success']) {
                $giftCard->markAsDelivered($response['redemption_link']);
                $this->logDelivery($giftCard, $attemptNumber, 'success', null, $response['data']);
            } else {
                throw new \Exception($response['error']);
            }
        } catch (\Exception $e) {
            $this->logDelivery($giftCard, $attemptNumber, 'failed', $e->getMessage());

            // Exponential backoff for retries
            $delay = pow(2, $attemptNumber) * 5; // 10, 20, 40, 80 minutes
            dispatch(function () use ($giftCard, $attemptNumber) {
                $this->retryDelivery($giftCard, $attemptNumber + 1);
            })->delay(now()->addMinutes($delay));
        }
    }

    /**
     * Log delivery attempt
     */
    protected function logDelivery(
        GiftCard $giftCard,
        int $attemptNumber,
        string $status,
        ?string $errorMessage = null,
        ?array $apiResponse = null
    ): void {
        GiftCardDeliveryLog::create([
            'gift_card_id' => $giftCard->id,
            'attempt_number' => $attemptNumber,
            'status' => $status,
            'error_message' => $errorMessage,
            'api_response' => $apiResponse,
            'attempted_at' => now(),
        ]);
    }
}
