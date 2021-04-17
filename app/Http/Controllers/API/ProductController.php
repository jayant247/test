<?php
namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Models\ProductHasCategory;
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;

class ProductController extends BaseController{

    public function createProduct(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_name' => 'required|string',
                'description' => 'required|string',
                'sub_category_id' => 'required|string',
                'primary_image' => 'required|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'is_new' => 'boolean',
                'price'=>'required|numeric',
                'mrp'=>'required|numeric',
                'sale_price'=>'numeric',
                'sale_percentage'=>'numeric',
                'is_on_sale' => 'boolean',
                'available_sizes' => 'required|string',
                'available_colors' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }


            $newProduct = new Products;
            if($request->has('is_on_sale') && $request->is_on_sale=true){
                if(!$request->has('sale_percentage') || !$request->has('sale_price')){
                    return $this->sendError('Validation Error.', [
                        "sale_price"=>'Sale price is required',
                        "sale_percentage"=>'Sale percentage is required'
                    ]);
                }else{
                    $newProduct->sale_price = $request->sale_price;
                    $newProduct->sale_percentage = $request->sale_percentage;
                    $newProduct->is_on_sale = $request->is_on_sale;
                }
            }
            $newProduct->product_name = $request->product_name;
            $newProduct->description = $request->description;
            $newProduct->primary_image =$this->saveImage($request->primary_image) ;
            $newProduct->price = $request->price;
            $newProduct->mrp = $request->mrp;
            $newProduct->available_sizes = $request->product_name;
            $newProduct->available_colors = $request->product_name;
            $newProduct->is_new = $request->has('is_new')? $request->is_new:false;
            $newProduct->save();


            $subCategoryArray = [];
            foreach (array_map('intval',explode(',',$request->sub_category_id)) as $key=>$eachLanguageId){
                if(!in_array($eachLanguageId, $subCategoryArray)){
                    array_push($subCategoryArray,$eachLanguageId);
                }
            }
            foreach ($subCategoryArray as $key=>$subcategory){
                $originalSubcategory = Category::find($subcategory);
                if(!is_null($originalSubcategory)){
                    if(is_null(ProductHasCategory::where('product_id',$newProduct->id)->where('sub_category_id',$subcategory)->first())){
                        $newCategoryMapping = new ProductHasCategory();
                        $newCategoryMapping->product_id = $newProduct->id;
                        $newCategoryMapping->category_id = $originalSubcategory->parent_id;
                        $newCategoryMapping->sub_category_id = $subcategory;
                        $newCategoryMapping->save();
                    }
                }

            }
            if($newProduct->save()){
                return $this->sendResponse([],'Product Created Successfully.', true);
            }else{
                if(file_exists(public_path().$newProduct->primary_image)){
                    unlink(public_path().$newProduct->primary_image);
                }
                return $this->sendError('Product Creation Failed',[], 422);
            }


        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }

    }

    public function getProduct(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'numeric',
                'limit'=>'numeric',
                'fields'=>'string',
                'product_name'=>'string',
                'sub_category_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $subCategoryId = $request->sub_category_id;
            $query = Products::query();
            $query->whereHas('subCategories', function ($query) use($subCategoryId){
                $query->where('sub_category_id', $subCategoryId);
            });
            if($request->has('product_name')){
                $query =$query->where('category_name','like','%'.$request->category_name.'%');
            }
            if($request->has('fields')){
                $fieldsArray=explode(',',$request->fields);
                $query = $query->select($fieldsArray);
            }

            $data = $query->get();
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

    function saveImage($image){
        $image_name = 'product'.time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/product/');
        $image->move($destinationPath, $image_name);
        $imageURL='/images/product/'.$image_name;
        return $imageURL;
    }



}
