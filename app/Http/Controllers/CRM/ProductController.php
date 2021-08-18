<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductHasCategory;
use App\Models\ProductDescription;
use App\Models\ProductVariables;
use App\Models\ProductImages;
use App\Models\UserWhishlist;
use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;


class ProductController extends Controller{

    public function index(Request $request){
        $products = Products::all();
        return view('admin.products.index',compact(['products']));
    }

    public function show(Request $request, $id){
        // dd('show');
        $product = Products::find($id);
        $productDescriptions = ProductDescription::where("product_id", "=", $product->id)->get();
        $productImages = ProductImages::where("product_id", "=", $product->id)->get();
        //dd($productImages);
        $productVariables = ProductVariables::where("product_id", "=", $product->id)->get();
        //$productImages = ProductImages::where('product_id', '=', $id)->get();
        return view('admin.products.show',compact(['product','productDescriptions','productVariables','productImages']));
    }

    public function edit(Request $request, $id){
        $product = Products::find($id);
        //dd($product);
        $categories = Category::all();
        $product_categories = ProductHasCategory::
        where("product_id", "=", $product->id)->select('category_id')->distinct()->pluck('category_id');
        $product_sub_categories = ProductHasCategory::
        where("product_id", "=", $product->id)->select('sub_category_id')->distinct()->pluck('sub_category_id');
        return view('admin.products.edit',compact(['product','categories','product_categories','product_sub_categories']));
    }

    public function create(Request $request){
        $categories = Category::whereNull('parent_id')->get();

        return view('admin.products.create',compact(['categories']));
    }

