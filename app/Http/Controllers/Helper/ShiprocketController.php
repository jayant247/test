<?php
namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
class ShiprocketController extends Controller{

    public function login(){

        $postfields = array("email"=> env('SHIPROCKER_EMAIL'),"password"=> env('SHIPROCKET_PASSWORD'));
        $url = '/external/auth/login';


            $response = $this->curlPostCall($url,$postfields,[]);
            $response = json_decode($response,true);
            $token = $response['token'];
            $expiresAt = Carbon::now()->endOfDay()->addSecond();
            Cache::put('shiprocket_token', $token,$expiresAt);
            return $token;


    }

    public function getAuthToken(){
        {

            if(Cache::has('shiprocket_token')){
                $now = Carbon::now();
                $token = Cache::get('shiprocket_token');
                $tokenParts = explode(".", $token);
                $tokenHeader = base64_decode($tokenParts[0]);
                $tokenPayload = base64_decode($tokenParts[1]);
                $jwtHeader = json_decode($tokenHeader);
                $jwtPayload = json_decode($tokenPayload);

                if(!($jwtPayload->exp < $now->getTimestamp() )){
                    return $this->login();
                }else{
                    return Cache::get('shiprocket_token');
                }
            }else{
                return $this->login();
            }
        }
    }

    function curlPostCall($url,$postFields,$header ){
        $url = "https://apiv2.shiprocket.in/v1".$url;
        $curl = curl_init();
//        print_r($url);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url ,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$postFields,
            CURLOPT_HTTPHEADER =>  array(
                'Authorization: Bearer '
            ),
//            array(
//                'Authorization : Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjE3MTIzNzgsImlzcyI6Imh0dHBzOi8vYXBpdjIuc2hpcHJvY2tldC5pbi92MS9leHRlcm5hbC9hdXRoL2xvZ2luIiwiaWF0IjoxNjI4MjMzNjYzLCJleHAiOjE2MjkwOTc2NjMsIm5iZiI6MTYyODIzMzY2MywianRpIjoiNklqT1FnYWhQeDh3T0ZVTyJ9.W3DAaop9FPbB4a9XVcCg6OY1URY0tBt5oGFg-imQ3_0'
//            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function placeOrder($order_id=519){
        $order_id=519;
        $orderData = Order::with(['orderStatus','addressDetails','customer','paymentStatus','orderItems','orderItems.productVariable','orderItems.productVariable.productDetails'])
            ->withCount(['orderItems'])->find($order_id);
//        print_r($orderData);
        $orderItems = array();
        foreach ($orderData->orderItems as $orderItem){
            $item = array();
            $item['name']=$orderItem->productVariable->productDetails->product_name;
            $item['sku']=$orderItem->productVariable->id;
            $item['units']=$orderItem->quantity;
            $item['selling_price']=$orderItem->selling_price;

            $orderItems[]=$item;
        }
        $postFields = array();
        $postFields['order_id']=$orderData->id;
        $postFields['order_date']=$orderData->created_at;
        $postFields['pickup_location']='Delhi';
        $postFields['billing_customer_name']=$orderData->customer->name;
        $postFields['billing_address']=$orderData->addressDetails->address_line_1;
        $postFields['billing_city']=$orderData->addressDetails->city;
        $postFields['billing_pincode']=$orderData->addressDetails->pincode;
        $postFields['billing_state']='Delhi';
        $postFields['billing_country']='India';
        $postFields['billing_email']=$orderData->customer->email;
        $postFields['billing_phone']=$orderData->addressDetails->contact_number;
        $postFields['billing_alternate_phone']=$orderData->customer->mobile_no;
        $postFields['shipping_is_billing']=1;
        $postFields['order_items']=$orderItems;
        $postFields['payment_method']='Prepaid';
        $postFields['sub_total']=$orderData->subTotal;
        $postFields['length']=$orderData->length;
        $postFields['breadth']=$orderData->breadth;
        $postFields['height']=$orderData->height;
        $postFields['weight']=$orderData->weight;
//        dd($postFields);
       $headers = array();
       array_push($headers,'Authorization : Bearer '.$this->getAuthToken());
//       $headers['Authorization']='Bearer '.$this->getAuthToken();
       $this->curlPostCall('/external/orders/create/adhoc',$postFields,$headers);
    }
}
