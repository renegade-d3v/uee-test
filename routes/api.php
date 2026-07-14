<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CompanyVersionController;
use Illuminate\Support\Facades\Route;

Route::post('/company', [CompanyController::class, 'store']);
Route::get('/company/{edrpou}/versions', [CompanyVersionController::class, 'index']);
