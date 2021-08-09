<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;
use App\Models\ProductDescription;
use App\Models\Products;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;


class ProductDescriptionController extends Controller{

/*    public function index(Request $request){

        $giftcards = GiftCard::orderBy('id','DESC')->get();
        return view('admin.giftcard.index',compact(['giftcards']));

    }

    public function show(Request $request, $id){

        $giftcard = GiftCard::find($id);
        return view('admin.giftcard.show',compact(['giftcard']));

    }*/

    public function edit(Request $request, $id){
        //dd("edit");
        $productDescription = ProductDescription::find($id);
        return view('admin.productDescription.edit',compact(['productDescription']));
    }

    public function createNewProductDescription($id)
    {
        $product = DB::table('products')->find($id);

        return view('admin.productDescription.create',compact(['product']));
    }

    /*public function create(Request $request, $id){
        //$product = Products::find($request->id);
        dd($id);
        return view('admin.productDescription.create',compact(['product']));
    }*/

    public function store(Request $request){
        try {
            $request->validate([
                'property_name' => 'required|string',
                'property_value'=>'required|string'
            ]);

            $newProdcutDescription = new ProductDescription;
            $newProdcutDescription->property_name=$request->property_name;
            $newProdcutDescription->property_value=$request->property_value;
            $newProdcutDescription->product_id=$request->product_id;
            if($newProdcutDescription->save()){
                return redirect()->route('product.show',$newProdcutDescription->product_id)
                        ->with('success','Product Description created successfully.');
            }else{
                return $this->sendError('Product Description Creation Failed',[], 422);
            }

        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }


    public function update(Request $request, $id){
        try {
            $request->validate([
                'property_name' => 'nullable|string',
                'property_value'=>'nullable|string'
            ]);

            $productDescription=ProductDescription::find($id);

            if(!is_null($productDescription)){
                $productDescription->property_name=$request->has('property_name')?$request->property_name:$productDescription->property_name;
                $productDescription->property_value=$request->has('property_value')?$request->property_value:$productDescription->property_value;
                if($productDescription->save()){
                    return redirect()->route('product.show',$productDescription->product_id)
                        ->with('success','Product Description updated successfully.');
                }else{
                    return $this->sendError('Product Description Updation Failed',[], 422);
                }

            }else{
                return $this->sendError('Product Description Does Not Exist.',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function destroy(Request $request, $id)
    {
        productDescription::find($id)->delete();
        return redirect()->route('product.index')
                        ->with('success','product Description deleted successfully');
    }
}
