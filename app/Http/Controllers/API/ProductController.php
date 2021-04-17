<?php
namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Models\ProductDescription;
use App\Models\ProductHasCategory;
use App\Models\Products;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;

class ProductController extends BaseController{


    //get all products
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
    //create new product
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
                'sale_percentage'=>'numeric|max:100',
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
                        "sale_price"=>['Sale price is required'],
                        "sale_percentage"=>['Sale percentage is required']
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
    //update product
    public function updateProduct(Request $request,$id){
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
                'sale_percentage'=>'numeric|max:100',
                'is_on_sale' => 'boolean',
                'available_sizes' => 'required|string',
                'available_colors' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }


            $newProduct = Products::find($id);
            if(!is_null($newProduct)){
                if($request->has('is_on_sale') && $request->is_on_sale=true){
                    if(!$request->has('sale_percentage') || !$request->has('sale_price')){
                        return $this->sendError('Validation Error.', [
                            "sale_price"=>['Sale price is required'],
                            "sale_percentage"=>['Sale percentage is required']
                        ]);
                    }else{
                        $newProduct->sale_price = $request->sale_price;
                        $newProduct->sale_percentage = $request->sale_percentage;
                        $newProduct->is_on_sale = $request->is_on_sale;
                    }
                }
                $newProduct->product_name =$request->has('product_name')?$request->product_name:$newProduct->product_name;
                $newProduct->description = $request->has('description')?$request->description:$newProduct->description;

                if($request->hasFile('primary_image')){
                    $oldImage = $newProduct->primary_image;
                    $newProduct->primary_image = $this->saveImage($request->primary_image);
                    unlink(public_path().$oldImage);
                }
                $newProduct->price = $request->has('price')?$request->price:$newProduct->price;
                $newProduct->mrp = $request->has('mrp')?$request->mrp:$newProduct->mrp;
                $newProduct->available_sizes = $request->has('available_sizes')?$request->available_sizes:$newProduct->available_sizes;
                $newProduct->available_colors = $request->has('available_colors')?$request->available_colors:$newProduct->available_colors;
                $newProduct->is_new = $request->has('is_new')? $request->is_new:$newProduct->is_new;
                $newProduct->save();


                $subCategoryArray = [];
                foreach (array_map('intval',explode(',',$request->sub_category_id)) as $key=>$eachLanguageId){
                    if(!in_array($eachLanguageId, $subCategoryArray)){
                        array_push($subCategoryArray,$eachLanguageId);
                    }
                }

                if(count($subCategoryArray)>0){
                    ProductHasCategory::query()->where('product_id',$newProduct->id)->delete();
                    foreach ($subCategoryArray as $key=>$subcategory){
                        $originalSubcategory = Category::find($subcategory);
                        if(!is_null($originalSubcategory)){
                            if(is_null(ProductHasCategory::withTrashed()->where('product_id',$newProduct->id)->where('sub_category_id',$subcategory)->first())){
                                $newCategoryMapping = new ProductHasCategory();
                                $newCategoryMapping->product_id = $newProduct->id;
                                $newCategoryMapping->category_id = $originalSubcategory->parent_id;
                                $newCategoryMapping->sub_category_id = $subcategory;
                                $newCategoryMapping->save();
                            }else if(!is_null(ProductHasCategory::withTrashed()->where('product_id',$newProduct->id)->where('sub_category_id',$subcategory)->first())){
                                ProductHasCategory::withTrashed()->where('product_id',$newProduct->id)->where('sub_category_id',$subcategory)->restore();
                            }
                        }
                    }
                }

                if($newProduct->save()){
                    return $this->sendResponse([],'Product Updation Successfully.', true);
                }else{
                    if(file_exists(public_path().$newProduct->primary_image)){
                        unlink(public_path().$newProduct->primary_image);
                    }
                    return $this->sendError('Product Updation Failed',[], 422);
                }
            }else{
                return $this->sendError('No Product Found', [],200);
            }



        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }

    }

    public function deleteProduct(Request $request,$id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }


            $newProduct = Products::find($id);
            if(!is_null($newProduct)){


                if($newProduct->delete()){
                    return $this->sendResponse([],'Product Deleted Successfully.', true);
                }else{
                    if(file_exists(public_path().$newProduct->primary_image)){
                        unlink(public_path().$newProduct->primary_image);
                    }
                    return $this->sendError('Product Deletion Failed',[], 422);
                }
            }else{
                return $this->sendError('No Product Found', [],200);
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

    //create product descriptons for the product
    public function createProductDescription(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_id' => 'required|numeric',
                'property_name' => 'required|string',
                'property_value' => 'required|string',

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $newProdcutDescription = new ProductDescription;
            $newProdcutDescription->product_id = $request->product_id;
            $newProdcutDescription->property_name = $request->property_name;
            $newProdcutDescription->property_value = $request->property_value;

            if($newProdcutDescription->save()){

                return $this->sendResponse([],'Product Description Created Successfully', true);
            }else{
                return $this->sendError('Product Description Creation Failed', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }
    //update product descriptons for the product
    public function updateProductDescription(Request $request, $id){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_id' => 'numeric',
                'property_name' => 'string',
                'property_value' => 'string',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $prodcutDescription = ProductDescription::find($id);
            if(!is_null($prodcutDescription)){
                foreach ($request->all() as $key=>$value){
                    $prodcutDescription->$key = $value;
                }
                if($prodcutDescription->save()){
                    return $this->sendResponse([],'Product Description Updated Successfully', true);
                }else{
                    return $this->sendError('Product Description Updation Failed', [],200);
                }
            }else{
                return $this->sendError('No Product Description Found', [],200);
            }


        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }
    //get product descriptons for the product.  limit and pageNo are optional, descriptio_id is also optional, product_id required
    public function getProductDescription(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'numeric',
                'limit'=>'numeric',
                'product_id' => 'required|numeric',
                'description_id' => 'numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $query = ProductDescription::query()->whereProductId($request->product_id);

            if($request->has('description_id')){
                $query =$query->where('id',$request->description_id);
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
    //delete product description
    public function deleteProductDescription(Request $request, $id){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $prodcutDescription = ProductDescription::find($id);
            if(!is_null($prodcutDescription)){

                if($prodcutDescription->delete()){
                    return $this->sendResponse([],'Product Description Deleted Successfully', true);
                }else{
                    return $this->sendError('Product Description Deletion Failed', [],200);
                }
            }else{
                return $this->sendError('No Product Description Found', [],200);
            }


        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }
}
