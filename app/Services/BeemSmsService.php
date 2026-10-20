<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BeemSmsService
{
    private string $apiKey;
    private string $secretKey;
    private string $senderId;
    private string $endpoint = 'https://apisms.beem.africa/v1/send';

    public function __construct()
    {
        $this->apiKey    = config('services.beem.api_key', '');
        $this->secretKey = config('services.beem.secret_key', '');
        $this->senderId  = config('services.beem.sender_id', 'KANISA');
    }

    /**
     * Send an SMS to one or many recipients.
     *
     * @param  array<int,array{recipient_id:int,dest_addr:string}>  $recipients
     * @param  string  $message
     * @return array{success:bool,sent:int,failed:int,response:array|null,error:string|null}
     */
    public function send(array $recipients, string $message): array
    {
        if (empty($this->apiKey) || empty($this->secretKey)) {
            Log::error('Beem SMS: API key or secret key not configured.');
            return [
                'success'  => false,
                'sent'     => 0,
                'failed'   => count($recipients),
                'response' => null,
                'error'    => 'Beem API credentials are not configured.',
            ];
        }

        $payload = [
            'source_addr'   => $this->senderId,
            'schedule_time' => '',
            'encoding'      => 0,
            'message'       => $message,
            'recipients'    => $recipients,
        ];

        try {
            $response = Http::withBasicAuth($this->apiKey, $this->secretKey)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->endpoint, $payload);

            $body = $response->json();

            if ($response->successful() && ($body['successful'] ?? false) === true) {
                $valid = (int) ($body['valid'] ?? count($recipients));
                $invalid = (int) ($body['invalid'] ?? 0);
                return [
                    'success'  => true,
                    'sent'     => $valid,
                    'failed'   => $invalid,
                    'response' => $body,
                    'error'    => null,
                ];
            }

            $errorMsg = $body['message'] ?? 'Unknown error from Beem API.';
            Log::error('Beem SMS send failed.', [
                'status'   => $response->status(),
                'response' => $body,
            ]);

            return [
                'success'  => false,
                'sent'     => 0,
                'failed'   => count($recipients),
                'response' => $body,
                'error'    => "Beem API Error: {$errorMsg}",
            ];

        } catch (\Exception $e) {
            Log::error('Beem SMS exception: ' . $e->getMessage());
            return [
                'success'  => false,
                'sent'     => 0,
                'failed'   => count($recipients),
                'response' => null,
                'error'    => 'HTTP request to Beem failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Normalise a phone number to international format (e.g. 255700000001).
     * Handles +255, 255, 07xx, 7xx formats common in Tanzania.
     */
    public static function normalisePhone(string $phone): string
    {
        // Strip all spaces, dashes, parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        if (str_starts_with($phone, '+')) {
            return ltrim($phone, '+');
        }

        // Tanzania local format: 07xxxxxxxx or 7xxxxxxxx → 2557xxxxxxxx
        if (preg_match('/^0[67]\d{8}$/', $phone)) {
            return '255' . ltrim($phone, '0');
        }

        if (preg_match('/^[67]\d{8}$/', $phone)) {
            return '255' . $phone;
        }

        return $phone; // return as-is for already-international numbers
    }
}
