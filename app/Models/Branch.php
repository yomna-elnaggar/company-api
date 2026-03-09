<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Services\CityService;
class Branch extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'company_branches';

    protected $fillable = [
        'title',
        'address',
        'phone',
        'email',
        'manager_name',
        'manager_contact',
        'latitude',
        'longitude',
        'active',
        'company_id',
        'city_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function getCityAttribute()
    {
        return app(CityService::class)->getCityById($this->city_id);
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query, $active = true)
    {
        return $query->where('active', $active);
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        return $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sq) use ($search) {
                $sq->where('title', 'like', "%{$search}%");
            });
        })
        ->when($filters['company_id'] ?? null, function ($q, $companyId) {
            $q->where('company_id', $companyId);
        })
        ->when($filters['city_id'] ?? null, function ($q, $cityId) {
            $q->where('city_id', $cityId);
        })
        ->when(isset($filters['active']), function ($q) use ($filters) {
            $q->where('active', $filters['active']);
        });
    }
}
