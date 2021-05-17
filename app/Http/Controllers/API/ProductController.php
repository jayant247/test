<?php
namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Models\Order;
use App\Models\ProductDescription;
use App\Models\ProductHasCategory;
use App\Models\ProductImages;
use App\Models\ProductReview;
use App\Models\Products;
use App\Models\ProductVariables;
use App\Models\UserActivity;
use App\Models\UserWhishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Auth;
use Validator;

class ProductController extends BaseController{


    //get all products
    public function getProduct(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
                'fields'=>'string',
                'product_name'=>'string',
                'sub_category_id' => 'string',
                'category_id'=>'required|numeric',
                'sortByPrice'=>'boolean',
                'sortBySalePercentage'=>'boolean',
                'isOnSale'=>'boolean',
                'bestSelling'=>'boolean',
                'priceRangeHigh'=>'numeric',
                'priceRangeLow'=>'numeric',
                'salePercentageRangeHigh'=>'numeric',
                'salePercentageRangeLow'=>'numeric'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $query = Products::query();
            $categoryId = $request->category_id;

            $query->whereHas('categories', function ($query) use($categoryId){
                $query->where('category_id', $categoryId);
            });


            if($request->has('sub_category_id')){
                $subCategoryId = array_map('intval',explode(',',$request->sub_category_id));
                $query->whereHas('subCategories', function ($query) use($subCategoryId){
                    $query->whereIn('sub_category_id', $subCategoryId);
                });
            }

            if($request->has('colors')){
                $colors = explode(',',$request->colors);;
                $query->whereHas('productVariables', function ($query) use($colors){
                    $query->whereIn('color', $colors);
                });
            }
            if($request->has('sizes')){
                $sizes = explode(',',$request->sizes);;
                $query->whereHas('productVariables', function ($query) use($sizes){
                    $query->whereIn('size', $sizes);
                });
            }
            if($request->has('types')){
                $types = explode(',',$request->types);;
                $query->whereHas('productVariables', function ($query) use($types){
                    $query->whereIn('type', $types);
                });
            }

            if($request->has('product_name')){
                $query =$query->where('category_name','like','%'.$request->category_name.'%');
            }
            if($request->has('fields')){
                $fieldsArray=explode(',',$request->fields);
                $query = $query->select($fieldsArray);
            }

            if($request->has('sortByPrice')){
                if($request->sortByPrice){
                    $query =$query->orderBy('price','DESC');
                }else{
                    $query =$query->orderBy('price','ASC');
                }

            }
            if($request->has('bestSelling')){
                if($request->bestSelling){
                    $query =$query->orderBy('sellCount','DESC');
                }else{
                    $query =$query->orderBy('sellCount','ASC');
                }

            }
            if($request->has('sortBySalePercentage')){
                if($request->sortBySalePercentage){
                    $query =$query->orderBy('sale_percentage','DESC');
                }else{
                    $query =$query->orderBy('sale_percentage','ASC');
                }

            }

            if($request->has('isOnSale')){
                if($request->isOnSale){
                    $query =$query->where('is_on_sale',true);
                }else{
                    $query =$query->where('is_on_sale',false);
                }
            }

            if($request->has('salePercentageRangeHigh') && $request->has('salePercentageRangeLow')){
                $low = $request->salePercentageRangeLow;
                $high = $request->salePercentageRangeHigh;
               $query =  $query->whereHas('productVariables', function ($query) use ($low,$high) {
                    $query->where('is_on_sale',true)->whereBetween('sale_percentage',[$low,$high]);
                });
            }

            if($request->has('priceRangeLow') && $request->has('priceRangeHigh')){
                $low = $request->priceRangeLow;
                $high = $request->priceRangeHigh;
                $query =  $query->whereHas('productVariables', function ($query) use ($low,$high) {
                    $query->whereBetween('price',[$low,$high]);
                });
            }
            $query->whereHas('productVariables', function ($query) {
                $query->where('quantity','>',0);
            })->with(['bestReviews','bestReviews.userInfo']);
            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $query= $query->skip($skip)->limit($limit);
            $data = $query->orderBy('id','DESC')->get();

            $userId = Auth::user()->id;
            $userWishListItemIds = UserWhishlist::where('user_id',$userId)->pluck('product_id')->toArray();
            foreach($data as $key=>$product){
                if(in_array($product['id'], $userWishListItemIds)){
                    $product['isInUserWishList']=true;
                }else{
                    $product['isInUserWishList']=false;
                }
                $productVariable = ProductVariables::whereProductId($product['id'])->where('quantity','>',0)->get();
                $productColorsImageArray = [];
                $colorArray=[];
                foreach ($productVariable as $prodVar){

                    if(!in_array($prodVar['color'], $colorArray)){
                        array_push($colorArray,$prodVar['color']);
                        $imageColorArray = ['color'=>$prodVar['color'],'imagePath'=>$prodVar['primary_image']];
                        array_push($productColorsImageArray,$imageColorArray);
                    }
                }
//                dd($productColorsImageArray);
                $product['colorsImageArray']=$productColorsImageArray;
//                $data[$key]
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
            $newProduct->available_sizes = $request->has('available_sizes')?$request->available_sizes:null;
            $newProduct->available_colors = $request->has('available_colors')?$request->available_colors:null;
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


        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
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



        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }
    //delete product
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

                    return $this->sendError('Product Deletion Failed',[], 422);
                }
            }else{
                return $this->sendError('No Product Found', [],200);
            }



        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    //product variables

    //get product variables

    public function getProductVariable(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'numeric',
                'limit'=>'numeric',
                'product_id' => 'required|numeric',
                'variable_id' => 'numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $query = ProductVariables::query()->whereProductId($request->product_id);
            if($request->has('variable_id')){
                $query =$query->where('id',$request->variable_id);
            }
            if($request->has('pageNo') && $request->has('limit')){
                $limit = $request->limit;
                $pageNo = $request->pageNo;
                $skip = $limit*$pageNo;
                $query= $query->skip($skip)->limit($limit);
            }
            $data = $query->get();
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

    //create product variables
    public function createProductVariable(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_id' => 'required|numeric',
                'primary_image' => 'required|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'price'=>'required|numeric',
                'mrp'=>'required|numeric',
                'sale_price'=>'numeric',
                'sale_percentage'=>'numeric|max:100',
                'is_on_sale' => 'boolean',
                'color' => 'string',
                'size' => 'string',
                'type'=>'string',
                'quantity'=>'required|numeric'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $product = Products::find($request->product_id);
            if(!is_null($product)){
                $newProductVariable = new ProductVariables();
                if($request->has('is_on_sale') && $request->is_on_sale=true){
                    if(!$request->has('sale_percentage') || !$request->has('sale_price')){
                        return $this->sendError('Validation Error.', [
                            "sale_price"=>['Sale price is required'],
                            "sale_percentage"=>['Sale percentage is required']
                        ]);
                    }else{
                        $newProductVariable->sale_price = $request->sale_price;
                        $newProductVariable->sale_percentage = $request->sale_percentage;
                        $newProductVariable->is_on_sale = $request->is_on_sale;
                    }
                }
                $newProductVariable->product_id = $request->product_id;
                $newProductVariable->primary_image =$this->saveImage($request->primary_image) ;
                $newProductVariable->price = $request->price;
                $newProductVariable->mrp = $request->mrp;
                $newProductVariable->quantity = $request->quantity;
                $newProductVariable->color = $request->has('color')?$request->color:null;
                $newProductVariable->size = $request->has('size')?$request->size:null;
                $newProductVariable->type = $request->has('type')?$request->type:null;
                $newProductVariable->save();
                if($newProductVariable->is_on_sale){
                    $product->sale_price = $request->sale_price;
                    $product->sale_percentage = $request->sale_percentage;
                    $product->is_on_sale = $request->is_on_sale;
                    $product->save();
                }
                $allProductVariables = ProductVariables::whereProductId($newProductVariable->product_id)->get();
                $colorsArray = [];
                $sizeArray = [];
                foreach($allProductVariables as $key=>$prodVar){
                    if(!in_array($prodVar['color'], $colorsArray)){
                        array_push($colorsArray,$prodVar['color']);
                    }
                    if(!in_array($prodVar['size'], $sizeArray)){
                        array_push($sizeArray,$prodVar['size']);
                    }
                }
                $product->available_sizes=implode(" ,",$sizeArray);
                $product->available_colors=implode(" ,",$colorsArray);
                $product->save();
                if($newProductVariable->save()){
                    return $this->sendResponse([],'Product Variable Created Successfully.', true);
                }else{
                    if(file_exists(public_path().$newProductVariable->primary_image)){
                        unlink(public_path().$newProductVariable->primary_image);
                    }
                    return $this->sendError('Product Variable Creation Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut With id '.$request->product_id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    public function updateProductVariable(Request $request,$id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_id' => 'numeric',
                'primary_image' => 'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'price'=>'numeric',
                'mrp'=>'numeric',
                'sale_price'=>'numeric',
                'sale_percentage'=>'numeric|max:100',
                'is_on_sale' => 'boolean',
                'color' => 'string',
                'size' => 'string',
                'type'=>'string',
                'quantity'=>'numeric'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newProductVariable = ProductVariables::find($id);

            if(!is_null($newProductVariable)){
                if($request->has('is_on_sale') && $request->is_on_sale=true){
                    if(!$request->has('sale_percentage') || !$request->has('sale_price')){
                        return $this->sendError('Validation Error.', [
                            "sale_price"=>['Sale price is required'],
                            "sale_percentage"=>['Sale percentage is required']
                        ]);
                    }else{
                        $newProductVariable->sale_price = $request->sale_price;
                        $newProductVariable->sale_percentage = $request->sale_percentage;
                        $newProductVariable->is_on_sale = $request->is_on_sale;
                    }
                }

                if($request->has('product_id')){
                    $product = Products::find($request->product_id);
                    if(!is_null($product)){
                        $newProductVariable->product_id = $request->product_id;
                    }else{
                        return $this->sendError('No Prodcut Variable With id '.$request->product_id.' available',[], 200);
                    }
                }
                if($request->hasFile('primary_image')){
                    $oldImage = $newProductVariable->primary_image;
                    $newProductVariable->primary_image = $this->saveImage($request->primary_image);
                    unlink(public_path().$oldImage);
                }

                $newProductVariable->price = $request->has('price')?$request->price:$newProductVariable->price;
                $newProductVariable->mrp = $request->has('mrp')?$request->mrp:$newProductVariable->mrp;
                $newProductVariable->color = $request->has('color')?$request->color:$newProductVariable->color;
                $newProductVariable->size = $request->has('size')?$request->size:$newProductVariable->size;
                $newProductVariable->quantity = $request->has('quantity')?$request->quantity:$newProductVariable->quantity;
                $newProductVariable->type = $request->has('type')?$request->type:$newProductVariable->type;

                $newProductVariable->save();
                $product = Products::find($newProductVariable->product_id);
                if($newProductVariable->is_on_sale){
                    $product->sale_price = $request->sale_price;
                    $product->sale_percentage = $request->sale_percentage;
                    $product->is_on_sale = $request->is_on_sale;
                    $product->save();
                }

                $allProductVariables = ProductVariables::whereProductId($newProductVariable->product_id)->get();
                $colorsArray = [];
                $sizeArray = [];
                foreach($allProductVariables as $key=>$prodVar){
                    if(!in_array($prodVar['color'], $colorsArray)){
                        array_push($colorsArray,$prodVar['color']);
                    }
                    if(!in_array($prodVar['size'], $sizeArray)){
                        array_push($sizeArray,$prodVar['size']);
                    }
                }
                $product->available_sizes=implode(" ,",$sizeArray);
                $product->available_colors=implode(" ,",$colorsArray);
                $product->save();
                if($newProductVariable->save()){
                    return $this->sendResponse([],'Product Variable Updated Successfully.', true);
                }else{
                    if(file_exists(public_path().$newProductVariable->primary_image)){
                        unlink(public_path().$newProductVariable->primary_image);
                    }
                    return $this->sendError('Product Variable Updation Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut Variable With id '.$id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    public function deleteProductVariable(Request $request,$id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newProductVariable = ProductVariables::find($id);

            if(!is_null($newProductVariable)){
                if($newProductVariable->delete()){
                    return $this->sendResponse([],'Product Variable Deleted Successfully.', true);
                }else{

                    return $this->sendError('Product Variable Deletion Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut Variable With id '.$id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    //product images

    public function getProductImages(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'numeric',
                'limit'=>'numeric',
                'product_id' => 'required|numeric',
                'product_variable_id' => 'numeric',
                'image_id' => 'numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $query = ProductImages::query()->whereProductId($request->product_id);
            if($request->has('product_variable_id')){
                $query =$query->where('product_variable_id',$request->product_variable_id);
            }
            if($request->has('image_id')){
                $query =$query->where('id',$request->image_id);
            }
            if($request->has('pageNo') && $request->has('limit')){
                $limit = $request->limit;
                $pageNo = $request->pageNo;
                $skip = $limit*$pageNo;
                $query= $query->skip($skip)->limit($limit);
            }
            $data = $query->get();
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

    //create product images
    public function createProductImages(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_id' => 'required|numeric',
                'image' => 'required|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'product_variable_id'=>'numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $product = Products::find($request->product_id);
            if(!is_null($product)){
                $newProductVariable = new ProductImages();

                $newProductVariable->product_id = $request->product_id;
                $newProductVariable->imagePath =$this->saveImage($request->image) ;
                $newProductVariable->product_variable_id = $request->has('product_variable_id')?$request->product_variable_id:null;


                if($newProductVariable->save()){
                    return $this->sendResponse([],'Product Image Uploaded Successfully.', true);
                }else{
                    if(file_exists(public_path().$newProductVariable->imagePath)){
                        unlink(public_path().$newProductVariable->imagePath);
                    }
                    return $this->sendError('Product Image Uploading Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut With id '.$request->product_id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    public function updateProductImages(Request $request,$id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_id' => 'numeric',
                'image' => 'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'product_variable_id'=>'numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newProductVariable = ProductImages::find($id);

            if(!is_null($newProductVariable)){


                if($request->has('product_id')){
                    $product = Products::find($request->product_id);
                    if(!is_null($product)){
                        $newProductVariable->product_id = $request->product_id;
                    }else{
                        return $this->sendError('No Prodcut Variable With id '.$request->product_id.' available',[], 200);
                    }
                }
                if($request->hasFile('image')){
                    $oldImage = $newProductVariable->imagePath;
                    $newProductVariable->imagePath = $this->saveImage($request->image);
                    if(file_exists(public_path().$oldImage)){
                        unlink(public_path().$oldImage);
                    }
                }

                $newProductVariable->product_variable_id = $request->has('product_variable_id')?$request->product_variable_id:$newProductVariable->product_variable_id;

                if($newProductVariable->save()){
                    return $this->sendResponse([],'Product Images Updated Successfully.', true);
                }else{
                    if(file_exists(public_path().$newProductVariable->imagePath)){
                        unlink(public_path().$newProductVariable->imagePath);
                    }
                    return $this->sendError('Product Images Updation Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut Variable With id '.$id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    public function deleteProductImages(Request $request,$id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newProductVariable = ProductImages::find($id);

            if(!is_null($newProductVariable)){
                if($newProductVariable->delete()){
                    return $this->sendResponse([],'Product Images Deleted Successfully.', true);
                }else{

                    return $this->sendError('Product Images Deletion Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut Variable With id '.$id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }


    //product review
    public function getProductReview(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'numeric',
                'limit'=>'numeric',
                'product_id' => 'required|numeric',
                'user_id'=>'numeric',
                'review_id'=>'numeric'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $query = ProductReview::query()->whereProductId($request->product_id);
            if($request->has('user_id')){
                $query =$query->where('user_id',$request->user_id);
            }
            if($request->has('review_id')){
                $query =$query->where('id',$request->review_id);
            }
            if($request->has('pageNo') && $request->has('limit')){
                $limit = $request->limit;
                $pageNo = $request->pageNo;
                $skip = $limit*$pageNo;
                $query= $query->skip($skip)->limit($limit);
            }
            $data = $query->with('userInfo')->get();
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

    //create product images
    public function createProductReview(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'product_id' => 'required|numeric',
                'image' => 'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'comment'=>'string|required',
                'rating'=>'numeric|max:5'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $productId = $request->product_id;
            $order = Order::where('user_id',Auth::user()->id)->whereHas('orderItems', function ($query) use($productId){
                $query->where('product_id', $productId);
            })->first();
            if(is_null($order)){
                return $this->sendError('You Haven\'t purchased this product yet.',[], 200);
            }

            $product = Products::find($request->product_id);
            if(!is_null($product)){
                $newProductVariable = new ProductReview();
                $newProductVariable->product_id = $request->product_id;
                $newProductVariable->comment =$request->comment ;
                $newProductVariable->rating = $request->rating;
                $newProductVariable->user_id = Auth::user()->id;
                if($request->hasFile('image')){
                    $oldImage = $newProductVariable->imagePath;
                    $newProductVariable->imagePath = $this->saveReviewImage($request->image);
                    $newProductVariable->is_pic_available = true;

                }
                $newProductVariable->save();
                $avg = ProductReview::whereProductId($request->product_id)->avg('rating');
                $product->avg_rating = $avg;
                $product->save();

                if($newProductVariable->save()){
                    return $this->sendResponse([],'Product Review Uploaded Successfully.', true);
                }else{
                    if(file_exists(public_path().$newProductVariable->imagePath)){
                        unlink(public_path().$newProductVariable->imagePath);
                    }
                    return $this->sendError('Product Review Uploading Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut With id '.$request->product_id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    public function updateProductReview(Request $request,$id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

                'image' => 'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'comment'=>'string',
                'rating'=>'numeric|max:5'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newProductVariable = ProductReview::find($id);

            if(!is_null($newProductVariable)){
                if($request->hasFile('image')){
                    $oldImage = $newProductVariable->imagePath;
                    $newProductVariable->imagePath = $this->saveReviewImage($request->image);
                    $newProductVariable->is_pic_available = true;
                    if(file_exists(public_path().$oldImage)){
                        unlink(public_path().$oldImage);
                    }
                }

                $newProductVariable->comment = $request->has('comment')?$request->comment:$newProductVariable->comment;
                $newProductVariable->rating = $request->has('rating')?$request->rating:$newProductVariable->rating;
                $newProductVariable->save();
                $product = Products::find($newProductVariable->product_id);
                $avg = ProductReview::whereProductId($newProductVariable->product_id)->avg('rating');
                $product->avg_rating = $avg;
                $product->save();
                if($newProductVariable->save()){
                    return $this->sendResponse([],'Product Review Updated Successfully.', true);
                }else{
                    if(file_exists(public_path().$newProductVariable->imagePath)){
                        unlink(public_path().$newProductVariable->imagePath);
                    }
                    return $this->sendError('Product Review Updation Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut Review With id '.$id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    public function deleteProductReview(Request $request,$id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newProductVariable = ProductReview::find($id);

            if(!is_null($newProductVariable)){
                if($newProductVariable->delete()){
                    return $this->sendResponse([],'Product Review Deleted Successfully.', true);
                }else{

                    return $this->sendError('Product Review Deletion Failed',[], 200);
                }
            }
            else{
                return $this->sendError('No Prodcut Variable With id '.$id.' available',[], 200);
            }




        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }

    }

    function saveImage($image){
        $image_name = 'product'.time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/product/');
        $image->move($destinationPath, $image_name);
        $imageURL='/images/product/'.$image_name;
        return $imageURL;
    }

    function saveReviewImage($image){
        $image_name = 'productReview'.time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/productReview/');
        $image->move($destinationPath, $image_name);
        $imageURL='/images/productReview/'.$image_name;
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
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
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


        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
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
            if($request->has('pageNo') && $request->has('limit')){
                $limit = $request->limit;
                $pageNo = $request->pageNo;
                $skip = $limit*$pageNo;
                $query= $query->skip($skip)->limit($limit);
            }
            $data = $query->get();
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


        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getSingleProductInfo(Request $request,$id)
    {
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [

            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $prodcutDescription = Products::find($id);
            if(!is_null($prodcutDescription)){
                $productData = null;
                $productData = Products::whereId($id)
                    ->with(['productDescriptions','productVariables','productImages','productVariables.productVariablesImages'])
                    ->first();
                if(!is_null(UserWhishlist::whereUserId(Auth::user()->id)->whereProductId('$id')->first())){
                    $productData['isInUserWishList']=true;
                }else{
                    $productData['isInUserWishList']=false;
                }
                if($productData){
                    return $this->sendResponse($productData,'Product Description Deleted Successfully', true);
                }else{
                    return $this->sendError('Product Description Deletion Failed', [],200);
                }
            }else{
                return $this->sendError('No Product Description Found', [],200);
            }


        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getNewProducts(Request $request){
        try{
            $categories = Category::whereNull('parent_id')->get();
            foreach ($categories as $category) {
                $productArray=[];
                $products  = Products::where('is_new',true)->whereHas('categories', function ($query) use($category){
                    $query->where('category_id', $category['id']);
                })->limit(50)->orderBy('created_at','DESC')->get()->toArray();

                if(count($products)<50){
                    $products2  = Products::where('is_new',false)->whereHas('categories', function ($query) use($category){
                        $query->where('category_id', $category['id']);
                    })->limit(50)->orderBy('created_at','DESC')->get()->toArray();

                    $productArray = array_merge($products,$products2);
                }else{
                    $productArray = array_merge($products,[]);
                }
                $category['products']=$productArray;
            }
            if(count($categories)>0){
                $response =  $categories;
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }
        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getSizeColorData(Request $request){
        try{
            $sizeData = null;
            $colorData = null;
            $typeData = null;
            $colorData = ProductVariables::select('color')->distinct()->pluck('color');
            $sizeData = ProductVariables::select('size')->distinct()->pluck('size');
            $typeData = ProductVariables::select('type')->distinct()->pluck('type');
            $response['colorData'] =  $colorData;
            $response['sizeData'] =  $sizeData;
            $response['typeData'] =  $typeData;

            return $this->sendResponse($response,'Data Fetched Successfully', true);
        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getRecommendedProducts(Request $request){
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
            $productIdsUserWishlist = UserWhishlist::where('user_id',$user->id)->pluck('product_id')->toArray();
            $productIdsUserActivity = UserActivity::where('user_id',$user->id)->pluck('product_id')->toArray();
            $productIds = array_merge($productIdsUserActivity,$productIdsUserWishlist);
            $categoryIds = ProductHasCategory::whereIn('product_id',$productIds)->pluck('category_id')->toArray();
            $query = Products::query();
            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $query->whereHas('categories', function ($query) use($categoryIds){
                $query->whereIn('category_id', $categoryIds);
            });
            $products= $query->skip($skip)->limit($limit)->orderBy('sellCount','DESC')->get();
            if(count($products)<1){
                $products = Products::inRandomOrder()->limit($limit)->get();
            }
            foreach($products as $key=>$product){
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
            if(count($products)>0){
                $response =  $products;
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }
        }
        catch(\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function searchProductByName(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                "searchTerm" => 'required|string',
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $products = null;
            $query = Products::query();
            $searchString = $request->searchTerm;
            $query=$query->where('product_name','like','%' .$searchString. '%')->orWhere('description','like','%' .$searchString. '%');
            $query=$query->orWhereHas('categories', function ($query) use($searchString){
                $query->where('category_name', 'like','%' .$searchString. '%');
            });
            $query =$query->orWhereHas('productDescriptions', function ($query) use($searchString){
                $query->where('property_value', 'like','%' .$searchString. '%');
            });
            $query =$query->orWhereHas('productDescriptions', function ($query) use($searchString){
                $query->where('property_name', 'like','%' .$searchString. '%');
            });

            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $products= $query->skip($skip)->limit($limit)->get();
            foreach($products as $key=>$product){
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
            if(count($products)>0){
                $response =  $products;
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }

        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }

    public function getRandomProducts(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'category_id'=>'numeric',
                'sub_category_id'=>'numeric',
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $products = null;

            $query = Products::query();
            if($request->has('category_id')){
                $categoryId = $request->category_id;
                $query->whereHas('categories', function ($query) use($categoryId){
                    $query->where('category_id', $categoryId);
                });
            }
            if($request->has('sub_category_id')){
                $subCategoryId = array_map('intval',explode(',',$request->sub_category_id));
                $query->whereHas('subCategories', function ($query) use($subCategoryId){
                    $query->whereIn('sub_category_id', $subCategoryId);
                });
            }



            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $products= $query->inRandomOrder()->limit($limit)->get();
            foreach($products as $key=>$product){
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
            if(count($products)>0){
                $response =  $products;
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }

        }
        catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }
}
