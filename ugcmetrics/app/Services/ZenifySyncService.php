<?php

namespace App\Services;

use App\Models\SeedingLead;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ZenifySyncService
{
    /**
     * Synchronize a SeedingLead to Zenify.cx CRM.
     */
    public function sync(SeedingLead $lead): bool
    {
        $lead->status = 'syncing';
        $lead->save();

        $apiKey = config('services.zenify.key');
        $apiUrl = config('services.zenify.url', 'https://api.zenify.cx/v1/leads');

        $seederUsername = $lead->seederAccount ? $lead->seederAccount->username : null;
        $platformDisplay = ucfirst($lead->platform);

        $payload = [
            'name' => $lead->customer_name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'source' => "Seeding {$platformDisplay}",
            'description' => $lead->content,
            'profile_url' => $lead->profile_url,
            'comment_url' => $lead->comment_url,
            'seeder_username' => $seederUsername,
            'external_id' => (string) $lead->id,
        ];

        if (empty($apiKey) || $apiKey === 'MOCK' || $apiKey === 'placeholder') {
            // Mock mode success
            Log::info('Zenify Sync: Running in MOCK mode (no API key).', ['payload' => $payload]);
            
            // Simulating a minor lag or immediate success
            $lead->status = 'synced';
            $lead->zenify_lead_id = 'zen_' . Str::random(12);
            $lead->sync_error = null;
            $lead->save();

            return true;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($apiUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                $zenifyLeadId = $responseData['id'] ?? $responseData['lead_id'] ?? $responseData['data']['id'] ?? 'zen_api_' . Str::random(8);

                $lead->status = 'synced';
                $lead->zenify_lead_id = $zenifyLeadId;
                $lead->sync_error = null;
                $lead->save();

                return true;
            }

            // API request failed (e.g. 4xx, 5xx)
            $errorMsg = "HTTP Status: {$response->status()}. Response: " . substr($response->body(), 0, 1000);
            Log::warning('Zenify Sync Failed: ' . $errorMsg, ['payload' => $payload]);

            $lead->status = 'failed';
            $lead->sync_error = $errorMsg;
            $lead->save();

            return false;

        } catch (\Exception $e) {
            // Network or connection errors
            $errorMsg = 'Exception: ' . $e->getMessage();
            Log::error('Zenify Sync Exception: ' . $errorMsg, ['payload' => $payload]);

            $lead->status = 'failed';
            $lead->sync_error = $errorMsg;
            $lead->save();

            return false;
        }
    }
}
