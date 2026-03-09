<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;
use App\Helpers\Constants;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Services\PackageService;
use App\Services\CityService;



class Company extends EloquentModel
{
    use HasFactory;
    use HasTranslations, SoftDeletes , HasUuids;



    protected $fillable = [
        'contact_id',
        'commerce_letter',
        'national_id',
        'commercial_record',
        'tax_id',
        'company_name',
        'trade_name',
        'active',
        'city_id',
        'electronic_contract_website',
    ];


    public function companySetting()
    {
        return $this->hasOne(CompanySetting::class , 'company_id', 'id');
    }

    public function getPackagesAttribute()
    {
        if (!isset($this->attributes['cached_packages'])) {
            $this->attributes['cached_packages'] = app(PackageService::class)->getPackagesByCompany($this->id);
        }
        return $this->attributes['cached_packages'];
    }

    // public function activePackage()
    // {
    //     return $this->hasOne(\App\Models\Company\CompanyPackage::class, 'company_id', 'id')
    //         ->active();
    // }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function getActivePackageAttribute()
    {
        if (!isset($this->attributes['cached_active_package'])) {
            $this->attributes['cached_active_package'] = app(PackageService::class)->getActivePackageByCompany($this->id);
        }
        return $this->attributes['cached_active_package'];
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }


    public function getCityAttribute()
    {
        if (!isset($this->attributes['cached_city'])) {
            $this->attributes['cached_city'] = app(CityService::class)->getCityById($this->city_id);
        }
        return $this->attributes['cached_city'];
    }



    // Note: getWithdrawalAttribute was removed as it depends on external domains (Vehicle, Wallet).
    // These should be fetched via API/Services from their respective microservices.



        public function scopeFilter($query, $data)
        {
            $status = $data['status'] ?? '-1';
            $search = $data['search'] ?? '';
            $from_date = $data['from_date'] ?? '';
            $to_date = $data['to_date'] ?? '';
            $have_package = $data['have_package'] ?? '';

            // 1. Search (Search by ID, name, or trade name)
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%")
                      ->orWhere('trade_name', 'like', "%{$search}%");
                });
            }

            // 2. Date Filtering
            if (!empty($from_date)) {
                $query->whereDate('created_at', '>=', $from_date);
            }
            if (!empty($to_date)) {
                $query->whereDate('created_at', '<=', $to_date);
            }

            // 3. Status/Active
            if ($status != '-1') {
                $query->where('active', $status);
            }

            // 4. Package Filter (handled in controller or using PackageService IDs)
            if ($have_package !== '') {
                // Implementation will be handled in Controller using PackageService
            }

            return $query;
        }

}
