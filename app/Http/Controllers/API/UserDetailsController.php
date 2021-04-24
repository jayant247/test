<?php
namespace App\Http\Controllers\API;



use App\Models\Category;
use App\Models\Products;
use App\Models\ProductVariables;
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
            $userWishListItemIds = UserWhishlist::where('user_id',$userId)->pluck('product_id')->toArray();
            $query = Products::query()->whereIn('id',$userWishListItemIds);

            $data = $query->get();
            $colorArray=[];

            foreach($data as $key=>$product){
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

        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    //update User Whishlist
    public function addUserWishList(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_ids' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $userId = Auth::user()->id;
            $productIdArray=[];
            foreach (array_map('intval',explode(',',$request->product_ids)) as $key=>$eachLanguageId){
                if(!in_array($eachLanguageId, $productIdArray)){
                    array_push($productIdArray,$eachLanguageId);
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
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

}
