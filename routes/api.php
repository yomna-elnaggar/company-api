<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\City;
use App\Models\SalePerson;

Route::post('/companies', [CompanyController::class, 'store']);
Route::get('/companies', [CompanyController::class, 'index']);
Route::get('/companies/{id}', [CompanyController::class, 'show']);
Route::put('/companies/{id}/settings', [CompanyController::class, 'updateSettings']);

Route::get('/cities', function () {
    return response()->json(['status' => true, 'data' => City::all()]);
});

Route::get('/sales-person', function () {
    return response()->json(['status' => true, 'data' => SalePerson::all()]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
