<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PackageService
{
    protected string $baseUrl;

    public function __construct()
    {
        // Placeholder URL - Should be set in .env as PACKAGE_SETTINGS_SERVICE_URL
        $this->baseUrl = config('services.packages.url') ?? 'http://packages-service/api';
    }

    public function getAllpackages()
    {
        try {
            // Assuming there's an endpoint to get active packages
            $url = "{$this->baseUrl}/packages";
            $params = ['active' => 1];
            $response = Http::timeout(5)->get($url, $params);
            
            // Log raw response after API call
            Log::info("PackageService API Call: {$url} with params " . json_encode($params), [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $content = $response->json('data');
                // Log extracted content
                Log::info("PackageService Extracted Content from {$url}", ['content' => $content]);
                
                // Support multiple nesting styles: items.data (paginated) or items (direct)
                if (isset($content['items']['data'])) {
                    return $content['items']['data'];
                }
                if (isset($content['items'])) {
                    return $content['items'];
                }
                return $content ?? [];
            }
            
            Log::error("Failed to fetch packages: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("PackageService Error: " . $e->getMessage());
            return [];
        }
    }

    public function getPackageById($id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/packages/{$id}");
            
            if ($response->successful()) {
                return $response->json('data') ?? null;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("PackageService Error: " . $e->getMessage());
            return null;
        }
    }

    public function getPackagesByCompany($companyId)
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/packages", ['company_id' => $companyId]);

            if ($response->successful()) {
                $content = $response->json('data');
                // Standardize extraction
                if (isset($content['items']['data'])) {
                    return $content['items']['data'];
                }
                if (isset($content['items'])) {
                    return $content['items'];
                }
                return $content ?? [];
            }

            Log::error("Failed to fetch packages for company {$companyId}: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("PackageService Error: " . $e->getMessage());
            return [];
        }
    }

    public function getActivePackageByCompany($companyId)
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/subscriptions", [
                'company_id' => $companyId,
                'active' => 1,
                'latest' => 1
            ]);

            if ($response->successful()) {
                $content = $response->json('data');
                // Extract list from data
                $packages = $content;
                if (isset($content['items']['data'])) {
                    $packages = $content['items']['data'];
                } elseif (isset($content['items'])) {
                    $packages = $content['items'];
                }
                
                $packages = $packages ?? [];
                return count($packages) > 0 ? $packages[0] : null;
            }

            Log::error("Failed to fetch active package for company {$companyId}: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("PackageService Error: " . $e->getMessage());
            return null;
        }
    }

    public function getCompanyIdsWithActivePackages()
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/subscriptions", [
                'active' => 1
            ]);
            
            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                // Extract company_id from subscriptions and return unique ones
                return collect($data)->pluck('company_id')->unique()->toArray();
            }
            return [];
        } catch (\Exception $e) {
            Log::error("PackageService Error (Active IDs): " . $e->getMessage());
            return [];
        }
    }

    public function subscribeCompany(array $data)
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/subscriptions", $data);

            if ($response->successful()) {
                return $response->json('data');
            }

            Log::error("Failed to subscribe company: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("PackageService Error: " . $e->getMessage());
            return null;
        }
    }

    public function getAllActivePackages()
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/packages", ['active' => 1]);
            
            if ($response->successful()) {
                $content = $response->json('data');
                Log::info("PackageService Response from {$this->baseUrl}/packages", ['content' => $content]);

                // Check if the result is paginated/nested: data -> items -> data
                if (isset($content['items']['data'])) {
                    return $content['items']['data'];
                }
                // Check if the result is directly in items
                if (isset($content['items'])) {
                    return $content['items'];
                }
                return $content ?? [];
            }
            
            Log::error("Failed to fetch active packages: " . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error("PackageService Error (Active): " . $e->getMessage());
            return [];
        }
    }
}
