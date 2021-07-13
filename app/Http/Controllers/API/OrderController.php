<?php
namespace App\Http\Controllers\API;



use App\Models\Cart;
use App\Models\Category;
use App\Models\DeliveryPincode;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Products;
use App\Models\ProductVariables;
use App\Models\Promocode;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserAddress;
use App\Models\UserCart;
use App\Models\UserGiftCards;
use App\Models\UserWhishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use paytm\paytmchecksum\PaytmChecksum;
use Razorpay\Api\Api;
use Validator;
use Auth;
use function PHPUnit\Framework\isNan;

class OrderController extends BaseController{

    public function cart(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'products_list' => 'array',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $response = [];
            if($request->has('products_list') && count($request->products_list)>=0){
                $productsList = $request->products_list;
                $cartDelete = Cart::where('user_id',Auth::user()->id)->delete();
                $cart_records = [];
                $subTotal = 0;
                foreach ($productsList as $productVariable){
                    if(!empty($productVariable)) {
                        $productVariableofDb = ProductVariables::find($productVariable['product_variable_id']);
                        if(!is_null($productVariableofDb)){
                            if($productVariableofDb['is_on_sale']){
                                $subTotal += $productVariableofDb['sale_price']*$productVariable['customer_qty'];
                            }else{
                                $subTotal += $productVariableofDb['price']*$productVariable['customer_qty'];
                            }
                            $now = Carbon::now();
                            $cart_records[] = [
                               'product_variable_id' => $productVariable['product_variable_id'],
                               'customer_qty'=> $productVariable['customer_qty'],
                               'user_id' => Auth::user()->id,
                               'updated_at' => $now,  // remove if not using timestamps
                               'created_at' => $now   // remove if not using timestamps
                            ];
                         }

                    }


                }
                Cart::insert($cart_records);
                $response['subtotal']=$subTotal;
            }

            return $this->sendResponse($response,'Data Updated Successfully', true);
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getAvailablePromocodes(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $promocodes = Promocode::where('is_active',1)->where('end_on', '>=', Carbon::now())
                ->where('start_from', '<=', Carbon::now())->get()->toArray();
            if(count($promocodes)>0){
                return $this->sendResponse($promocodes,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Promo codes available', false);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function checkout(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'products_list' => 'required|array',
                'address_id'=>'required|numeric',
                'use_wallet_balance'=>'required|boolean',
                'promocode'=>'string',
                'gift_card_code'=>'string'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $response = [];
            $user = Auth::user();
            $response['walletBalance'] = (float)$user->balance();
            $response['walletBalanceUsed'] = 0;
            $msg = '';
            $discountAmount = 0;
            $address = UserAddress::whereUserId(Auth::user()->id)->whereId($request->address_id)->first();

            if(is_null($address)){
                return $this->sendError('No Address For Current User With Id '.$request->address_id, [],211);
            }
            $delivryPincode = DeliveryPincode::where('pincode',$address['pincode'])->first();
            if(is_null($delivryPincode)){
                return $this->sendError('Not deliverable in this pincode '.$address['pincode'], [],213);
            }
//            if($request->has("promocode") && $request->use_wallet_balance){
//                return $this->sendError('Promo code and Wallet Balance Can\'t be used at one time. Either use promo code or wallet balance' , [],200);
//            }
            $promocode = null;
            if($request->has('promocode')) {
                $promocode = Promocode::wherePromocode($request->promocode)
                    ->where('end_on', '>=', Carbon::now())
                    ->where('start_from', '<=', Carbon::now())
                    ->first();
                if (is_null($promocode)) {
                    return $this->sendError('Invalid Promo code', [], 212);
                }
                $orders = Order::where('user_id',Auth::user()->id)->where('promo_id',$promocode->id)->get();
                if(count($orders)>0){
                    return $this->sendError('Promo code Already Used', [], 220);
                }
                if($promocode->is_for_new_user){
                    $orders = Order::where('user_id',Auth::user()->id)->get();
                    if(count($orders)>0){
                        return $this->sendError('Promo code is for new user only', [], 221);
                    }
                }

            }
            if($request->has('gift_card_code')){
                $now = Carbon::now();
                $couponCode = base64_encode($request->gift_card_code);
//                dd($couponCode);
                $userGiftCardCode = UserGiftCards::where('coupon_code',$couponCode)->where('use_status',0)
                    ->where('payment_status',1)
                    ->where('expiry_date','>',$now)
//                    ->where('otp_verified_at','<',Carbon::now()->subHours(1))
                    ->first();
                if(is_null($userGiftCardCode)){
                    return $this->sendError('Invalid Gift Card code', [], 215);
                }else{
                    $start_date = new \DateTime();
                    $since_start = $start_date->diff(new \DateTime($userGiftCardCode->otp_verified_at));
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    if($minutes>60){
                        return $this->sendError('Gift Card OTP Not Verified', [], 216);
                    }
                }

            }

            if($request->has('products_list') && count($request->products_list)>0){
                $productsList = $request->products_list;
                $cartDelete = Cart::where('user_id',Auth::user()->id)->delete();
                $cart_records = [];
                $subTotal = 0;
                $outOfStockItemId = [];
                foreach ($productsList as $productVariable){
                    if(!empty($productVariable)) {
                        $productVariableofDb = ProductVariables::find($productVariable['product_variable_id']);
                        if(!is_null($productVariableofDb)){
                            if($productVariableofDb['quantity']<$productVariable['customer_qty']){
                                array_push($outOfStockItemId,$productVariableofDb['id']);
                            }else{
                                if($productVariableofDb['is_on_sale']){
                                    $subTotal += $productVariableofDb['sale_price']*$productVariable['customer_qty'];
                                }else{
                                    $subTotal += $productVariableofDb['price']*$productVariable['customer_qty'];
                                }
                                $now = Carbon::now();
                                $cart_records[] = [
                                    'product_variable_id' => $productVariable['product_variable_id'],
                                    'customer_qty'=> $productVariable['customer_qty'],
                                    'user_id' => Auth::user()->id,
                                    'updated_at' => $now,  // remove if not using timestamps
                                    'created_at' => $now   // remove if not using timestamps
                                ];
                            }
                        }
                    }
                }
                Cart::insert($cart_records);
                if(count($outOfStockItemId)>0){
                    return $this->sendError('Some Items Are Out Of Stock', ["outOfStockItemIds"=>$outOfStockItemId], 217);
                }

                $tempSubTotal = $subTotal;
                $remainingAmountToBePaid = $subTotal;
                $giftCardAmountUtilized = 0;
                $totalGiftCardValue = 0;
                $giftCardAmountRemaining = 0;
                if($subTotal>0){

                    if($request->has('gift_card_code') && !is_null($userGiftCardCode)){
                        $totalGiftCardValue = $userGiftCardCode['gift_amount'];
                        if($remainingAmountToBePaid<$userGiftCardCode['gift_amount']){

                            $giftCardAmountUtilized = $userGiftCardCode['gift_amount'] - $remainingAmountToBePaid;
                            $giftCardAmountRemaining = $userGiftCardCode['gift_amount'] - $giftCardAmountUtilized;
                            $remainingAmountToBePaid = 0;

                        }else{
                            $remainingAmountToBePaid = $remainingAmountToBePaid - $userGiftCardCode['gift_amount'] ;
                        }
                    }

                    if($request->has("use_wallet_balance") && $request->use_wallet_balance){
                        if($remainingAmountToBePaid>0){
                            if($response['walletBalance']<$remainingAmountToBePaid){
                                $response['walletBalanceUsed'] = $response['walletBalance'];
                                $remainingAmountToBePaid = $remainingAmountToBePaid - $response['walletBalance'];
                                $response['walletBalanceRemaining'] = 0;
                            }else{
                                $response['walletBalanceRemaining'] = $response['walletBalance'] - $remainingAmountToBePaid;
                                $response['walletBalanceUsed'] = $remainingAmountToBePaid;
                                $remainingAmountToBePaid = 0;

                            }
                        }
                    }
                    if(!is_null($promocode)){
                        $isPromoCodeAllowedOrNot= true;
                        if($promocode['is_for_new_user']){
                            $orders = Order::where('user_id',Auth::user()->id)->get();

                            if(count($orders)>=1){
                                $isPromoCodeAllowedOrNot = false;
                            }
                        }
                        if($isPromoCodeAllowedOrNot){
                            if($remainingAmountToBePaid>=$promocode['minimal_cart_total']){
                                if($promocode['type']=='percentage'){
                                    $discountAmount  =  round(($remainingAmountToBePaid*$promocode['discount'])/100,2) ;
                                    if($discountAmount>$promocode['max_discount']){
                                        $discountAmount = $promocode['max_discount'];
                                    }
                                }
                                else if($promocode['type']=='flat'){
                                    $discountAmount = $promocode['discount'];
                                    if($discountAmount>$promocode['max_discount']){
                                        $discountAmount = $promocode['max_discount'];
                                    }
                                }
                            }else{
                                $msg = 'Minimum Cart Amount Should Be '.$promocode['minimal_cart_total'];
                                return $this->sendError($msg, [], 222);
                            }
                        }
                    }
                }

                $response['shippingCharges']=10;
                $shippingCharges = $response['shippingCharges'];
                $response['discountAmount'] = (float)$discountAmount;
                $response['subtotal']=(float)$subTotal;
                if($shippingCharges<$giftCardAmountRemaining){
                    $giftCardAmountRemaining = $giftCardAmountRemaining -$shippingCharges;
                    $shippingCharges=0;
                }else{
                    $giftCardAmountRemaining = 0;
                    $shippingCharges = $shippingCharges -$giftCardAmountRemaining;
                }
                $response['giftCardAmountRemaining']= $giftCardAmountRemaining;
                $response['totalGiftCardValue']=$totalGiftCardValue;
                $response['total']= (float)($remainingAmountToBePaid+$shippingCharges - $discountAmount);
                $response['pointsEarned']=round($response['total']*0.1,0);
                $response['giftCardBalanceUsed'] = $totalGiftCardValue - $giftCardAmountRemaining;
            }
            return $this->sendResponse($response,$msg==''?'Data Updated Successfully':$msg, true);
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function placeOrder(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'products_list' => 'required|array',
                'address_id'=>'required|numeric',
                'use_wallet_balance'=>'required|boolean',
                'promocode'=>'string',
                'gift_card_code'=>'string',
                'paymentMode'=>'required|numeric|min:0|max:2'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $response = [];
            $user = Auth::user();
            $response['walletBalance'] = (float)$user->balance();
            $response['walletBalanceUsed'] = 0;
            $msg = '';
            $discountAmount = 0;
            $is_promocode_applied = false;
            $is_gift_coupon_applied = false;
            $is_wallet_applied = false;
            $address = UserAddress::whereUserId(Auth::user()->id)->whereId($request->address_id)->first();
            if(is_null($address)){
                return $this->sendError('No Address For Current User With Id '.$request->address_id, [],211);
            }
            $delivryPincode = DeliveryPincode::where('pincode',$address['pincode'])->first();
            if(is_null($delivryPincode)){
                return $this->sendError('Not deliverable in this pincode '.$address['pincode'], [],213);
            }
            $promocode = null;
            if($request->has('promocode')) {
                $promocode = Promocode::wherePromocode($request->promocode)
                    ->where('end_on', '>=', Carbon::now())
                    ->where('start_from', '<=', Carbon::now())
                    ->first();
                if (is_null($promocode)) {
                    return $this->sendError('Invalid Promo code', [], 212);
                }
                $orders = Order::where('user_id',Auth::user()->id)->where('promo_id',$promocode->id)->get();
                if(count($orders)>0){
                    return $this->sendError('Promo code Already Used', [], 220);
                }
                if($promocode->is_for_new_user){
                    $orders = Order::where('user_id',Auth::user()->id)->get();
                    if(count($orders)>0){
                        return $this->sendError('Promo code is for new user only', [], 221);
                    }
                }
            }
            if($request->has('gift_card_code')){
                $now = Carbon::now();
                $couponCode = base64_encode($request->gift_card_code);
                $userGiftCardCode = UserGiftCards::where('coupon_code',$couponCode)->where('use_status',0)
                    ->where('payment_status',1)
                    ->where('expiry_date','>',$now)
//                    ->where('otp_verified_at','<',Carbon::now()->subHours(1))
                    ->first();
                if(is_null($userGiftCardCode)){
                    return $this->sendError('Invalid Gift Card code', [], 215);
                }else{
                    $start_date = new \DateTime();
                    $since_start = $start_date->diff(new \DateTime($userGiftCardCode->otp_verified_at));
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    if($minutes>60){
                        return $this->sendError('Gift Card OTP Not Verified', [], 216);
                    }
                }
            }
            if($request->has('products_list') && count($request->products_list)>0){
                $productsList = $request->products_list;
                $cart_records = [];
                $subTotal = 0;$outOfStockItemId = [];
                foreach ($productsList as $productVariable){
                    if(!empty($productVariable)) {
                        $productVariableofDb = ProductVariables::find($productVariable['product_variable_id']);
                        if(!is_null($productVariableofDb)){
                            if($productVariableofDb['quantity']<$productVariable['customer_qty']){
                                array_push($outOfStockItemId,$productVariableofDb['id']);
                            }else{
                                if($productVariableofDb['is_on_sale']){
                                    $subTotal += $productVariableofDb['sale_price']*$productVariable['customer_qty'];
                                }else{
                                    $subTotal += $productVariableofDb['price']*$productVariable['customer_qty'];
                                }
                                $now = Carbon::now();
                                $cart_records[] = [
                                    'product_variable_id' => $productVariable['product_variable_id'],
                                    'customer_qty'=> $productVariable['customer_qty'],
                                    'user_id' => Auth::user()->id,
                                    'updated_at' => $now,  // remove if not using timestamps
                                    'created_at' => $now   // remove if not using timestamps
                                ];
                            }
                        }
                    }
                }
                if(count($outOfStockItemId)>0){
                    return $this->sendError('Some Items Are Out Of Stock', ["outOfStockItemIds"=>$outOfStockItemId], 217);
                }
                $tempSubTotal = $subTotal;
                $remainingAmountToBePaid = $subTotal;
                $giftCardAmountUtilized = 0;
                $totalGiftCardValue = 0;
                $giftCardAmountRemaining = 0;
                if($subTotal>0){

                    if($request->has('gift_card_code') && !is_null($userGiftCardCode)){
                        $totalGiftCardValue = $userGiftCardCode['gift_amount'];

                        if($remainingAmountToBePaid<$userGiftCardCode['gift_amount']){

                            $giftCardAmountUtilized = $userGiftCardCode['gift_amount'] - $remainingAmountToBePaid;
                            $giftCardAmountRemaining = $userGiftCardCode['gift_amount'] - $giftCardAmountUtilized;
                            $remainingAmountToBePaid = 0;
                            $is_gift_coupon_applied = true;
                        }else{
                            $remainingAmountToBePaid = $remainingAmountToBePaid - $userGiftCardCode['gift_amount'] ;
                            $is_gift_coupon_applied = true;
                        }
                    }

                    if($request->has("use_wallet_balance") && $request->use_wallet_balance){
                        if($remainingAmountToBePaid>0){
                            if($response['walletBalance']<$remainingAmountToBePaid){
                                $response['walletBalanceUsed'] = $response['walletBalance'];
                                $remainingAmountToBePaid = $remainingAmountToBePaid - $response['walletBalance'];
                                $response['walletBalanceRemaining'] = 0;
                            }else{
                                $response['walletBalanceRemaining'] = $response['walletBalance'] - $remainingAmountToBePaid;
                                $response['walletBalanceUsed'] = $remainingAmountToBePaid;
                                $remainingAmountToBePaid = 0;
                            }
                            $is_wallet_applied = true;
                        }
                    }
                    if(!is_null($promocode)){
                        $isPromoCodeAllowedOrNot= true;
                        if($promocode['is_for_new_user']){
                            $orders = Order::where('user_id',Auth::user()->id)->get();

                            if(count($orders)>=1){
                                $isPromoCodeAllowedOrNot = false;
                            }
                        }
                        if($isPromoCodeAllowedOrNot){
                            if($remainingAmountToBePaid>=$promocode['minimal_cart_total']){
                                if($promocode['type']=='percentage'){
                                    $discountAmount  =  round(($remainingAmountToBePaid*$promocode['discount'])/100,2) ;
                                    if($discountAmount>$promocode['max_discount']){
                                        $discountAmount = $promocode['max_discount'];
                                    }
                                }
                                else if($promocode['type']=='flat'){
                                    $discountAmount = $promocode['discount'];
                                    if($discountAmount>$promocode['max_discount']){
                                        $discountAmount = $promocode['max_discount'];
                                    }
                                }
                                if($discountAmount>0){
                                    $is_promocode_applied = true;
                                }
                            }else{
                                $msg = 'Minimum Cart Amount Should Be '.$promocode['minimal_cart_total'];
                                return $this->sendError($msg, [], 222);
                            }
                        }
                    }
                }
                $response['shippingCharges']=10;
                $shippingCharges = $response['shippingCharges'];
                $response['discountAmount'] = (float)$discountAmount;
                $response['subtotal']=(float)$subTotal;
                if($shippingCharges<$giftCardAmountRemaining){
                    $giftCardAmountRemaining = $giftCardAmountRemaining -$shippingCharges;
                    $shippingCharges=0;
                }else{
                    $giftCardAmountRemaining = 0;
                    $shippingCharges = $shippingCharges -$giftCardAmountRemaining;
                }
                $response['giftCardAmountRemaining']= $giftCardAmountRemaining;
                $response['totalGiftCardValue']=$totalGiftCardValue;
                $response['total']= (float)($remainingAmountToBePaid+$shippingCharges - $discountAmount);
                $response['pointsEarned']=round($response['total']*0.1,0);
                $response['giftCardBalanceUsed'] = $totalGiftCardValue - $giftCardAmountRemaining;
                $response['isFullPaymentDone']=false;
                $newOrder = new Order;
                $newOrder->user_id = $user->id;
                $newOrder->address_id = $request->address_id;
                $newOrder->orderRefNo = time();
                if($is_promocode_applied){
                    $newOrder->is_promo_code = true;
                    $newOrder->promo_id = $promocode['id'];
                    $newOrder->promo_discount = (float)$discountAmount;
                }

                if($is_wallet_applied){
                    $newOrder->is_wallet_balance_used = true;
                    $newOrder->wallet_balance_used = $response['walletBalanceUsed'];
                }
                if($is_gift_coupon_applied){
                    $newOrder->is_gift_coupon_used = true;
                    $newOrder->gift_card_id = $userGiftCardCode['id'];
                    $newOrder->gift_card_amount_used = $userGiftCardCode['gift_amount'] - $giftCardAmountRemaining;
                }
                $newOrder->subTotal = $response['subtotal'];
                $newOrder->total = $response['total'];
                $newOrder->shipping_charge = $response['shippingCharges'];
                switch ($request->paymentMode) {
                    case 0:
                        $newOrder->paymentMode = 'Paytm';
                    break;
                    case 1:
                        $newOrder->paymentMode = 'Razorpay';
                        break;
                    case 1:
                        $newOrder->paymentMode = 'COD';
                        break;
                }
                $newOrder->order_status = 1;
                $newOrder->payment_status = 1;
                if($response['total']<=0){
                    $newOrder->payment_status = 2;
                    $response['isFullPaymentDone']=true;
                }
                if($newOrder->save()){
                    if($is_gift_coupon_applied){
                        $userGiftCardCode->use_status = 1;
                        $userGiftCardCode->save();
                    }
                    if($is_wallet_applied && $response['walletBalanceUsed']>0) {
                        $user = Auth::user();
                        $data = ['type'  =>  'debit',
                            'amount' => $response['walletBalanceUsed'],
                            'description' =>  "Wallet Balance Used For Order With Reference No. ".$newOrder->orderRefNo,
                            'status' => 1,
                        ];
                        $wallet = $user->transactions()
                            ->create($data);
                    }
                    $payment_mode_details = [];
                    if($newOrder->paymentMode=='Paytm'){
                        $payment_mode_details = $this->generateOrderPaytm($newOrder->total,$newOrder->id);
                    }
                    if($newOrder->paymentMode=='Razorpay'){
                        $payment_mode_details = $this->generateOrderRazorpay($newOrder->total,$newOrder->id);
                    }
                    if(count($payment_mode_details)<=0){
                        return $this->sendError('Payment Gateway Error', [],219);
                    }else{
                        $response['payment_gatway_details'] = $payment_mode_details;
                        $newOrder->gateway_transaction_id = $payment_mode_details['txnToken'];
                        $newOrder->save();
                    }


                    foreach ($productsList as $productVariable){
                        if(!empty($productVariable)) {
                            $productVariableofDb = ProductVariables::find($productVariable['product_variable_id']);
                            if(!is_null($productVariableofDb)){
                                $newOrderItem = new OrderItems;
                                $newOrderItem->order_id = $newOrder->id;
                                $newOrderItem->product_id = $productVariableofDb['product_id'];
                                $newOrderItem->product_variable_id = $productVariableofDb['id'];
                                if($productVariableofDb['is_on_sale']){
                                    $newOrderItem->selling_price = $productVariableofDb['sale_price'];
                                }else{
                                    $newOrderItem->selling_price = $productVariableofDb['price'];
                                }
                                $newOrderItem->quantity = $productVariable['customer_qty'];
                                $productVariableofDb->decrement('quantity', $productVariable['customer_qty']);
                                $newOrderItem->save();
                            }
                        }
                    }
                    $response['orderDetails'] = Order::with(['orderStatus','paymentStatus','orderItems','orderItems.productVariable','orderItems.productVariable.productDetails','addressDetails'])->find($newOrder->id);
                    return $this->sendResponse($response,$msg==''?'Data Updated Successfully':$msg, true);
                }else{
                    return $this->sendResponse([],$msg==''?'Order Not Created':$msg, false);
                }
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getMyOrders(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = (int)$request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $orders = Order::with(['orderStatus','paymentStatus','orderItems','orderItems.productVariable','orderItems.productVariable.productDetails','addressDetails'])->whereUserId(Auth::user()->id)->skip($skip)->limit($limit)->orderBy('id','DESC')->get()->toArray();
            if(count($orders)>0){
                return $this->sendResponse($orders,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Orders Available available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function addToCart(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'products_list' => 'array',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $response = [];
            $user = Auth::user();

            if($request->has('products_list') && count($request->products_list)>0){
                $productsList = $request->products_list;
                $cartDelete = UserCart::where('user_id',Auth::user()->id)->delete();
                $cart_records = [];
                $subTotal = 0;
                foreach ($productsList as $productVariable){
                    if(!empty($productVariable)) {
                        $productVariableofDb = ProductVariables::find($productVariable['product_variable_id']);
                        if(!is_null($productVariableofDb)){
                            if($productVariableofDb['is_on_sale']){
                                $subTotal += $productVariableofDb['sale_price']*$productVariable['customer_qty'];
                            }else{
                                $subTotal += $productVariableofDb['price']*$productVariable['customer_qty'];
                            }
                            $now = Carbon::now();
                            $cart_records[] = [
                                'product_variable_id' => $productVariable['product_variable_id'],
                                'quantity'=> $productVariable['customer_qty'],
                                'user_id' => Auth::user()->id,
                                'product_id'=>$productVariableofDb['product_id'],
                                'updated_at' => $now,  // remove if not using timestamps
                                'created_at' => $now   // remove if not using timestamps
                            ];
                        }
                    }


                }
                UserCart::insert($cart_records);
            }
            return $this->sendResponse($response,'Data Updated Successfully', true);
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getBagItems(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = (int)$request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $user =  Auth::user();
            $itemsProductVariableId = UserCart::where('user_id',$user->id)->pluck('product_variable_id');
            $itemsProductId = UserCart::where('user_id',$user->id)->pluck('product_id');
            $items = ProductVariables::whereIn('id',$itemsProductVariableId)->with('product', function ($query) use($itemsProductId){
                $query->whereIn('id',$itemsProductId);
            })->skip($skip)->limit($limit)->get();
            if(count($items)>0){
                return $this->sendResponse($items,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Orders Available available', false);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getCartItems(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user =  Auth::user();

            $items = Cart::where('user_id',$user->id)->with(['productVariable','productVariable.product'])->get();
//            dd(Cart::where('user_id',$user->id)->with(['productVariable','productVariable.product'])->toSql());
            $subTotal = 0;
            foreach ($items as $item){
                if($item['productVariable']['is_on_sale']){
                    $subTotal += $item['productVariable']['sale_price']*$item['customer_qty'];
                }else{
                    $subTotal += $item['productVariable']['price']*$item['customer_qty'];
                }
            }
            if(count($items)>0){
                $response = ['items'=>$items,'cartTotal'=>$subTotal];
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Orders Available', false);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    function generateOrderPaytm($amount,$orderId){
        $paytmParams = array();

        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            "mid"           => env("PAYTM_MERCHANT_ID"),
            "websiteName"   => "WEBSTAGING",
            "orderId"       => 'order_'.$orderId,
            "callbackUrl"   => "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=order_".$orderId,
            "txnAmount"     => array(
                "value"     => $amount,
                "currency"  => "INR",
            ),
            "userInfo"      => array(
                "custId"    => Auth::user()->id,
                "custName"  => Auth::user()->name
            ),
        );

        /*
        * Generate checksum by parameters we have in body
        * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
        */
        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), env("PAYTM_MERCHANT_KEY"));

        $paytmParams["head"] = array(
            "signature"    => $checksum
        );
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        if(env('PAYTM_ENVIRONMENT')=='local'){
            $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=".env("PAYTM_MERCHANT_ID")."&orderId=".'order_'.$orderId;
        }else{
            $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=".env("PAYTM_MERCHANT_ID")."&orderId=".'order_'.$orderId;
        }


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $curlResponse = json_decode(curl_exec($ch),true);

        $response =[];
        if($curlResponse['body']['resultInfo']['resultStatus'] == "S"){
            $response['mid']=env('PAYTM_MERCHANT_ID');
            $response['orderId']='order_'.$orderId;
            $response['system_order_id']=$orderId;
            $response['amount']=$amount;
            $response['txnToken']=$curlResponse['body']['txnToken'];
            $response['callbackURL']=route('payment.paytmOrderPaymentWebhookCallback');
            $response['isStaging']= env('PAYTM_ENVIRONMENT')=='local'?true:false;
        }
        return $response;
    }

    function generateOrderRazorpay($amount,$orderId){
        $api = new Api(env('R_API_KEY'), env('R_API_SECRET'));
        $razorOrder  = $api->order->create(array('receipt' => 'order_'.$orderId, 'amount' => intval($amount*100), 'currency' => 'INR',
            'payment_capture'=>'1')); // Creates order
        $rzorderId = $razorOrder['id'];
        $response = [];
        if(!is_null($razorOrder['id'])){
            $response['mid']=env('R_API_KEY');
            $response['orderId']='order_'.$orderId;
            $response['system_order_id']=$orderId;
            $response['amount']=$amount;
            $response['txnToken']=$rzorderId;
            $response['isStaging']= env('PAYTM_ENVIRONMENT')=='local'?true:false;
        }

        return $response;
    }

    public function paytmOrderPaymentWebhookCallback(Request $request){
        try {
            $order_id = explode("_", $request->ORDERID);
            $order_id = end($order_id);
            $order = Order::find($order_id);
            $user = User::find($order->user_id);
            if($request->STATUS=='TXN_SUCCESS'){
                $order->payment_status=2;
                $order->save();
                $response = [];
                $response['payment_status']=true;
                return  $this->sendResponse($response,'Payment Successful',true);
            }else{
                $response=[];
                $response['payment_status']=false;
                return $this->sendResponse($response,'Payment Failed', true);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e->getMessage(),413);
        }
    }

    public function checkOrderRazorpayPaymentStatus(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'payment_gateway_order_id'=>'required',
                'system_id'=>'required|numeric'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = Auth::user();
            if(!$user){
                return $this->sendError('No User Found. Something Went Wrong', ['error'=>"No User Found"]);
            }
            $order = Order::whereId($request->system_id)->where('gateway_transaction_id',$request->payment_gateway_order_id)->first();
            if(is_null($order)){
                return $this->sendError('No Order Found', ['error'=>"No Order Found"],200);
            }
            if($order->payment_status==2){
                $response = [];
                $response['payment_status']=true;
                return $this->sendResponse($response,'Payment Already Done. Please Contact admin in case of any issue', false);
            }
            $api = new Api(env('R_API_KEY'), env('R_API_SECRET'));
            $razorpay_order = $api->order->fetch($request->payment_gateway_order_id);
            if($razorpay_order->status == 'paid'){
                $order->payment_status=2;
                $order->save();
                $user = User::find($order->user_id);
                $data = ['type'  =>  'credit',
                    'amount' => round($order->total*0.1,0),
                    'description' =>  "Points Earned For Order With Reference No. ".$order->orderRefNo,
                    'status' => 1,
                ];
                $wallet = $user->transactions()
                    ->create($data);
                $response = [];
                $response['orderDetails'] = Order::with(['orderStatus','paymentStatus','orderItems','addressDetails'])->find($order->id);
                $response['payment_status']=true;
                return  $this->sendResponse($response,'Payment Successful');
            }
            else{
                $response = [];
                $response['orderDetails'] = Order::with(['orderStatus','paymentStatus','orderItems','addressDetails'])->find($order->id);
                $response['payment_status']=false;
                if($order->payment_status==1){
                    $order->payment_status = 3;
                    $order->save();
                    $orderItems = OrderItems::where('order_id',$request->system_id)->get();
                    foreach ($orderItems as $orderItem){
                        $prodcutVariable = ProductVariables::where('id',$orderItem['product_variable_id'])->increment('quantity',$orderItem['quantity']);
                    }
                    if($order->is_gift_coupon_used){
                        $userGiftCard = UserGiftCards::find($order->gift_card_id );
                        $userGiftCard->use_status = 0;
                        $userGiftCard->save();
                    }
                    if($order->is_wallet_balance_used){
                        $user = User::find($order->user_id);
                        $data = ['type'  =>  'credit',
                            'amount' => $order->wallet_balance_used,
                            'description' =>  "Wallet Balance Used For Order With Reference No. ".$order->orderRefNo,
                            'status' => 1,
                        ];
                        $wallet = $user->transactions()
                            ->create($data);

                    }
                }

                return $this->sendResponse($response,'Payment Failed. Please contact admin in case your payment deducted.', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e->getMessage(),413);
        }
    }

    public function checkOrderPaytmPaymentStatus(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'payment_gateway_order_id'=>'required',
                'system_id'=>'required|numeric'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = Auth::user();
            if(!$user){
                return $this->sendError('No User Found. Something Went Wrong', ['error'=>"No User Found"]);
            }
            $order= Order::where('id','=',$request->system_id)->first();

            if(is_null($order)){
                return $this->sendError('No Order Found. Something Went Wrong', ['error'=>"No Order Found"],200);
            }
            if($order->payment_status==2){
                $response = [];
                $response['payment_status']=true;
                return $this->sendResponse($response,'Payment Already Done. Please Contact admin in case of any issue', false);
            }

            $paytmParams = array();

            /* body parameters */
            $paytmParams["body"] = array(

                /* Find your MID in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
                "mid" => env("PAYTM_MERCHANT_ID"),

                /* Enter your order id which needs to be check status for */
                "orderId" => "order_".$request->system_id,
            );

            /**
             * Generate checksum by parameters we have in body
             * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
             */
            $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), env("PAYTM_MERCHANT_KEY"));

            /* head parameters */
            $paytmParams["head"] = array(

                /* put generated checksum value here */
                "signature"	=> $checksum
            );

            /* prepare JSON string for request */
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

            if(env('PAYTM_ENVIRONMENT')=='local'){
                $url = "https://securegw-stage.paytm.in/v3/order/status";
            }else{
                $url = "https://securegw.paytm.in/v3/order/status";
            }

            /* for Staging */
//            $url = "https://securegw-stage.paytm.in/v3/order/status";

            /* for Production */
// $url = "https://securegw.paytm.in/v3/order/status";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            $curlResponse = json_decode(curl_exec($ch),true);;

            if($curlResponse['body']['resultInfo']['resultStatus']  == 'TXN_SUCCESS'){
                $order->payment_status=2;
                $order->save();
                $user = User::find($order->user_id);
                $data = ['type'  =>  'credit',
                    'amount' => round($order->total*0.1,0),
                    'description' =>  "Points Earned For Order With Reference No. ".$order->orderRefNo,
                    'status' => 1,
                ];
                $wallet = $user->transactions()
                    ->create($data);
                $response = [];
                $response['orderDetails'] = Order::with(['orderStatus','paymentStatus','orderItems','addressDetails'])->find($order->id);
                $response['payment_status']=true;
                return  $this->sendResponse($response,'Payment Successful');
            }
            else{
                $response = [];
                $response['orderDetails'] = Order::with(['orderStatus','paymentStatus','orderItems','addressDetails'])->find($order->id);
                $response['payment_status']=false;
                if($order->payment_status==1){
                    $order->payment_status = 3;
                    $order->save();
                    $orderItems = OrderItems::where('order_id',$request->system_id)->get();
                    foreach ($orderItems as $orderItem){
                        $prodcutVariable = ProductVariables::where('id',$orderItem['product_variable_id'])->increment('quantity',$orderItem['quantity']);
                    }
                    if($order->is_gift_coupon_used){
                        $userGiftCard = UserGiftCards::find($order->gift_card_id );
                        $userGiftCard->use_status = 0;
                        $userGiftCard->save();
                    }
                    if($order->is_wallet_balance_used){
                        $user = User::find($order->user_id);
                        $data = ['type'  =>  'credit',
                            'amount' => $order->wallet_balance_used,
                            'description' =>  "Wallet Balance Used Refund For Order With Reference No. ".$order->orderRefNo,
                            'status' => 1,
                        ];
                        $wallet = $user->transactions()
                            ->create($data);

                    }
                }
                return $this->sendResponse($response,'Payment Failed. Please contact admin in case your payment deducted.', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e->getMessage(),413);
        }
    }

    public function getSingleOrder(Request $request,$id){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = (int)$request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $orders = Order::with(['orderStatus','paymentStatus','orderItems','orderItems.productVariable','orderItems.productVariable.productDetails','addressDetails'])->where('id',$id)->whereUserId(Auth::user()->id)->get()->toArray();
            if(count($orders)>0){
                return $this->sendResponse($orders,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Orders Available available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getMyPaidOrders(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
//            $limit = (int)$request->limit;
//            $pageNo = $request->pageNo;
//            $skip = $limit*$pageNo;
            $orders = Order::with(['orderStatus','paymentStatus','orderItems','orderItems.productVariable','orderItems.productVariable.productDetails','addressDetails'])->where('payment_status',2)->whereUserId(Auth::user()->id)->orderBy('id','DESC')->get()->toArray();
            if(count($orders)>0){
                return $this->sendResponse($orders,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Orders Available available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function cancelOrder(Request  $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'order_id'=>'required|numeric',
                'cancellation_reason'=>'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $order = Order::with(['orderStatus','paymentStatus','orderItems'])->where('id',$request->order_id)->where('payment_status',2)->whereUserId(Auth::user()->id)->first();
            if(!is_null($order)){
                if($order->order_status == 1 ){
                    $order->order_status = 4;
                    $order->cancellation_reason = $request->cancellation_reason;
                    $order->cancellation_time = Carbon::now();

                    if($order->save()){
                        $this->initiateRefund($order);
                        return $this->sendResponse($order,'Order Cancelled Successfully And Refund Initiated.', true);
                    }else{
                        return $this->sendResponse($order,'Order Cancellation failed.', true);
                    }
                }else if($order->order_status == 2){
                    $order->order_status = 4;
                    $order->cancellation_reason = $request->cancellation_reason;
                    $order->cancellation_time = Carbon::now();
                    if($order->save()){
                        $this->initiateRefund($order);
                        return $this->sendResponse($order,'Order Cancelled Successfully And Refund Initiated.', true);
                    }else{
                        return $this->sendResponse($order,'Order Cancellation failed.', true);
                    }
                }

            }else{
                return $this->sendResponse([],'No Order Available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }


    function  initiateRefund($order){
        $order->payment_status = 4;
//        dd($order);
        foreach ($order["orderItems"] as $orderItem){
            $prodcutVariable = ProductVariables::where('id',$orderItem['product_variable_id'])->increment('quantity',$orderItem['quantity']);
        }
        if($order->is_gift_coupon_used){
            $userGiftCard = UserGiftCards::find($order->gift_card_id );
            $userGiftCard->use_status = 0;
            $userGiftCard->save();
        }
        if($order->is_wallet_balance_used){
            $user = User::find($order->user_id);
            $data = ['type'  =>  'credit',
                'amount' => $order->wallet_balance_used,
                'description' =>  "Wallet Balance Used Refund For Order With Reference No. ".$order->orderRefNo,
                'status' => 1,
            ];
            $wallet = $user->transactions()
                ->create($data);

        }
        $order->save();
    }

    function fullOrderReturn(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'order_id'=>'required|numeric',
                'return_reason'=>'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $order = Order::with(['orderStatus','paymentStatus','orderItems'])->where('id',$request->order_id)->where('payment_status',2)->whereUserId(Auth::user()->id)->first();
            if(!is_null($order)){
                if($order->order_status == 3 ){

                    if(Carbon::parse($order->delivery_date)->diffInDays(Carbon::now())<=3){
                        $order->order_status = 7;
                        $order->return_replacemnet_type='full';
                        $order->return_replacement_reason = $request->return_reason;
                        $order->return_replacement_requested_at = Carbon::now();
                        if($order->save()){
                            return $this->sendResponse($order,'Order Return Requested  Initiated.', true);
                        }else{
                            return $this->sendResponse($order,'Order Cancellation failed.', false);
                        }
                    }else{
                        return $this->sendResponse($order,'Order Return Can Not Be Proceed as it\'s more than 3 days from delivery', false);
                    }
                }else {
                    return $this->sendResponse($order,'Order Return Can Not Be Proceed ', false);
                }
            }else{
                return $this->sendResponse([],'No Order Available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    function fullOrderReplacement(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'order_id'=>'required|numeric',
                'replacement_reason'=>'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $order = Order::with(['orderStatus','paymentStatus','orderItems'])->where('id',$request->order_id)->where('payment_status',2)->whereUserId(Auth::user()->id)->first();
            if(!is_null($order)){
                if($order->order_status == 3 ){

                    if(Carbon::parse($order->delivery_date)->diffInDays(Carbon::now())<=3){
                        $order->order_status = 8;
                        $order->return_replacemnet_type='full';
                        $order->return_replacement_reason = $request->return_reason;
                        $order->return_replacement_requested_at = Carbon::now();
                        if($order->save()){
                            return $this->sendResponse($order,'Order Return Requested  Initiated.', true);
                        }else{
                            return $this->sendResponse($order,'Order Cancellation failed.', false);
                        }
                    }else{
                        return $this->sendResponse($order,'Order Return Can Not Be Proceed as it\'s more than 3 days from delivery', false);
                    }
                }else {
                    return $this->sendResponse($order,'Order Return Can Not Be Proceed ', false);
                }
            }else{
                return $this->sendResponse([],'No Order Available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function partialOrderReplacement(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

                'order_items_list'=>'required',
                'order_id'=>'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $order = Order::with(['orderStatus','paymentStatus','orderItems'])->where('id',$request->order_id)->where('payment_status',2)->whereUserId(Auth::user()->id)->first();
            if(!is_null($order)){
                if($order->order_status == 3 ){

                    if(Carbon::parse($order->delivery_date)->diffInDays(Carbon::now())<=3){
                        $order->order_status = 8;
                        $order->return_replacemnet_type='partial';

                        $order->return_replacement_requested_at = Carbon::now();
                        if($order->save()){
                            if($request->has('order_items_list') && count($request->order_items_list)>=0){
                                $productsList = $request->order_items_list;
                                foreach ($productsList as $order_item){
                                    if($order_item['item_id'] && $order_item['replacement_reason']!=''){

                                    }else{
                                        return $this->sendError('Validation Error.',['order_items_list'=>'Please Enter All Required Field. replacement_reason, item_id ']);
                                    }

                                }
                                foreach ($productsList as $order_item){
                                    if($order_item['item_id'] && $order_item['replacement_reason']!=''){
                                        $temp_order_item = OrderItems::find($order_item['item_id']);
                                        if(!is_null($temp_order_item)){
                                            $temp_order_item->return_replacement_reason=$order_item['replacement_reason'];
                                            $temp_order_item->return_replacement_requested_at=Carbon::now();
                                            $temp_order_item->replacement_return_status = 'requested';
                                            $temp_order_item->save();
                                        }
                                    }else{
                                        return $this->sendError('Validation Error.',['order_items_list'=>'Please Enter All Required Field. replacement_reason, item_id ']);
                                    }

                                }


                            }
                            return $this->sendResponse($order,'Order Return Requested  Initiated.', true);
                        }else{
                            return $this->sendResponse($order,'Order Cancellation failed.', false);
                        }
                    }else{
                        return $this->sendResponse($order,'Order Return Can Not Be Proceed as it\'s more than 3 days from delivery', false);
                    }
                }else {
                    return $this->sendResponse($order,'Order Return Can Not Be Proceed ', false);
                }
            }else{
                return $this->sendResponse([],'No Order Available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }
    public function partialOrderReturn(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

                'order_items_list'=>'required',
                'order_id'=>'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $order = Order::with(['orderStatus','paymentStatus','orderItems'])->where('id',$request->order_id)->where('payment_status',2)->whereUserId(Auth::user()->id)->first();
            if(!is_null($order)){
                if($order->order_status == 3 ){

                    if(Carbon::parse($order->delivery_date)->diffInDays(Carbon::now())<=3){
                        $order->order_status = 7;
                        $order->return_replacemnet_type='partial';

                        $order->return_replacement_requested_at = Carbon::now();
                        if($order->save()){
                            if($request->has('order_items_list') && count($request->order_items_list)>=0){
                                $productsList = $request->order_items_list;
                                foreach ($productsList as $order_item){
                                    if($order_item['item_id'] && $order_item['replacement_reason']!=''){

                                    }else{
                                        return $this->sendError('Validation Error.',['order_items_list'=>'Please Enter All Required Field. replacement_reason, item_id ']);
                                    }

                                }
                                foreach ($productsList as $order_item){
                                    if($order_item['item_id'] && $order_item['replacement_reason']!=''){
                                        $temp_order_item = OrderItems::find($order_item['item_id']);
                                        if(!is_null($temp_order_item)){
                                            $temp_order_item->return_replacement_reason=$order_item['replacement_reason'];
                                            $temp_order_item->return_replacement_requested_at=Carbon::now();
                                            $temp_order_item->replacement_return_status = 'requested';
                                            $temp_order_item->save();
                                        }
                                    }else{
                                        return $this->sendError('Validation Error.',['order_items_list'=>'Please Enter All Required Field. replacement_reason, item_id ']);
                                    }

                                }


                            }
                            return $this->sendResponse($order,'Order Return Requested  Initiated.', true);
                        }else{
                            return $this->sendResponse($order,'Order Cancellation failed.', false);
                        }
                    }else{
                        return $this->sendResponse($order,'Order Return Can Not Be Proceed as it\'s more than 3 days from delivery', false);
                    }
                }else {
                    return $this->sendResponse($order,'Order Return Can Not Be Proceed ', false);
                }
            }else{
                return $this->sendResponse([],'No Order Available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }
}
