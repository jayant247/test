<?php

namespace App\Http\Controllers\API;



use App\Models\Cart;
use App\Models\Category;
use App\Models\DeliveryPincode;
use App\Models\GiftCard;
use App\Models\GiftCardPurchaseTransactions;
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
use Illuminate\Support\Facades\Crypt;
use paytm\paytmchecksum\PaytmChecksum;
use Razorpay\Api\Api;
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

            $giftCard = GiftCard::find($request->gift_card_id);
            if(!is_null($giftCard)){
                $payment_mode = '';
                $payment_gateway_id = '';
                $couponCode = $this->generateRandomString(10);
                $newCouponRegistration = new UserGiftCards;
                $newCouponRegistration->user_id = Auth::user()->id;
                $newCouponRegistration->coupon_code = base64_encode($couponCode);
                $newCouponRegistration->gift_for_mobile_number = $request->for_mobile_no;
                $newCouponRegistration->expiry_date = Carbon::now()->addDays($giftCard->validity_days_from_purchase_date);
                $newCouponRegistration->gift_card_id = $giftCard->id;
                $newCouponRegistration->purchase_amount = $giftCard->purchase_amount;
                $newCouponRegistration->gift_amount = $giftCard->gift_amount;
                if($newCouponRegistration->save()){
                    switch ($request->paymentMode) {
                        case 0:
                            $payment_mode = 'paytm';
                            break;
                        case 1:
                            $payment_mode = 'razorpay';
                            break;
                    }
                    $newTransaction = new GiftCardPurchaseTransactions;
                    $newTransaction->user_id = Auth::user()->id;
                    $newTransaction->user_gift_card_id = $newCouponRegistration->id;
                    $newTransaction->amount = $newCouponRegistration->purchase_amount;
                    if($newTransaction->save()){
                        $payment_mode_details = [];
                        if($payment_mode=='paytm'){
                            $payment_mode_details = $this->generateOrderPaytm($newTransaction->amount,$newTransaction->id);
                        }
                        if($payment_mode=='razorpay'){
                            $payment_mode_details = $this->generateOrderRazorpay($newTransaction->amount,$newTransaction->id);
                        }
                        if(count($payment_mode_details)>0){
                            $newTransaction->gateway_transaction_id = $payment_mode_details['txnToken'];
                            $newTransaction->save();
                            return $this->sendResponse($payment_mode_details,'Payment Initiated Successfully');
                        }else{
                            return $this->sendError('Error in payment gateway error transaction creation', ['error'=>"Error in payment gateway error transaction creation"],200);
                        }
                    }else{
                        return $this->sendError('Error in transaction creation', ['error'=>"Error in transaction creation"],200);
                    }
                }else{
                    return $this->sendError('Error in Gift Card Generation', ['error'=>"Error in Gift Card Generation"],200);
                }
            }else{
                return $this->sendError('No Gift Card Found', ['error'=>"No Gift Card Found"],200);
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
            "orderId"       => $orderId,
            "callbackUrl"   => "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=".$orderId,
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
            $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=".env("PAYTM_MERCHANT_ID")."&orderId=".$orderId;
        }else{
            $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=".env("PAYTM_MERCHANT_ID")."&orderId=".$orderId;
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
            $response['orderId']=$orderId;
            $response['amount']=$amount;
            $response['txnToken']=$curlResponse['body']['txnToken'];
            $response['callbackURL']=route('payment.paytmGiftCardFeesCallback');
            $response['isStaging']= env('PAYTM_ENVIRONMENT')=='local'?true:false;
        }
            return $response;

    }

    function generateOrderRazorpay($amount,$orderId){
        $api = new Api(env('R_API_KEY'), env('R_API_SECRET'));
        $razorOrder  = $api->order->create(array('receipt' => $orderId, 'amount' => intval($amount*100), 'currency' => 'INR',
            'payment_capture'=>'1')); // Creates order
        $rzorderId = $razorOrder['id'];

        $response = [];
        if(!is_null($razorOrder['id'])){
            $response['mid']=env('R_API_KEY');
            $response['orderId']=$orderId;
            $response['amount']=$amount;
            $response['txnToken']=$rzorderId;
            $response['isStaging']= env('PAYTM_ENVIRONMENT')=='local'?true:false;
        }

        return $response;
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if(UserGiftCards::where('coupon_code',$randomString)->first()){
            return $this->generateRandomString($length);
        }
        return $randomString;
    }

    public function paytmGiftCardFeesCallback(Request $request){
        try {

            $giftCardTransaction = GiftCardPurchaseTransactions::find($request->ORDERID);
            $user = User::find($giftCardTransaction->user_id);
            if($request->STATUS=='TXN_SUCCESS'){
                $giftCardTransaction->payment_status=1;
                $giftCardTransaction->save();
                $couponCode = UserGiftCards::find($giftCardTransaction->user_gift_card_id);
                $couponCode->payment_status = 1;
                $couponCode->save();
                $response=[];
                return $this->sendResponse($response,'Payment Success', true);
            }else{
                $giftCardTransaction->payment_status=0;
                $giftCardTransaction->save();
                $response=[];
                return $this->sendResponse($response,'Payment Failed', true);
            }

        }catch (\Exception $e){

            return $this->sendError('Something Went Wrong', $e->getMessage(),413);

        }
    }

    public function checkRazorpayPaymentStatus(Request $request){
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
            $giftCardTransaction= GiftCardPurchaseTransactions::where('id','=',$request->system_id)->where('gateway_transaction_id','=',$request->payment_gateway_order_id)->first();
            if(is_null($giftCardTransaction)){
                return $this->sendError('No Gift Card Found. Something Went Wrong', ['error'=>"No Gift Card Found"],200);
            }
            if($giftCardTransaction->payment_status==1){
                $response = [];
                $response['payment_status']=true;
                return $this->sendResponse($response,'Payment Already Done. Please Contact admin in case of any issue', false);
            }
            $api = new Api(env('R_API_KEY'), env('R_API_SECRET'));
            $razorpay_order = $api->order->fetch($request->payment_gateway_order_id);
            if($razorpay_order->status == 'paid'){
                $giftCardTransaction->payment_status=1;
                $giftCardTransaction->save();
                $couponCode = UserGiftCards::find($giftCardTransaction->user_gift_card_id);
                $couponCode->payment_status = 1;
                $couponCode->save();
                $couponCode->coupon_code = base64_decode($couponCode->coupon_code);
                $response = [];
                $response['payment_status']=true;
                $response['couponDetails']=$couponCode;
                return  $this->sendResponse($response,'Payment Successful');
            }
            else{
                $response = [];
                $response['payment_status']=false;
                return $this->sendResponse($response,'Payment Failed. Please contact admin in case your payment deducted.', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e->getMessage(),413);
        }
    }

    public function checkPaytmPaymentStatus(Request $request){
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
            $giftCardTransaction= GiftCardPurchaseTransactions::where('id','=',$request->system_id)->where('gateway_transaction_id','=',$request->payment_gateway_order_id)->first();

            if(is_null($giftCardTransaction)){
                return $this->sendError('No Gift Card Found. Something Went Wrong', ['error'=>"No Gift Card Found"],200);
            }
            if($giftCardTransaction->payment_status==1){
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
                "orderId" => $request->system_id,
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
                $giftCardTransaction->payment_status=1;
                $giftCardTransaction->save();
                $couponCode = UserGiftCards::find($giftCardTransaction->user_gift_card_id);
                $couponCode->payment_status = 1;
                $couponCode->save();
                $couponCode->coupon_code = base64_decode($couponCode->coupon_code);
                $response = [];
                $response['payment_status']=true;
                $response['couponDetails']=$couponCode;
                return  $this->sendResponse($response,'Payment Successful');
            }
            else{
                $response = [];
                $response['payment_status']=false;
                return $this->sendResponse($response,'Payment Failed. Please contact admin in case your payment deducted.', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e->getMessage(),413);
        }
    }

    public function getMyCouponCode(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
                'is_active'=>'required|boolean'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $user = Auth::user();
            if($request->is_active){
                $userGiftCards = UserGiftCards::where('gift_for_mobile_number',$user->mobile_no)->where('payment_status',1)->where('use_status',0)->skip($skip)->limit($limit)->orderBy('id','DESC')->get();
            }else{
                $userGiftCards = UserGiftCards::where('gift_for_mobile_number',$user->mobile_no)->where('payment_status',1)->where('use_status',1)->skip($skip)->limit($limit)->orderBy('id','DESC')->get();
            }

            if(count($userGiftCards)>0){

                foreach ($userGiftCards as $card){
                    $card->coupon_code = base64_decode($card->coupon_code);
                }
                return $this->sendResponse($userGiftCards,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No  codes available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e->getMessage(),413);
        }
    }

    public function getGiftCardPurchasedByMe(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
                'is_active'=>'required|boolean'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $user = Auth::user();
            if($request->is_active){
                $userGiftCards = UserGiftCards::where('gift_for_mobile_number',$user->mobile_no)->where('payment_status',1)->where('use_status',0)->skip($skip)->limit($limit)->orderBy('id','DESC')->get();
            }else{
                $userGiftCards = UserGiftCards::where('gift_for_mobile_number',$user->mobile_no)->where('payment_status',1)->where('use_status',1)->skip($skip)->limit($limit)->orderBy('id','DESC')->get();
            }
            if(count($userGiftCards)>0){
                return $this->sendResponse($userGiftCards,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No codes available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function sendGiftCardVerificationOTP(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'gift_card_code'=>'required|string',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $now = Carbon::now();
            $couponCode = base64_encode($request->gift_card_code);
//            dd(base64_encode('v27kBCpXQI'));
            $userGiftCardCode = UserGiftCards::where('coupon_code',$couponCode)
                ->where('use_status',0)
                ->where('payment_status',1)
                ->where('expiry_date','>',$now)
                ->first();
            if(!is_null($userGiftCardCode)){
                if($userGiftCardCode->withdraw_otp) {
                    $start_date = new \DateTime();
                    $since_start = $start_date->diff(new \DateTime($userGiftCardCode->withdraw_otp_time));
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    if ($minutes > 10) {
                        $userGiftCardCode->withdraw_otp = rand(100000,999999);
                        $userGiftCardCode->withdraw_otp_time = Carbon::now();
                        $userGiftCardCode->save();
                        if($this->sendOtp($userGiftCardCode)){
                            return $this->sendResponse([], 'New OTP Send Successfully');
                        }else{
                            return $this->sendResponse([], 'OTP Send Failed',false);
                        }

                    }else{

                        if($this->sendOtp($userGiftCardCode)){
                            return $this->sendResponse([], 'OTP Send Successfully');
                        }else{
                            return $this->sendResponse([], 'OTP Send Failed',false);
                        }
                    }
                }else{
                    $userGiftCardCode->withdraw_otp = rand(100000,999999);
                    $userGiftCardCode->withdraw_otp_time = Carbon::now();
                    $userGiftCardCode->save();
                    if($this->sendOtp($userGiftCardCode)){
                        return $this->sendResponse([], 'New OTP Send Successfully');
                    }else{
                        return $this->sendResponse([], 'OTP Send Failed',false);
                    }
                }
            }else{
                return $this->sendError('Invalid Coupon Code', [],211);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    function sendOtp($user){
        $curl = curl_init();
        $url = "https://2factor.in/API/V1/".env("MESSAGE_API_KEY")."/SMS/+91".$user->gift_for_mobile_number."/".$user->withdraw_otp;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(

            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        $response = json_decode($response,true);
        if($response["Status"]=="Success"){
            return true;
        }else{
            return false;
        }
    }

    public function verifyCouponOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'gift_card_code'=>'required|string',
                'otp'=>'required|numeric',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $now = Carbon::now();
            $couponCode = base64_encode($request->gift_card_code);
            $userGiftCardCode = UserGiftCards::where('coupon_code',$couponCode)->where('use_status',0)
                ->where('payment_status',1)
                ->where('expiry_date','>',$now)
                ->first();
            if(!is_null($userGiftCardCode)){
                if($userGiftCardCode->withdraw_otp){
                    $start_date = new \DateTime();
                    $since_start = $start_date->diff(new \DateTime($userGiftCardCode->withdraw_otp_time));
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    if($minutes>10){
                        return $this->sendError('OTP Timeout. Please Generate New OTP', ['error'=>"OTP Timeout. Please Generate New OTP"]);
                    }else{
                        if($userGiftCardCode->withdraw_otp==$request->otp.''){
                            $userGiftCardCode->withdraw_otp=null;
                            $userGiftCardCode->otp_verified_at = Carbon::now();
                            $userGiftCardCode->gift_for_user_id = Auth::user()->id;
                            $userGiftCardCode->save();
                            return $this->sendResponse([], 'OTP Verified Successfully');
                        }else{
                            return $this->sendError('Wrong OTP', ['error'=>"Wrong OTP"]);
                        }
                    }
                }else{
                    return $this->sendError('Please Generate New OTP', ['error'=>"OTP Timeout. Please Generate New OTP"]);
                }
            }else{
                return $this->sendError('Invalid Gift Card', [],211);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e->getMessage(),413);
        }
    }


}
