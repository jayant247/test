<?php

namespace App\Http\Controllers\API;


use App\Models\Cart;
use App\Models\Category;
use App\Models\DeliveryPincode;
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

class DeliveryPincodeController extends BaseController{

    public function create(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pincode' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newUserAddress = new DeliveryPincode;
            $newUserAddress->pincode=$request->pincode;
            if($newUserAddress->save()){
                return $this->sendResponse([],'Delivery Pincode Added Successfully', true);
            }else{
                return $this->sendResponse([],'Delivery Pincode Not Added Successfully', false);
            }
        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function update(Request $request, $id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pincode' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newUserAddress = DeliveryPincode::find($id);
            if(!is_null($newUserAddress)){
                $newUserAddress->pincode=$request->pincode;
                if($newUserAddress->save()){
                    return $this->sendResponse([],'Delivery Pincode Updated Successfully', true);
                }else{
                    return $this->sendResponse([],'Delivery Pincode Not Updated Successfully', false);
                }
            }else{
                return $this->sendResponse([],'No Delivery Pincode Available With Id '.$id, false);
            }
        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function checkDeliveryAvailable(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pincode' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newUserAddress = DeliveryPincode::where('pincode',$request->pincode)->first();
            if(!is_null($newUserAddress)){


                    return $this->sendResponse([],'Delivery Available ', true);
            }else{
                return $this->sendResponse([],'Delivery Not Available At Pincode '.$request->pincode, false);
            }
        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function delete(Request $request, $id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pincode' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newUserAddress = DeliveryPincode::find($id);
            if(!is_null($newUserAddress)){

                if($newUserAddress->delete()){
                    return $this->sendResponse([],'Delivery Pincode Deleted Successfully', true);
                }else{
                    return $this->sendResponse([],'Delivery Pincode Not Deleted Successfully', false);
                }
            }else{
                return $this->sendResponse([],'No Delivery Pincode Available With Id '.$id, false);
            }
        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }
}
