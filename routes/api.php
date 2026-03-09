<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanySettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\City;
use App\Models\SalePerson;


Route::get('/companies/expired', [CompanyController::class , 'expired']);
Route::Resource('/companies', CompanyController::class);


Route::get('/companies/{id}/settings', [CompanySettingController::class , 'show']);
Route::put('/companies/{id}/settings', [CompanySettingController::class , 'update']);

Route::Resource('branches', BranchController::class);
Route::post('branches/{id}/toggle-status', [BranchController::class , 'toggleStatus']);




