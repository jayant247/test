<?php

namespace App\Http\Controllers\API;



use App\Models\Order;
use App\Models\Promocode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Messaging\CloudMessage;
use paytm\paytmchecksum\PaytmChecksum;
use Validator;
use Auth;

class PromoCodeController extends  BaseController{

    public function getAvailablePromocodes(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user_used_promocodes = Order::where('is_promo_code',1)->where('user_id',Auth::user()->id)->select('promo_id')->distinct()->get()->toArray();
            $promocodes = Promocode::where('is_active',1)->where('end_on', '>=', Carbon::now())
                ->where('start_from', '<=', Carbon::now())->whereNotIn('id',$user_used_promocodes)->get()->toArray();
            if(count($promocodes)>0){
                return $this->sendResponse($promocodes,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Promo codes available', false);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getUsedPromocodes(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
//            dd(Auth::user()->id);
            $user_used_promocodes = Order::where('is_promo_code',1)->where('user_id',Auth::user()->id)->select('promo_id')->distinct()->get()->toArray();
//            dd($user_used_promocodes);
            $promocodes = Promocode::whereIn('id',$user_used_promocodes)->get()->toArray();
            if(count($promocodes)>0){
                return $this->sendResponse($promocodes,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Promo codes available', false);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }
}
