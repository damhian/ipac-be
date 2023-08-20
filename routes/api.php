<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\CompaniesController;
use App\Http\Controllers\API\EventsController;
use App\Http\Controllers\API\ImageuploaderController;
use App\Http\Controllers\API\JobfairController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\StrukturorganisasiController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserexperiencesController;
use App\Http\Controllers\API\UserprofilesController;
use App\Http\Controllers\API\UserstoryController;
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

// Banner Table
Route::get('banners', [BannerController::class, 'index']);
Route::get('banner/{id}', [BannerController::class, 'show']);

// Events Table
Route::get('events', [EventsController::class, 'index']);
Route::get('event/{id}', [EventsController::class, 'show']);

//  Jobfair table
Route::get('jobfairs', [JobfairController::class, 'index']);
Route::get('jobfair/{id}', [JobfairController::class, 'show']);
Route::post('jobfairs/search', [JobfairController::class, 'search']);

// Store Table
Route::get('stores', [StoreController::class, 'index']);
Route::get('store/{id}', [StoreController::class, 'show']);

// User Story Table
Route::get('userstory', [UserstoryController::class, 'index']);
Route::get('userstory/{id}', [UserstoryController::class, 'show']);

// User Profiles and Idcards
Route::get('userprofile/{id}', [UserprofilesController::class, 'show']);

// Struktur Organisasi
Route::get('strukturorganisasi', [StrukturorganisasiController::class, 'index']);

Route::post('userbytahunlulus', [UserController::class, 'showUserbyTahunLulus']);
Route::post('userlulusperthreeyears', [UserController::class, 'countUserbyTahunLulus']);
 
// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function() {
    // check user
    Route::get('/user', function(Request $request) { 
        return $request->user();
    });

    Route::post('auth/logout', LogoutController::class);
    
    // Get data user by id
    Route::get('user/{id}', [UserController::class, 'show']);
    
    // Get data user by token
    Route::get('userbytoken', [UserController::class, 'showbytoken']);

    Route::middleware('admin')->group(function() {
        // Table User
        Route::get('alluserdata', [UserController::class, 'index']);
        Route::post('createuser', [UserController::class, 'store']);
        Route::put('updateuser/{id}', [UserController::class, 'update']);

        // Struktur Organisasi Table
        Route::get('strukturorganisasi/{id}', [StrukturorganisasiController::class, 'show']);
        Route::get('strukturorganisasi/search', [StrukturorganisasiController::class, 'search']);
        Route::post('strukturorganisasi', [StrukturorganisasiController::class, 'store']);
        Route::put('strukturorganisasiupdate/{id}', [StrukturorganisasiController::class, 'update']);
        Route::delete('strukturorganisasidel/{id}', [StrukturorganisasiController::class, 'destroy']);

    });
    
    // Approval for every table
    Route::prefix('admin')->group(function(){
        Route::resource('/user', AdminController::class);
        Route::resource('/banner', AdminController::class);
        Route::resource('/events', AdminController::class);
        Route::resource('/jobfair', AdminController::class);
        Route::resource('/store', AdminController::class);
    });

    // Check user profile
    Route::middleware('profile.completed')->group(function(){
        
        // User Experiences Table
        Route::get('userexp/{id}', [UserexperiencesController::class, 'show']);
        Route::post('userexp', [UserexperiencesController::class, 'store']);
        Route::put('userexpupdate/{id}', [UserexperiencesController::class, 'update']);

        // Store Table
        Route::post('store', [StoreController::class, 'store']);
        Route::put('storeupdate/{id}', [StoreController::class, 'update']);
        Route::delete('storedelete/{id}', [StoreController::class, 'delete']);

        // Jobfair Table
        Route::post('jobfair', [JobfairController::class, 'store']);
        Route::put('jobfairupdate/{id}', [JobfairController::class, 'update']);
        Route::delete('jobfairdelete/{id}', [JobfairController::class, 'delete']);
        
        // Event Table
        Route::post('event', [EventsController::class, 'store']);
        Route::put('eventupdate/{id}', [EventsController::class, 'update']);
        Route::delete('eventdelete/{id}', [EventsController::class, 'delete']);
       
        // User Story Table
        
        Route::post('userstory', [UserstoryController::class, 'store']);
        Route::put('userstoryupdate/{id}', [UserstoryController::class, 'update']);
        Route::delete('userstorydelete/{id}', [UserstoryController::class, 'destroy']);
    });

    // User Experiences Table
    Route::get('userexp', [UserexperiencesController::class, 'index']);
    Route::get('userexpbytoken', [UserexperiencesController::class, 'showByToken']);

    // Store Table
    Route::get('storebytoken', [StoreController::class, 'showByToken']);
    Route::get('storebyuserid/{id}', [StoreController::class, 'showByUserId']);

    // Jobfair Table
    Route::get('jobfairbytoken', [JobfairController::class, 'showByToken']);

    // Events Table
    Route::get('eventsbytoken', [EventsController::class, 'showByToken']);
    
    // User Story Table
    Route::get('userstorybytoken', [UserstoryController::class, 'showByToken']);
    Route::get('userstorybyuserid/{id}', [UserstoryController::class, 'showByUserId']);

    // Image Uploader 
    Route::post('imageupload', [ImageuploaderController::class, 'store']);

    // Banner Table
    Route::post('banner', [BannerController::class, 'store']);
    Route::get('bannerbytoken', [BannerController::class, 'showByToken']);
    Route::put('bannerupdate/{id}', [BannerController::class, 'update']);
    Route::delete('bannerdelete/{id}' ,[BannerController::class, 'delete']);

     // User Profile Table
     Route::get('userprofilebytoken', [UserprofilesController::class, 'showByToken']);
     Route::post('userprofile', [UserprofilesController::class, 'store']);
     Route::put('userprofileupdate/{id}', [UserprofilesController::class, 'update']);
 
     // Companies Table
     Route::get('companies', [CompaniesController::class, 'index']);
     Route::get('company/{id}', [CompaniesController::class, 'show']);
     Route::get('companybytoken', [CompaniesController::class, 'showByToken']);
     Route::post('companies/search', [CompaniesController::class, 'search']);
     Route::post('company', [CompaniesController::class, 'store']);
     Route::put('companyupdate/{id}', [CompaniesController::class, 'update']);
     Route::delete('companydelete/{id}', [CompaniesController::class, 'destroy']);
});

