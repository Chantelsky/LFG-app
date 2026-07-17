<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IgdbService
{
    protected function getAccessToken(): string
    {
        return Cache::remember('igdb_access_token', now()->addDays(55), function () {
            $response = Http::post('https://id.twitch.tv/oauth2/token', [
                'client_id' => config('services.igdb.client_id'),
                'client_secret' => config('services.igdb.client_secret'),
                'grant_type' => 'client_credentials',
            ]);

            return $response->json('access_token');
        });
    }

    public function search(string $query): array
    {
        $token = $this->getAccessToken();

        $response = Http::withHeaders([
            'Client-ID' => config('services.igdb.client_id'),
            'Authorization' => "Bearer {$token}",
        ])->withBody(
            "search \"{$query}\"; fields name, cover.url; limit 10;",
            'text/plain'
        )->post('https://api.igdb.com/v4/games');

        return $response->json() ?? [];
    }
}
