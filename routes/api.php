<?php

use App\Http\Controllers\API\ApproveUserController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\CompaniesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('auth/register', RegisterController::class);
Route::post('auth/login', LoginController::class);

// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function() {
    // check user
    Route::get('/user', function(Request $request) { 
        return $request->user();
    });

    Route::post('auth/logout', LogoutController::class);
    Route::post('approveuser', ApproveUserController::class);
    
    // Companies Table
    Route::get('companies', [CompaniesController::class, 'index']);
    Route::get('company/{id}', [CompaniesController::class, 'show']);
    Route::post('company', [CompaniesController::class, 'store']);
    Route::put('companyupdate/{id}', [CompaniesController::class, 'update']);
    Route::delete('companydelete/{id}', [CompaniesController::class, 'destroy']);

    // Banner Table
    Route::get('banners', [BannerController::class, 'index']);
    Route::get('banner/{id}', [BannerController::class, 'show']);
    Route::post('banner', [BannerController::class, 'store']);
    Route::put('bannerupdate/{id}', [BannerController::class, 'update']);
    Route::delete('bannerdelete/{id}' ,[BannerController::class, 'delete']);
});

