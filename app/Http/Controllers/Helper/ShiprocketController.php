<?php
namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
class ShiprocketController extends Controller{

    public function login(){
        $postfields = array("email"=> env('SHIPROCKER_EMAIL'),"password"=> env('SHIPROCKET_PASSWORD'));
        $url = '/external/auth/login';


            $response = $this->curlPostCall($url,$postfields);
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

    function curlPostCall($url,$postFields){
        $url = "https://apiv2.shiprocket.in/v1".$url;
        $curl = curl_init();

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
            CURLOPT_HTTPHEADER => array(

            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    public function placeOrder(){
        $postFields = array(
          "order_id"=> "224-477",
          "order_date"=> "2019-07-24 11:11",
          "pickup_location"=> "Jammu",
          "channel_id"=> "12345",
          "comment"=> "Reseller: M/s Goku",
          "billing_customer_name"=> "Naruto",
          "billing_last_name"=> "Uzumaki",
          "billing_address"=> "House 221B, Leaf Village",
          "billing_address_2"=> "Near Hokage House",
          "billing_city"=> "New Delhi",
          "billing_pincode"=> "110002",
          "billing_state"=> "Delhi",
          "billing_country"=> "India",
          "billing_email"=> "naruto@uzumaki.com",
          "billing_phone"=> "9876543210",
          "shipping_is_billing"=> true,
          "shipping_customer_name"=> "",
          "shipping_last_name"=> "",
          "shipping_address"=> "",
          "shipping_address_2"=> "",
          "shipping_city"=> "",
          "shipping_pincode"=> "",
          "shipping_country"=> "",
          "shipping_state"=> "",
          "shipping_email"=> "",
          "shipping_phone"=> "",
          "order_items"=> [
            array(
                "name"=> "Kunai",
              "sku"=> "chakra123",
              "units"=> 10,
              "selling_price"=> "900",
              "discount"=> "",
              "tax"=> "",
              "hsn"=> 441122
            )
          ],
          "payment_method"=> "Prepaid",
          "shipping_charges"=> 0,
          "giftwrap_charges"=> 0,
          "transaction_charges"=> 0,
          "total_discount"=>  0,
          "sub_total"=> 9000,
          "length"=> 10,
          "breadth"=> 15,
          "height"=> 20,
          "weight"=> 2.5
        );
    }
}
