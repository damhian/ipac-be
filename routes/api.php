<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\CompaniesController;
use App\Http\Controllers\API\EventsController;
use App\Http\Controllers\API\JobfairController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\UserexperiencesController;
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

    // Approval for every table
    Route::prefix('admin')->group(function(){
        Route::resource('/user', AdminController::class)->except(['show']);
        Route::get('/user/{id}', [AdminController::class, 'showUser']);

        Route::resource('/banner', AdminController::class)->except(['show']);
        Route::resource('/events', AdminController::class)->except(['show']);
        Route::resource('/jobfair', AdminController::class)->except(['show']);
        Route::resource('/store', AdminController::class)->except(['show']);
    });

    // User Experiences Table
    Route::get('userexp', [UserexperiencesController::class, 'index']);
    Route::get('userexp/{id}', [UserexperiencesController::class, 'show']);
    
    // Banner Table
    Route::get('banners', [BannerController::class, 'index']);
    Route::get('banner/{id}', [BannerController::class, 'show']);
    Route::post('banner', [BannerController::class, 'store']);
    Route::put('bannerupdate/{id}', [BannerController::class, 'update']);
    Route::delete('bannerdelete/{id}' ,[BannerController::class, 'delete']);

    // Companies Table
    Route::get('companies', [CompaniesController::class, 'index']);
    Route::get('company/{id}', [CompaniesController::class, 'show']);
    Route::post('company', [CompaniesController::class, 'store']);
    Route::put('companyupdate/{id}', [CompaniesController::class, 'update']);
    Route::delete('companydelete/{id}', [CompaniesController::class, 'destroy']);

    // Events Table
    Route::get('events', [EventsController::class, 'index']);
    Route::get('event/{id}', [EventsController::class, 'show']);
    Route::post('event', [EventsController::class, 'store']);
    Route::put('eventupdate/{id}', [EventsController::class, 'update']);
    Route::delete('eventdelete/{id}', [EventsController::class, 'delete']);

    // Jobfair Table
    Route::get('jobfairs', [JobfairController::class, 'index']);
    Route::get('jobfair/{id}', [JobfairController::class, 'show']);
    Route::post('jobfair', [JobfairController::class, 'store']);
    Route::put('jobfairupdate/{id}', [JobfairController::class, 'update']);
    Route::delete('jobfairdelete/{id}', [JobfairController::class, 'delete']);

    // Store Table
    Route::get('stores', [StoreController::class, 'index']);
    Route::get('store/{id}', [StoreController::class, 'show']);
    Route::post('store', [StoreController::class, 'store']);
    Route::put('storeupdate/{id}', [StoreController::class, 'update']);
    Route::delete('storedelete/{id}', [StoreController::class, 'delete']);
});

