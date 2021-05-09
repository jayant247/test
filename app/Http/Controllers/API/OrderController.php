<?php
namespace App\Http\Controllers\API;



use App\Models\Cart;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductVariables;
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

}
