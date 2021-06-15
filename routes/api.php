
<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DeliveryPincodeController;
use App\Http\Controllers\API\GiftCardController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserDetailsController;
use App\Http\Controllers\API\OrderController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['jwt.verify'],'prefix' => 'metadata','as' => 'metadata.'], function () {
    //Create Password
    Route::post('createPassword',[AuthController::class,'createPassword']);

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

    //product Review
    Route::post('createProductReview',[ProductController::class,'createProductReview']);
    Route::post('updateProductReview/{id}',[ProductController::class,'updateProductReview']);
    Route::delete('deleteProductReview/{id}',[ProductController::class,'deleteProductReview']);

    //user wishlist
    Route::post('addUserWishList',[UserDetailsController::class,'addUserWishList']);
    Route::get('getUserWishlist',[UserDetailsController::class,'getUserWishlist']);

    //user address
    Route::post('createUserAddress',[UserDetailsController::class,'createUserAddress']);
    Route::post('updateUserAddress/{id}',[UserDetailsController::class,'updateUserAddress']);
    Route::delete('deleteUserAddress/{id}',[UserDetailsController::class,'deleteUserAddress']);
    Route::get('getUserAddress',[UserDetailsController::class,'getUserAddress']);

    //User Activity
    Route::post('createUserActivity',[UserDetailsController::class,'createUserActivity']);
    Route::get('recentlyViewProducts',[UserDetailsController::class,'recentlyViewProducts']);

    //wallet
    Route::get('getWalletTransaction',[UserDetailsController::class,'getWalletTransaction']);
    Route::get('getWalletBalance',[UserDetailsController::class,'getWalletBalance']);
    Route::get('addWalletBalance',[UserDetailsController::class,'addWalletBalance']);

    //delivery available pincode
    Route::post('create',[DeliveryPincodeController::class,'create']);
    Route::post('update/{id}',[DeliveryPincodeController::class,'update']);
    Route::get('checkDeliveryAvailable',[DeliveryPincodeController::class,'checkDeliveryAvailable']);
    Route::delete('delete/{id}',[DeliveryPincodeController::class,'checkDeliveryAvailable']);

});

Route::group(['middleware' => [],'prefix' => 'auth','as' => 'auth.'], function () {
    Route::post('customerRegistration',[AuthController::class,'customerRegistration']);
    Route::post('customerLoginWithOtp',[AuthController::class,'customerLoginWithOtp']);
    Route::post('verifyMobileOtp',[AuthController::class,'verifyMobileOtp']);
    Route::post('resendOtp',[AuthController::class,'resendOtp']);
    Route::post('verifyEmailOtp',[AuthController::class,'verifyEmailOtp']);
    Route::post('resendEmailOtp',[AuthController::class,'resendEmailOtp']);
    Route::post('loginWithEmailPassword',[AuthController::class,'loginWithEmailPassword']);
    Route::post('loginWithMobileNoPassword',[AuthController::class,'loginWithMobileNoPassword']);
    Route::post('customerRegistrationWithImei',[AuthController::class,'customerRegistrationWithImei']);
    Route::post('customerRegistrationWithMobileOnly',[AuthController::class,'customerRegistrationWithMobileOnly']);
});

Route::group(['middleware' => ['jwt.verify','throttle:60,1'],'prefix' => 'frontend','as' => 'frontend.'], function () {
    Route::get('getCategory',[CategoryController::class,'getCategory']);
    Route::get('getProducts',[ProductController::class,'getProduct']);
    Route::get('getProdcutDescription',[ProductController::class,'getProductDescription']);
    Route::get('getProductVariable',[ProductController::class,'getProductVariable']);
    Route::get('getProductImages',[ProductController::class,'getProductImages']);
    Route::get('getProductReview',[ProductController::class,'getProductReview']);
    Route::get('getSingleProductInfo/{id}',[ProductController::class,'getSingleProductInfo']);
    Route::get('getNewProducts',[ProductController::class,'getNewProducts']);
    Route::get('checkTokenExpiry',[AuthController::class,'checkToken']);
    Route::get('getSizeColorData',[ProductController::class,'getSizeColorData']);
    Route::get('searchProductByName',[ProductController::class,'searchProductByName']);
    Route::get('getRandomProducts',[ProductController::class,'getRandomProducts']);
    Route::get('getRecommendedProducts',[ProductController::class,'getRecommendedProducts']);
    Route::get('getSingleChildProducts',[ProductController::class,'getSingleChildProducts']);


});

Route::group(['middleware' => ['throttle:60,1'],'prefix' => 'payment','as' => 'payment.'], function () {
    Route::post('paytmOrderPaymentWebhookCallback',[OrderController::class,'paytmOrderPaymentWebhookCallback'])->name('paytmOrderPaymentWebhookCallback');
    Route::post('paytmGiftCardFeesCallback',[GiftCardController::class,'paytmGiftCardFeesCallback'])->name('paytmGiftCardFeesCallback');
});

Route::group(['middleware' => ['jwt.verify','throttle:60,1'],'prefix' => 'order','as' => 'order.'], function () {
    Route::post('cart',[OrderController::class,'cart']);
    Route::post('checkout',[OrderController::class,'checkout']);
    Route::post('placeOrder',[OrderController::class,'placeOrder']);
    Route::get('getAvailablePromocodes',[OrderController::class,'getAvailablePromocodes']);
    Route::get('getMyOrders',[OrderController::class,'getMyOrders']);
    Route::post('addToBag',[OrderController::class,'addToCart']);
    Route::get('getBagItems',[OrderController::class,'getBagItems']);
    Route::get('getCartItems',[OrderController::class,'getCartItems']);
    Route::post('checkOrderRazorpayPaymentStatus',[OrderController::class,'checkOrderRazorpayPaymentStatus']);
    Route::post('checkOrderPaytmPaymentStatus',[OrderController::class,'checkOrderPaytmPaymentStatus']);
    Route::get('getSingleOrder/{id}',[OrderController::class,'getSingleOrder']);
    Route::get('getMyPaidOrders',[OrderController::class,'getMyPaidOrders']);

    Route::get('getGiftCards',[GiftCardController::class,'getGiftCards']);
    Route::post('buyGiftCard',[GiftCardController::class,'buyGiftCard']);
    Route::post('checkRazorpayPaymentStatus',[GiftCardController::class,'checkRazorpayPaymentStatus']);
    Route::post('checkPaytmPaymentStatus',[GiftCardController::class,'checkPaytmPaymentStatus']);
    Route::get('getMyCouponCode',[GiftCardController::class,'getMyCouponCode']);
    Route::get('getGiftCardPurchasedByMe',[GiftCardController::class,'getGiftCardPurchasedByMe']);
    Route::post('sendGiftCardVerificationOTP',[GiftCardController::class,'sendGiftCardVerificationOTP']);
    Route::post('verifyCouponOtp',[GiftCardController::class,'verifyCouponOtp']);

});
