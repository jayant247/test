<?php

namespace App\Http\Controllers\API;



use App\Models\Cart;
use App\Models\Category;
use App\Models\DeliveryPincode;
use App\Models\GiftCard;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Products;
use App\Models\ProductVariables;
use App\Models\Promocode;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserAddress;
use App\Models\UserCart;
use App\Models\UserWhishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Auth;

class GiftCardController extends BaseController{

    public function getGiftCards(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;

            $promocodes = GiftCard::where('is_active',1)->where('end_on', '>=', Carbon::now())
                ->where('start_from', '<=', Carbon::now())->skip($skip)->limit($limit)->get()->toArray();
            if(count($promocodes)>0){
                return $this->sendResponse($promocodes,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Promo codes available', false);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function buyGiftCard(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'gift_card_id'=>'required|numeric',
                'for_mobile_no'=>'required|digits:10',
                'paymentMode'=>'required|numeric|min:0|max:1'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $payment_mode = '';
            switch ($request->paymentMode) {
                case 0:
                    $payment_mode = 'paytm';
                    break;
                case 1:
                    $payment_mode = 'razorpay';
                    break;
            }



        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }




}
