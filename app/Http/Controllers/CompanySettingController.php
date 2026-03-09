<?php

namespace App\Http\Controllers;

use App\Interfaces\CRUDRepositoryInterface;
use App\Http\Requests\UpdateCompanySettingsRequest;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Exception;

class CompanySettingController extends ApiController
{ 
    protected $itemRepository;
    protected $model = CompanySetting::class;

    public function __construct(CRUDRepositoryInterface $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function show($companyId)
    {
        try {

            // $serviceProviders = ServiceProvider::where('active', 1)->get();

            // $currentProviders = CompanyServiceProvider::where('company_id', $id)
            //     ->pluck('service_provider_id')
            //     ->toArray();

            // $allProvidersIds = $serviceProviders->pluck('id')->toArray();

            // if (!empty($currentProviders) && count(array_diff($allProvidersIds, $currentProviders)) === 0) {
            //     $currentProviders = ['all'];
            // }

            $settings = CompanySetting::firstOrCreate(
                ['company_id' => $companyId],
                ['company_id' => $companyId]
            );

            return ApiController::respondWithSuccess(  __('messages.retrieved_successfully'), $settings);
        } catch (Exception $e) {
            return ApiController::respondWithError($e->getMessage(), null, 500);
        }
    }

    public function update(UpdateCompanySettingsRequest $request)
    {
        try {
            $data = $request->validated();
            
            $settings = CompanySetting::updateOrCreate(
                ['company_id' => $data['company_id']],
                $data
            );

            return ApiController::respondWithSuccess(__('messages.UpdatedMessage'), $settings);
        } catch (Exception $e) {
            return ApiController::respondWithError($e->getMessage(), null, 500);
        }
    }
}
