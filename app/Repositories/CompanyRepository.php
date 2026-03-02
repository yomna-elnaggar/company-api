<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\CompanySetting;
use App\Models\User;
use App\Models\UserType;
use App\Models\Wallet;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyRepository
{
    /**
     * Persist a new Company record.
     */
    public function createCompany(array $data): Company
    {
        return Company::create([
            'company_name'                => $data['company_name'],
            'tax_id'                      => $data['tax_id'],
            'commercial_record'           => $data['commercial_record']           ?? null,
            'national_id'                 => $data['national_id']                 ?? null,
            'commerce_letter'             => $data['commerce_letter']             ?? null,
            'contact_id'                  => $data['contact_id']                  ?? null,
            'electronic_contract_website' => $data['electronic_contract_website'] ?? null,
            'city_id'                     => $data['city_id']                     ?? null,
            'sale_person_id'              => $data['sale_person_id']              ?? null,
            'active'                      => true,
        ]);
    }

    /**
     * Persist the company main user.
     */
    public function createUser(Company $company, array $userData): User
    {
        $companyTypeId = UserType::where('slug', 'company')->value('id');

        $imagePath = null;
        if (! empty($userData['image'])) {
            $imagePath = $userData['image']->store('company_users', 'public');
        }

        return User::create([
            'name'         => $userData['name'],
            'email'        => $userData['email'],
            'username'     => $userData['username'],
            'password'     => $userData['password'],   
            'mobile'       => $userData['mobile'],
            'iqama'        => $userData['iqama']  ?? null,
            'image'        => $imagePath,
            'active'       => true,
            'user_type_id' => $companyTypeId,
            'company_id'   => $company->id,
        ]);
    }

    /**
     * Auto-generate company settings with spec defaults.
     */
    public function createSettings(Company $company): CompanySetting
    {
        return CompanySetting::create([
            'company_id'                    => $company->id,
            'is_meter_number_required'      => false,
            'is_meter_image_required'       => false,
            'is_code_changeable'            => true,
            'is_code_generator'             => false,
            'code'                          => str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT),
            'login_with_otp'                => false,
            'vehicle_limit_type'            => 'daily',
            'vehicle_balance_min'           => null,
            'vehicles_can_use_fuel_balance' => false,
            'type_of_wallet'                => 'vehicle',
            'auto_balance'                  => false,
            'fuel_pull_limit'               => null,
            'fuel_pull_limit_days'          => null,
            'wash_count'                    => null,
            'vehicle_receiving_terms'       => null,
            'allow_multiple_assignments'    => true,
        ]);
    }

    /**
     * Auto-generate a unique wallet for the company.
     */
    public function createWallet(Company $company): Wallet
    {
        do {
            $walletNumber = 'WLT-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Wallet::where('wallet_number', $walletNumber)->exists());

        return Wallet::create([
            'wallet_number' => $walletNumber,
            'balance'       => 0,
            'active'        => true,
            'company_id'    => $company->id,
        ]);
    }
}
