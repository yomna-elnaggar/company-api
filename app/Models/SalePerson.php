<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalePerson extends Model
{
    protected $fillable = ['name', 'active'];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
