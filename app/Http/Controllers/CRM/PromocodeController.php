<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
use App\Models\DeliveryPincode;
use App\Models\PromocodeHasPincode;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;

class PromocodeController extends Controller{

    public function index(Request $request){
        // $promocodes = DB::table('promocodes')
        // ->where('is_active', 1)->get();
        $promocodes = Promocode::all();
        return view('admin.promocode.index',compact(['promocodes']));

    }

    public function show(Request $request, $id){
        $promocode = Promocode::find($id);
        return view('admin.promocode.show',compact(['promocode']));

    }

    public function edit(Request $request, $id){
        $promocode = Promocode::find($id);
        $pincodes = DeliveryPincode::all();
        $promo_pincodes = PromocodeHasPincode::
        where("promo_id", "=", $promocode->id)->select('pincode_id')->pluck('pincode_id');
        return view('admin.promocode.edit',compact(['promocode','pincodes','promo_pincodes']));
    }

    public function create(Request $request){
        $pincodes = DeliveryPincode::all();
        return view('admin.promocode.create',compact(['pincodes']));
    }

    public function store(Request $request){
        try {
            $request->validate([
                'promocode' => 'required|string|unique:promocodes',
                'type'=>'required',
                'discount' => 'required|numeric',
                'minimal_cart_total'=>'required|numeric',
                'max_discount'=>'required|numeric',
                'is_for_new_user'=>'required|boolean',
                'start_from'=>'required|date|after:sysdate',
                'end_on'=>'required|date|after:start_from',
                'description'=>'nullable',
                'is_for_registered_between'=>'required|boolean',
                'registered_from'=>'required_if:is_for_registered_between,==,1|date',
                 'registered_till'=>'required_if:is_for_registered_between,==,1|date|after:registered_from',
                'is_for_specific_pincode'=>'required|boolean',
                'pincodes'=>'required_if:is_for_specific_pincode,==,1',
                'is_active'=>'required|boolean'
            ]);

            $newPromo = new Promocode;
            $newPromo->promocode=$request->promocode;
            $newPromo->discount=$request->discount;
            $newPromo->minimal_cart_total=$request->minimal_cart_total;
            $newPromo->max_discount=$request->max_discount;
            $newPromo->is_for_registered_between=$request->is_for_registered_between;
            $newPromo->is_for_specific_pincode=$request->is_for_specific_pincode;
            $newPromo->start_from=Carbon::parse($request->start_from)->format('Y-m-d H:i:s');
            $newPromo->end_on=Carbon::parse($request->end_on)->format('Y-m-d H:i:s');
            if ($request->is_for_registered_between == 1) {
                $newPromo->registered_from=Carbon::parse($request->registered_from)->format('Y-m-d H:i:s');
                $newPromo->registered_till=Carbon::parse($request->registered_till)->format('Y-m-d H:i:s');
            }
            $newPromo->description=$request->has('description')?$request->description:null;
            if($request->type == 1){
                $newPromo->type = "percentage";
            }
            elseif ($request->type == 0){
                $newPromo->type = "flat";
            }
            if($request->is_for_new_user == 1){
                $newPromo->is_for_new_user = true;
            }
            elseif ($request->is_for_new_user == 0){
                $newPromo->is_for_new_user = false;
            }
            if($request->is_active == 1){
                $newPromo->is_active = true;
            }
            elseif ($request->is_active == 0) {
                $newPromo->is_active = false;
            }

            $newPromo->save();
            //dd($request->pincodes);

            if ($request->is_for_specific_pincode == 1) {
                foreach($request->pincodes as $key=>$pincode){
                    $promoHasPin = new PromocodeHasPincode;
                    $promoHasPin->promo_id=$newPromo->id;
                    $promoHasPin->pincode_id=$pincode;
                    $promoHasPin->save();
                }                
            }
            if($newPromo->save()){
                return redirect()->route('promocode.index')
                        ->with('success','Promocode created successfully.');
            }else{
                return $this->sendError('Promocode Creation Failed',[], 422);
            }

        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }


    public function update(Request $request, $id){
        try {
            $request->validate([                
                'promocode' => 'nullable|string',
                'type'=>'nullable|string',
                'discount' => 'nullable|numeric',
                'minimal_cart_total'=>'nullable|numeric',
                'max_discount'=>'nullable|numeric',
                'is_for_new_user'=>'nullable|boolean',
                'start_from'=>'nullable|date|after:today',
                'end_on'=>'nullable|date|after:start_from',
                'description'=>'nullable|string',
                'is_for_registered_between'=>'nullable|boolean',
                'registered_from'=>'nullable|date',
                'registered_till'=>'nullable|date|after:registered_from',
                'is_for_specific_pincode'=>'nullable|boolean',
                'is_active'=>'nullable|boolean'
            ]);
            $promocode=Promocode::find($id);

            if(!is_null($promocode)){
                $promocode->promocode=$request->has('promocode')?$request->promocode:$promocode->promocode;
                $promocode->discount=$request->has('discount')?$request->discount:$promocode->discount;
                $promocode->minimal_cart_total=$request->has('minimal_cart_total')?$request->minimal_cart_total:$promocode->minimal_cart_total;
                $promocode->max_discount=$request->has('max_discount')?$request->max_discount:$promocode->max_discount;
                $promocode->description=$request->has('description')?$request->description:$promocode->description;
                if($request->has('start_from')){
                    $promocode->start_from = Carbon::parse($request->start_from)->format('Y-m-d H:i:s'); 
                }
                if($request->has('end_on')){
                    $promocode->end_on = Carbon::parse($request->end_on)->format('Y-m-d H:i:s'); 
                }
                if($request->has('type')){
                    if($request->type == 1){
                        $promocode->type = "percentage";
                    }
                    elseif ($request->type == 0){
                        $promocode->type = "flat";
                    } 
                }
                if($request->has('is_for_new_user')){
                    if($request->is_for_new_user == 1){
                        $promocode->is_for_new_user = true;
                    }
                    elseif ($request->is_for_new_user == 0){
                        $promocode->is_for_new_user = false;
                    } 
                }
                if($request->has('is_active')){
                    if($request->is_active == 1){
                        $promocode->is_active = true;
                    }
                    elseif ($request->is_active == 0){
                        $promocode->is_active = false;
                    } 
                }
                $promocode->is_for_registered_between=$request->has('is_for_registered_between')?$request->is_for_registered_between:$promocode->is_for_registered_between;
                if ($request->is_for_registered_between == 1) {
                    $newPromo->registered_from=Carbon::parse($request->registered_from)->format('Y-m-d H:i:s');
                    if($request->has('end_on')){
                        $newPromo->registered_till=Carbon::parse($request->registered_till)->format('Y-m-d H:i:s');
                    }
                    
                }
                $promocode->is_for_specific_pincode=$request->has('is_for_specific_pincode')?$request->is_for_specific_pincode:$promocode->is_for_specific_pincode;
                $promocode->save();
                if ($request->is_for_specific_pincode == 1) {
                    foreach($request->pincodes as $key=>$pincode){
                        $promoHasPin = new PromocodeHasPincode;
                        $promoHasPin->promo_id=$promocode->id;
                        $promoHasPin->pincode_id=$pincode;
                        $promoHasPin->save();
                    }  
                }
                              
                if($promocode->save()){
                    return redirect()->route('promocode.index')
                        ->with('success','Promocode created successfully.');
                }else{
                    return $this->sendError('Promocode Updation Failed',[], 422);
                }

            }else{
                return $this->sendError('Promocode Does Not Exist.',[], 422);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }
}
