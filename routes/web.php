<?php

use App\Http\Controllers\CRM\OrderController;
use App\Http\Controllers\CRM\TicketsController;
use App\Http\Controllers\CRM\AuthController;
use App\Http\Controllers\CRM\CategoryController;
use App\Http\Controllers\CRM\ProductController;
use App\Http\Controllers\CRM\SubCategoryController;
use App\Http\Controllers\CRM\RoleController;
use App\Http\Controllers\CRM\PermissionController;
use App\Http\Controllers\CRM\UserController;
use App\Http\Controllers\CRM\PromocodeController;
use App\Http\Controllers\CRM\PincodeController;
use App\Http\Controllers\CRM\GiftCardController;
use App\Http\Controllers\CRM\ProductDescriptionController;
use App\Http\Controllers\CRM\ProductVariableController;
use App\Http\Controllers\CRM\NotificationController;
use App\Http\Controllers\CRM\DashboardController;
//use App\Http\Controllers\CRM\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    $exitCode = Artisan::call('cache:clear');
//    $exitCode = Artisan::call('optimize');
//    $exitCode = Artisan::call('route:cache');
//    $exitCode = Artisan::call('route:clear');
//    $exitCode = Artisan::call('view:clear');
//    $exitCode = Artisan::call('config:cache');
//    $exitCode = Artisan::call('migrate');
    return view('welcome');
})->name('resetAll');

Route::get('/1wdw',[AuthController::class,'login'])->name('login1');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [AuthController::class,'login'])->name('dashboard');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('role', RoleController::class);
    Route::resource('user', UserController::class);
    Route::resource('category',CategoryController::class);
    Route::resource('subcategory',SubCategoryController::class);
    Route::resource('product',ProductController::class);
    Route::resource('permission',PermissionController::class);
    Route::resource('promocode',PromocodeController::class);
    Route::resource('giftcard',GiftCardController::class);
    Route::resource('pincode',PincodeController::class);
    //Route::resource('order',OrderController::class);
    Route::resource('productDescription',ProductDescriptionController::class);
    Route::resource('productVariable',ProductVariableController::class);
    Route::resource('notification',NotificationController::class);
    Route::resource('dashboard',DashboardController::class);
    Route::get('orders/{id}',[OrderController::class,'orderindex'])->name('orderindex');
    Route::post('confirmOrder/{id}',[OrderController::class,'confirmOrder'])->name('confirmOrder');
    //order
    Route::resource('order',OrderController::class);
    //tickets
    Route::get('tickets',[TicketsController::class,'index'])->name('tickets.index');
    Route::get('tickets/assignToMe/{id}',[TicketsController::class,'assignToMe'])->name('tickets.assign');
    Route::get('tickets/closeTicket/{id}',[TicketsController::class,'close'])->name('tickets.close');
    Route::get('tickets/openTicket/{id}',[TicketsController::class,'open'])->name('tickets.open');
    Route::post('tickets/addMessage',[TicketsController::class,'addMessage'])->name('tickets.addMessage');
    Route::get('tickets/getMessage/{id}',[TicketsController::class,'getMessage'])->name('tickets.getMessage');


});

//required api
Route::group(['middleware' => ['auth:sanctum']], function () {
    //Product
    Route::get('getProductList',[ProductController::class,'getProductList'])->name('getProductList');
    Route::get('deleteProduct/{id}',[ProductController::class,'deleteProduct'])->name('deleteProduct');
    //Customer
    Route::get('getCustomers',[UserController::class,'getCustomers'])->name('getCustomers');
    Route::get('showCustomer/{id}',[UserController::class,'showCustomer'])->name('showCustomer');
    //Orders
    //Route::get('getOrderList',[OrderController::class,'getOrderList'])->name('getOrderList');
    //Categories
    Route::get('getSubCategory',[CategoryController::class,'getSubCategory'])->name('getSubCategory');
    //Product Description
    Route::get('productDescription/create/{id}',[ProductDescriptionController::class,'createNewProductDescription'])->name('productDescription.create');
    //Product Variable
    Route::get('productVariable/create/{id}',[ProductVariableController::class,'createNewProductVariable'])->name('productVariable.create');
    Route::get('productVariable/destroyImage/{id}',[ProductVariableController::class,'destroyImage'])->name('productVariable.destroyImage');
    Route::get('getOrders',[OrderController::class,'getOrders'])->name('getOrders');
});