    public function store(Request $request){
        try {
            $request->validate([
                'product_name'=>'required|string',
                'price'=>'required|numeric|min:0',
                'mrp' => 'required|numeric|min:0',

                'sale_price'=>'required_if:is_on_sale,"1"|numeric|min:0',
                'sale_percentage'=>'required_if:is_on_sale,"1"|numeric|min:0|max:100',
                'is_on_sale'=>'required|boolean',

                'primary_image'=>'required|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'other_images.*'=>'nullable|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'is_new'=>'required|boolean',
                'is_live'=>'required|boolean',
                'description'=>'nullable|string'
            ]);

            //dd($request->all());

            $newProduct = new Products;
            $newProduct->product_name=$request->product_name;
            $newProduct->price=$request->price;
            $newProduct->mrp=$request->mrp;
            $newProduct->available_sizes="NA";
            $newProduct->available_colors="NA";
            $newProduct->description=$request->has('description')?$request->description:null;
            $newProduct->is_new=$request->is_new;
            $newProduct->is_live=$request->is_live;
            $newProduct->is_on_sale=$request->is_on_sale;
            if($request->is_on_sale){
                $newProduct->sale_price=$request->has('sale_price')?$request->sale_price:0;
                $newProduct->sale_percentage=$request->has('sale_percentage')?$request->sale_percentage:0;
            }else{
                $newProduct->sale_price=0;
                $newProduct->sale_percentage=0;
            }
            $newProduct->primary_image =$this->saveImage($request->primary_image);
            $newProduct->save();

            if($request->has('other_images')){
                 foreach ($request->other_images as $image) {
                    $productImages = new ProductImages;
                    $productImages->product_id = $newProduct->id;
                    $productImages->imagePath = $this->saveImage($image);
                    $productImages->save();
                }
            }

            foreach ($request->subCategories as $key=>$subCategory){
                $originalSubcategory = Category::find($subCategory);
                if(!is_null($originalSubcategory)){
                    if(is_null(ProductHasCategory::where('product_id',$newProduct->id)->where('sub_category_id',$subCategory)->first())){
                        $newCategoryMapping = new ProductHasCategory();
                        $newCategoryMapping->product_id = $newProduct->id;
                        $newCategoryMapping->category_id = $originalSubcategory->parent_id;
                        $newCategoryMapping->sub_category_id = $subCategory;
                        $newCategoryMapping->save();
                    }
                }

            }

            if($newProduct->save()){
                return redirect()->route('product.index')
                        ->with('success','Product created successfully.');
            }else{
                return $this->sendError('Product Creation Failed',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function update(Request $request, $id){
        try {
            $request->validate([
                'product_name'=>'nullable|string',
                'price'=>'nullable|numeric|min:0',
                'mrp' => 'nullable|numeric|min:0',
                'sale_price'=>'nullable|numeric|min:0',
                'sale_percentage'=>'nullable|numeric|min:0|max:100',
                'is_on_sale'=>'nullable|boolean',
                'primary_image'=>'nullable|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'other_images.*'=>'nullable|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'is_new'=>'nullable|boolean',
                'is_live'=>'nullable|boolean',
                'description'=>'nullable|string'
            ]);
            //dd($request->all());
            $product=Products::find($id);
            $product->product_name=$request->has('product_name')?$request->product_name:null;
            $product->mrp=$request->has('mrp')?$request->mrp:null;
            $product->price=$request->has('price')?$request->price:null;
            $product->description=$request->has('description')?$request->description:null;
            $product->is_new = $request->has('is_new')? $request->is_new:$product->is_new;
            $product->is_live = $request->has('is_live')? $request->is_live:$product->is_live;
            $product->is_on_sale = $request->has('is_on_sale')? $request->is_on_sale:$product->is_on_sale;
            $product->sale_price=$request->has('sale_price')?$request->sale_price:null;
            $product->sale_percentage=$request->has('sale_percentage')?$request->sale_percentage:null;
            if($request->hasFile('primary_image')){
                    $oldImage = $product->primary_image;
                    $product->primary_image = $this->saveImage($request->primary_image);
                    unlink(public_path().$oldImage);
                }
            $product->save();

            if($request->has('other_images')){
                 foreach ($request->other_images as $image) {
                    $productImages = new ProductImages;
                    $productImages->product_id = $product->id;
                    $productImages->imagePath = $this->saveImage($image);
                    $productImages->save();
                }
            }

            foreach ($request->subCategories as $key=>$subCategory){
                $originalSubcategory = Category::find($subCategory);
                if(!is_null($originalSubcategory)){
                    if(is_null(ProductHasCategory::where('product_id',$product->id)->where('sub_category_id',$subCategory)->first())){
                        $newCategoryMapping = new ProductHasCategory();
                        $newCategoryMapping->product_id = $product->id;
                        $newCategoryMapping->category_id = $originalSubcategory->parent_id;
                        $newCategoryMapping->sub_category_id = $subCategory;
                        $newCategoryMapping->save();
                    }
                }

            }
            if($product->save()){
                return redirect()->route('product.index')
                        ->with('success','Product Updated successfully.');
            }else{
                return $this->sendError('Product Updation Failed',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    function saveImage($image){
        $image_name = 'product'.time().'-'.rand(10,1000).'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/product/');
        $image->move($destinationPath, $image_name);
        $imageURL='/images/product/'.$image_name;
        return $imageURL;
    }

    public function getProductList(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'pageNo'=>'required|numeric',
                'limit'=>'required|numeric',
                'fields'=>'string',
                'product_name'=>'string',
                'sub_category_id' => 'string',
                'category_id'=>'numeric',
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
            if($request->has('category_id')){
                $categoryId = array_map('intval',explode(',',$request->category_id));;
                $query->whereHas('categories', function ($query) use($categoryId){
                    $query->whereIn('category_id', $categoryId);
                });
            }

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
                $query =  $query->whereBetween('price',[$low,$high]);
            }
            $count = $query->count();
            $limit = $request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $query= $query->skip($skip)->limit($limit);
            $data = $query->orderBy('id','DESC')->get();


            if(count($data)>0){
                $response =  ['products'=>$data,'count'=>$count];
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getMessage()],413);
        }
    }
    public function deleteProduct($id)
    {
        Products::find($id)->delete();
        return redirect()->route('product.index')
                        ->with('success','product Variable deleted successfully');
    }
}
