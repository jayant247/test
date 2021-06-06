<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductVariables;
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
        return view('admin.products.show',compact(['product']));
    }

    public function edit(Request $request, $id){

       dd('edit');

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
}
