<?php
namespace App\Http\Controllers\API;



use App\Models\Cart;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductVariables;
use App\Models\Promocode;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserAddress;
use App\Models\UserWhishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Auth;

class OrderController extends BaseController{

    public function cart(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'products_list' => 'required|array',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $response = [];
            if($request->has('products_list') && count($request->products_list)>0){
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

    public function checkout(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'products_list' => 'required|array',
                'address_id'=>'required|numeric',
                'use_wallet_balance'=>'required|boolean',
                'promocode'=>'string'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $response = [];
            $user = Auth::user();
            $response['walletBalance'] = (float)$user->balance;
            $response['walletBalanceUsed'] = 0;
            $msg = '';
            $discountAmount = 0;
            $address = UserAddress::whereUserId(Auth::user()->id)->whereId($request->address_id)->first();
            if(is_null($address)){
                return $this->sendError('No Address For Current User With Id '.$request->address_id, [],200);
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
                    return $this->sendError('Invalid Promo code', [], 200);
                }
            }
            if($request->has('products_list') && count($request->products_list)>0){
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
                if($subTotal>0){
                    if($request->has("use_wallet_balance") && $request->use_wallet_balance){
                        if($response['walletBalance']<$subTotal){

                            $response['walletBalanceUsed'] = $response['walletBalance'];
                        }else{

                            $response['walletBalanceUsed'] = $response['walletBalance'] - $subTotal;
                        }
                    }
                    if(!is_null($promocode)){
                //to do for is new user
                        if($subTotal>$promocode['minimal_cart_total']){
                            if($promocode['type']=='percentage'){
                                $discountAmount  =  round(($subTotal*$promocode['discount'])/100,2) ;
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
                        }
                    }
                }
                Cart::insert($cart_records);
                $response['shippingCharges']=10;
                $response['discountAmount'] = (float)$discountAmount;
                $response['subtotal']=(float)$subTotal;
                $response['total']= (float)($subTotal+$response['shippingCharges'] - $discountAmount - $response['walletBalanceUsed']);
            }
            return $this->sendResponse($response,$msg==''?'Data Updated Successfully':$msg, true);
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

}
