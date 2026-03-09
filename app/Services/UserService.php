<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected string $baseUrl;

    public function __construct()
    {
        // Placeholder URL - Should be set in .env
        $this->baseUrl = config('services.users.url', 'http://users-service/api');
    }

    /**
     * Create a new user in the User Microservice.
     */
    public function createUser(array $userData)
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/users", $userData);

            if ($response->successful()) {
                return $response->json('data');
            }

            Log::error("Failed to create user in Microservice: " . $response->body());
            throw new \Exception("User creation in Microservice failed: " . $response->body());
        } catch (\Exception $e) {
            Log::error("UserService Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get user by ID from the User Microservice.
     */
    public function getUserById($id)
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/users/{$id}");

            if ($response->successful()) {
                return $response->json('data');
            }

            return null;
        } catch (\Exception $e) {
            Log::error("UserService Error: " . $e->getMessage());
            return null;
        }
    }
    /**
     * Update a user in the User Microservice.
     */
    public function updateUser($id, array $userData)
    {
        try {
            $response = Http::timeout(5)->put("{$this->baseUrl}/users/{$id}", $userData);

            if ($response->successful()) {
                return $response->json('data');
            }

            Log::error("Failed to update user in Microservice: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("UserService Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user by Company ID from the User Microservice.
     */
    public function getUserByCompanyId($companyId)
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/users", ['company_id' => $companyId]);

            if ($response->successful()) {
                $users = $response->json('data') ?? [];
                return count($users) > 0 ? $users[0] : null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error("UserService Error: " . $e->getMessage());
            return null;
        }
    }
}
