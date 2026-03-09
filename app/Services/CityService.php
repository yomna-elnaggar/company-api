<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CityService
{
    protected string $baseUrl;

    public function __construct()
    {
        // Placeholder URL - Should be set in .env as CITY_SETTINGS_SERVICE_URL
        $this->baseUrl = config('services.cities.url') ?? 'http://cities-service/api';
    }

    public function getAllCities()
    {
        try {
            // Assuming there's an endpoint to get active packages
            $response = Http::timeout(5)->get("{$this->baseUrl}/cities", ['active' => 1]);
            
            if ($response->successful()) {
                $content = $response->json('data');
                // Support multiple nesting styles: items.data (paginated) or items (direct)
                if (isset($content['items']['data'])) {
                    return $content['items']['data'];
                }
                if (isset($content['items'])) {
                    return $content['items'];
                }
                return $content ?? [];
            }
            
            Log::error("Failed to fetch cities: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("CityService Error: " . $e->getMessage());
            return [];
        }
    }

    public function getCityById($id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/cities/{$id}");
            
            if ($response->successful()) {
                return $response->json('data') ?? null;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("CityService Error: " . $e->getMessage());
            return null;
        }
    }

    public function getAllActiveCities()
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/cities", ['active' => 1]);
            
            if ($response->successful()) {
                $content = $response->json('data');
                if (isset($content['items']['data'])) {
                    return $content['items']['data'];
                }
                if (isset($content['items'])) {
                    return $content['items'];
                }
                return $content ?? [];
            }
            
            Log::error("Failed to fetch active cities: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("CityService Error: " . $e->getMessage());
            return [];
        }
    }

   
}
