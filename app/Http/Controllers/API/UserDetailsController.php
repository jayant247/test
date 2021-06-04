<?php
namespace App\Http\Controllers\API;



use App\Models\Category;
use App\Models\Products;
use App\Models\ProductVariables;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserAddress;
use App\Models\UserWallet;
use App\Models\UserWhishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;
use Auth;

class UserDetailsController extends BaseController{

    //get user whishlist item
    public function getUserWishlist(Request $request){
        try {
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
            $userId = Auth::user()->id;
            $userWishListItemIds = UserWhishlist::where('user_id',$userId)->skip($skip)->limit($limit)->pluck('product_id')->toArray();
            $query = Products::query()->whereIn('id',$userWishListItemIds);

            $data = $query->get();


            foreach($data as $key=>$product){
                $colorArray=[];
                $productVariable = ProductVariables::whereProductId($product['id'])->get();
                $productColorsImageArray = [];
                foreach ($productVariable as $prodVar){
                    if(!in_array($prodVar['color'], $colorArray)){
                        array_push($colorArray,$prodVar['color']);
                        $imageColorArray = ['color'=>$prodVar['color'],'imagePath'=>$prodVar['primary_image']];
                        array_push($productColorsImageArray,$imageColorArray);
                    }
                }

                $product['colorsImageArray']=$productColorsImageArray;
            }
            if(count($data)>0){
                $response =  $data;
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    //update User Whishlist
    public function addUserWishList(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_ids' => '',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $userId = Auth::user()->id;
            $productIdArray=[];
            if($request->has('product_ids')){
                foreach (array_map('intval',explode(',',$request->product_ids)) as $key=>$eachLanguageId){
                    if(!in_array($eachLanguageId, $productIdArray)){
                        array_push($productIdArray,$eachLanguageId);
                    }
                }
            }

            $productsData = Products::whereIn('id',$productIdArray)->get();
            $userWishListItemMakeFalse = UserWhishlist::where('user_id',$userId)->delete();
            foreach ($productsData as $product){
                $userWishListItem = UserWhishlist::withTrashed()->where('user_id',$userId)->where('product_id',$product['id'])->first();
                if(is_null($userWishListItem)){
                    $newUserWishListItem = new UserWhishlist;
                    $newUserWishListItem->user_id=$userId;
                    $newUserWishListItem->product_id=$product['id'];
                    $newUserWishListItem->save();
                }else{
                    $userWishListItem->deleted_at=null;
                    $userWishListItem->save();
                }
            }
            return $this->sendResponse([],'User whishList Updated Successfully', true);

        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    //get user address
    public function getUserAddress(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
                'address_id'=>'numeric'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = (int)$request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $userId = Auth::user()->id;
            if($request->has('address_id')){
                $userAddress = UserAddress::where('user_id',$userId)->where('id',$request->address_id)->get();
            }else{
                $userAddress = UserAddress::where('user_id',$userId)->skip($skip)->limit($limit)->get();
            }
//            $userAddress = UserAddress::where('user_id',$userId)->skip($skip)->limit($limit)->get();

            if(count($userAddress)>0){
                $response =  $userAddress;
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    //create user address

    public function createUserAddress(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'address_name' => 'required|string',
                'is_primary'=>'required|boolean',
                'pincode' => 'required|numeric',
                'address_line_1' => 'required|string',
                'city' => 'required|string',
                'contact_number' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $userId = Auth::user()->id;
            $newUserAddress = new UserAddress;
            $newUserAddress->user_id = $userId;
            $newUserAddress->address_name=$request->address_name;
            $newUserAddress->pincode=$request->pincode;
            $newUserAddress->address_line_1=$request->address_line_1;
            $newUserAddress->city=$request->city;
            $newUserAddress->contact_number=$request->contact_number;
            $newUserAddress->save();
            if($request->is_primary){
                UserAddress::whereUserId($userId)->update(['is_primary'=>false]);
                $newUserAddress->is_primary= true;
                $newUserAddress->save();
                $user = User::find($userId);
                $user->primary_address = $newUserAddress->address_line_1;
                $user->primary_pincode =$newUserAddress->pincode;
                $user->city=$newUserAddress->city;
                $user->save();
            }
            $addressData = UserAddress::where('user_id',$userId)->orderBy('id','DESC')->get()->toArray();
            if($newUserAddress->save()){
                return $this->sendResponse($addressData,'User Address Added Successfully', true);
            }else{
                return $this->sendResponse([],'User Address Not Added Successfully', false);
            }

        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function updateUserAddress(Request $request, $id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'address_name' => 'string',
                'is_primary'=>'boolean',
                'pincode' => 'numeric',
                'address_line_1' => 'string',
                'city' => 'string',
                'contact_number' => 'string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $userId = Auth::user()->id;
            $userAddress = UserAddress::find($id);
            if(!is_null($userAddress)){
                if($userAddress->user_id == $userId){

                    $userAddress->address_name=$request->address_name;
                    $userAddress->pincode=$request->pincode;
                    $userAddress->address_line_1=$request->address_line_1;
                    $userAddress->city=$request->city;
                    $userAddress->contact_number=$request->contact_number;
                    $userAddress->save();
                    if($request->is_primary){
                        UserAddress::whereUserId($userId)->update(['is_primary'=>false]);
                        $userAddress->is_primary= true;
                        $userAddress->save();
                        $user = User::find($userId);
                        $user->primary_address = $userAddress->address_line_1;
                        $user->primary_pincode =$userAddress->pincode;
                        $user->city=$userAddress->city;
                        $user->save();
                    }
                    if($userAddress->save()){
                        return $this->sendResponse([],'User Address Updated Successfully', true);
                    }else{
                        return $this->sendResponse([],'User Address Not Updated', false);
                    }
                }else{
                    return $this->sendResponse([],'User Address Not Attached to Your Account ', false);
                }

            }
            else{
                return $this->sendResponse([],'No User Address With This Id ', false);
            }

        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function deleteUserAddress(Request $request,$id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $userId = Auth::user()->id;
            $userAddress = UserAddress::find($id);
            if(!is_null($userAddress)){
                if($userAddress->user_id == $userId){
                    if($userAddress->delete()){
                        return $this->sendResponse([],'User Address Deleted Successfully', true);
                    }else{
                        return $this->sendResponse([],'User Address Not Deleted ', false);
                    }
                }else{
                    return $this->sendResponse([],'User Address Not Attached to Your Account ', false);
                }

            }else{
                return $this->sendResponse([],'No User Address With This Id ', false);
            }

        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function createUserActivity(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_id'=>'required|numeric',
                'category_id'=>'numeric'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $product = null;
            if($request->has('category_id') && $request->category_id!=-1){
                $categoryId = $request->category_id;
                $product = Products::whereId($request->product_id)->whereHas('categories', function ($query) use($categoryId){
                    $query->where('category_id', $categoryId);
                })->first();
            }else{
                $product = Products::whereId($request->product_id)->first();
            }

            $userId = Auth::user()->id;
            $isSaved = false;
            if(!is_null($product)){

                $userActivity = UserActivity::where('user_id',$userId)->where('product_id',$request->product_id)->orderBy('id','DESC')->first();
                if(!is_null($userActivity)){
                    $userActivity->user_id = $userId;
                    $userActivity->updated_at = Carbon::now();
                    $isSaved = $userActivity->update();

                }else{
                    $newActivity = new UserActivity;
                    $newActivity->product_id = $request->product_id;
                    $newActivity->category_id = $request->category_id!=-1?$request->category_id:null;
                    $newActivity->user_id = $userId;
                    $isSaved = $newActivity->save();
                }

                if($isSaved){
                    $response =  [];
                    return $this->sendResponse($response,'Data Saved Successfully', true);
                }else{
                    return $this->sendError('No Data Saved', [],200);
                }
            }
            else{
                return $this->sendError('No Prodcut With id '.$request->product_id.' Available',[], 200);
            }



        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function recentlyViewProducts(Request $request){
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
            $userId = Auth::user()->id;
            $userWishListItemIds = UserActivity::where('user_id',$userId)->skip($skip)->limit($limit)->orderBy('updated_at','DESC')->pluck('product_id')->toArray();
            $query = Products::query()->whereIn('id',$userWishListItemIds);

            $data = $query->get();

            foreach($data as $key=>$product){
                $colorArray=[];
                $productVariable = ProductVariables::whereProductId($product['id'])->get();
                $productColorsImageArray = [];
                foreach ($productVariable as $prodVar){
                    if(!in_array($prodVar['color'], $colorArray)){
                        array_push($colorArray,$prodVar['color']);
                        $imageColorArray = ['color'=>$prodVar['color'],'imagePath'=>$prodVar['primary_image']];
                        array_push($productColorsImageArray,$imageColorArray);
                    }
                }

                $product['colorsImageArray']=$productColorsImageArray;
            }
            if(count($data)>0){
                $response =  $data;
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);

        }
    }

    public function getWalletTransaction(Request $request){
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
            $user = Auth::user();
            $userTransaction = UserWallet::where('user_id',$user->id)->where('status',1)->skip($skip)->limit($limit)->orderBy('id','DESC')->get();
            if(count($userTransaction)>0){
                return $this->sendResponse($userTransaction,'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse([],'No Transactions available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function getWalletBalance(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $user = Auth::user();
            $walletBalance = $user->balance();


            return $this->sendResponse(['walletBalance'=>$walletBalance],'Data Fetched Successfully', true);

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function addWalletBalance(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $user = Auth::user();
            $data = ['type'  =>  'credit',
                'amount' => 100,
                'description' =>  "Temp Wallet Balance Update",
                'status' => 1,
            ];
            $wallet = $user->transactions()
                ->create($data);
            $walletBalance = $user->balance();


            return $this->sendResponse(['walletBalance'=>$walletBalance],'Data Added Successfully', true);

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e->getMessage(),413);
        }
    }
}
