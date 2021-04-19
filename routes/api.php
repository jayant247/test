<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['jwt.verify'],'prefix' => 'metadata','as' => 'metadata.'], function () {

    //Roles & Permissions
    Route::post('createRole',[AuthController::class,'createRole']);
    Route::post('createPermission',[AuthController::class,'createPermission']);
    Route::post('assignRole',[AuthController::class,'assignRole']);
    Route::put('updateRole/{id}',[AuthController::class,'updateRole']);
    Route::get('getPermissionsList',[AuthController::class,'getPermissionsList']);
    Route::get('getRoles',[AuthController::class,'getRoles']);

    //category
    Route::post('createCategory',[CategoryController::class,'createCategory']);
    Route::post('updateCategory/{id}',[CategoryController::class,'updateCategory']);

    //product
    Route::post('createProduct',[ProductController::class,'createProduct']);
    Route::post('updateProduct/{id}',[ProductController::class,'updateProduct']);
    Route::delete('deleteProduct/{id}',[ProductController::class,'deleteProduct']);

    //product Description
    Route::post('createProductDescription',[ProductController::class,'createProductDescription']);
    Route::post('updateProductDescription/{id}',[ProductController::class,'updateProductDescription']);
    Route::delete('deleteProductDescription/{id}',[ProductController::class,'deleteProductDescription']);

    //product variables
    Route::post('createProductVariable',[ProductController::class,'createProductVariable']);
    Route::post('updateProductVariable/{id}',[ProductController::class,'updateProductVariable']);
    Route::delete('deleteProductVariable/{id}',[ProductController::class,'deleteProductVariable']);

    //product Images
    Route::post('createProductImages',[ProductController::class,'createProductImages']);
    Route::post('updateProductImages/{id}',[ProductController::class,'updateProductImages']);
    Route::delete('deleteProductImages/{id}',[ProductController::class,'deleteProductImages']);
});

Route::group(['middleware' => [],'prefix' => 'auth','as' => 'auth.'], function () {

    Route::post('customerRegistration',[AuthController::class,'customerRegistration']);
    Route::post('customerLoginWithOtp',[AuthController::class,'customerLoginWithOtp']);
    Route::post('verifyMobileOtp',[AuthController::class,'verifyMobileOtp']);
    Route::post('resendOtp',[AuthController::class,'resendOtp']);
    Route::post('verifyEmailOtp',[AuthController::class,'verifyEmailOtp']);
    Route::post('resendEmailOtp',[AuthController::class,'resendEmailOtp']);

});

Route::group(['middleware' => [],'prefix' => 'frontend','as' => 'frontend.'], function () {

    Route::get('getCategory',[CategoryController::class,'getCategory']);
    Route::get('getProducts',[ProductController::class,'getProduct']);
    Route::get('getProdcutDescription',[ProductController::class,'getProductDescription']);
    Route::get('getProductVariable',[ProductController::class,'getProductVariable']);
    Route::get('getProductImages',[ProductController::class,'getProductImages']);

});
