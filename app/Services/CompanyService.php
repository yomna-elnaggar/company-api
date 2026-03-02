<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Repositories\CompanyRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;


class CompanyService
{
    protected $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * Orchestrate the complete company creation flow.
     */
    public function createCompany(array $data): array
    {
        Log::info('CompanyService::createCompany started', [
            'company_name' => $data['company_name'] ?? null,
            'user_email' => $data['user']['email'] ?? null,
        ]);

        DB::beginTransaction();

        try {
            //  إنشاء الشركة
            $company = $this->companyRepository->createCompany($data);
            if (!$company) {
                throw new Exception('Failed to create company');
            }
            Log::info('Company record created', ['company_id' => $company->id]);

            //  إنشاء المستخدم الرئيسي للشركة
            $user = $this->companyRepository->createUser($company, $data['user']);
            if (!$user) {
                throw new Exception('Failed to create company user');
            }
            Log::info('Company user created', ['user_id' => $user->id]);

            //  إنشاء إعدادات الشركة
            $settings = $this->companyRepository->createSettings($company);
            if (!$settings) {
                throw new Exception('Failed to create company settings');
            }
            Log::info('Company settings created', ['settings_id' => $settings->id]);

            //  إنشاء محفظة الشركة
            $wallet = $this->companyRepository->createWallet($company);
            if (!$wallet) {
                throw new Exception('Failed to create company wallet');
            }
            Log::info('Company wallet created', ['wallet_number' => $wallet->wallet_number]);

            // Commit Transaction
            DB::commit();
            Log::info('CompanyService::createCompany transaction committed');

            return [
                'status' => true,
                'message' => 'Company created successfully',
                'data' => [
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'wallet_number' => $wallet->wallet_number,
                ]
            ];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Company creation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; //  Re-throw so controller catch block can handle it
        }
    }

    /**
     * Get list of all companies.
     */
    public function getAllCompanies()
    {
        return Company::with(['city', 'salePerson', 'wallet', 'user'])->latest()->get();
    }

    /**
     * Get specific company with its relations.
     */
    public function getCompanyDetails(int $id)
    {
        return Company::with(['city', 'salePerson', 'user', 'settings', 'wallet'])->findOrFail($id);
    }

    /**
     * Update company settings.
     */
    public function updateSettings(int $companyId, array $settingsData)
    {
        $settings = CompanySetting::where('company_id', $companyId)->firstOrFail();
        $settings->update($settingsData);
        return $settings->fresh();
    }
}
