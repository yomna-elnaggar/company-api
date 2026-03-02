<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanySettingsRequest;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Exception;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(CreateCompanyRequest $request): JsonResponse
    {
        \Illuminate\Support\Facades\Log::info('Company Store Request:', $request->all());
        
        try {
            $validated = $request->validated();
            
            $result = $this->companyService->createCompany($validated);
            \Illuminate\Support\Facades\Log::info('Company Saved Result:', $result);

            return response()->json($result, 201);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('API Store Error: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => 'Operation Failed: ' . $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Display a listing of companies.
     */
    public function index(): JsonResponse
    {
        try {
            $companies = $this->companyService->getAllCompanies();
            return response()->json([
                'status' => true,
                'data' => $companies
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch companies.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified company.
     */
    public function show($id): JsonResponse
    {
        try {
            $company = $this->companyService->getCompanyDetails($id);
            return response()->json([
                'status' => true,
                'data' => $company
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Company not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the company settings.
     */
    public function updateSettings(UpdateCompanySettingsRequest $request, $id): JsonResponse
    {
        $validated = $request->validated();
        
        if (empty($validated)) {
            return response()->json([
                'status' => false,
                'message' => 'No data was provided for update. Make sure you are sending the body as JSON or x-www-form-urlencoded (PUT requests do not support multipart/form-data in PHP).',
            ], 422);
        }

        try {
            $settings = $this->companyService->updateSettings($id, $validated);
            return response()->json([
                'status' => true,
                'message' => 'Settings updated successfully',
                'data' => $settings
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update settings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
