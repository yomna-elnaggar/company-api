<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'mobile',
        'image',    
        'active',
        'company_id',
        'branch_id',
        'parent_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean',
    ];

    /**
     * Mutator to automatically hash the password.
     */
    public function setPasswordAttribute($pass)
    {
        if (!$pass) {
            $this->attributes['password'] = null;
        } elseif (\Illuminate\Support\Str::startsWith($pass, '$2y$')) {
            $this->attributes['password'] = $pass;
        } else {
            $this->attributes['password'] = bcrypt($pass);
        }
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function scopeFilter($query, $filters)
    {
        $filters = is_array($filters) ? $filters : [];

        if (!empty($filters['company'])) {
            $query->where('company_id', $filters['company']);
        }

        if (!empty($filters['branch'])) {
            $query->where('branch_id', $filters['branch']);
        }

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getImagePathAttribute()
    {
        $value = $this->image;
        if ($value && $value != '') {
            return asset($value);
        }
        return asset('/user.png');
    }
}
