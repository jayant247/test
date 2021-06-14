<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\ProductVariables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;


class ProductVariableController extends Controller{

    /*public function index(Request $request){

        $productVariables = ProductVariables::orderBy('id','DESC')->get();
        return view('admin.productVariable.index',compact(['productVariables']));

    }

    public function show(Request $request, $id){

        $productVariable = ProductVariables::find($id);
        return view('admin.productVariable.show',compact(['productVariable']));

    }*/

    public function edit(Request $request, $id){
        //dd("edit");
        $productVariable = ProductVariables::find($id);
        return view('admin.productVariable.edit',compact(['productVariable']));
    }

    public function createNewProductVariable($id)
    {
        $product = DB::table('products')->find($id);

        return view('admin.productVariable.create',compact(['product']));
    }

    // public function create(Request $request){
    //     return view('admin.productVariable.create');
    // }

    public function store(Request $request){
        try {
            $request->validate([
                'color' => 'required|string',
                'size'=>'required|string',
                'price' => 'required|numeric',
                'mrp' => 'required|numeric',
                'is_on_sale' => 'required|boolean',
                'sale_price' => 'nullable|numeric',
                'sale_percentage' => 'nullable|numeric',                
                'primary_image'=>'nullable|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'quantity' => 'required|numeric',
                'type' => 'nullable|string'
            ]);

            $newProductVariable = new ProductVariables;
            $newProductVariable->color=$request->color;
            $newProductVariable->size=$request->size;
            $newProductVariable->price=$request->price;           
            $newProductVariable->mrp=$request->mrp;
            $newProductVariable->quantity=$request->quantity;
            $newProductVariable->primary_image =$this->saveImage($request->primary_image);
            $newProductVariable->sale_price=$request->has('sale_price')?$request->sale_price:null;
            $newProductVariable->sale_percentage=$request->has('sale_percentage')?$request->sale_percentage:null;
            $newProductVariable->type=$request->has('type')?$request->type:null;
            if($request->is_on_sale == 1){
                $newProductVariable->is_on_sale = true;
            }elseif ($request->is_on_sale == 0) {
                $newProductVariable->is_on_sale = false;
            }

            $newProductVariable->product_id=$request->product_id;
            if($newProductVariable->save()){
                return redirect()->route('product.index')
                        ->with('success','Product Variable created successfully.');
            }else{        
                return $this->sendError('Product Variable Creation Failed',[], 422);
            }

        }
        catch (Exception $e){
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


    public function update(Request $request, $id){
        try {
            $request->validate([
                'color' => 'nullable|string',
                'size'=>'nullable|string',
                'price' => 'nullable|number',
                'mrp' => 'nullable|number',
                'is_on_sale' => 'nullable|boolean',
                'sale_price' => 'nullable|numeric',
                'sale_percentage' => 'nullable|numeric',
                'quantity' => 'nullable|numeric',
                'type' => 'nullable|string'
            ]);


            $productVariable=ProductVariables::find($id);

            if(!is_null($productVariable)){
                $productVariable->color=$request->has('color')?$request->color:$productVariable->color;
                $productVariable->size=$request->has('size')?$request->size:$productVariable->size;
                $productVariable->price=$request->has('price')?$request->price:$productVariable->price;
                $productVariable->mrp=$request->has('mrp')?$request->mrp:$productVariable->mrp;
                $productVariable->quantity=$request->has('quantity')?$request->quantity:$productVariable->quantity;
                $productVariable->sale_price=$request->has('sale_price')?$request->sale_price:null;
                $productVariable->sale_percentage=$request->has('sale_percentage')?$request->sale_percentage:null;
                $productVariable->type=$request->has('type')?$request->type:null;
                if($request->has('is_on_sale')){
                    if($request->is_on_sale == 1){
                        $productVariable->is_on_sale = true;
                    }
                    elseif ($request->is_on_sale == 0){
                        $productVariable->is_on_sale = false;
                    }
                }
                if($productVariable->save()){
                    return redirect()->route('productVariable.index')
                        ->with('success','Product Variable Updated successfully.');
                }else{
                    return $this->sendError('Product Variable Updation Failed',[], 422);
                }

            }else{
                return $this->sendError('Product Variable Does Not Exist.',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }
}
