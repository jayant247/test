<?php

use App\Http\Controllers\CRM\AuthController;
use App\Http\Controllers\CRM\CategoryController;
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
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('optimize');
    $exitCode = Artisan::call('route:cache');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('config:cache');
    $exitCode = Artisan::call('migrate');
    return view('welcome');
})->name('resetAll');

Route::get('/1wdw',[AuthController::class,'login'])->name('login1');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', [AuthController::class,'login'])->name('dashboard');

Route::group(['middleware' => ['auth:sanctum']], function () {
   Route::resource('category',CategoryController::class);
});
