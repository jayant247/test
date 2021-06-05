<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Promocode;
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
        return view('admin.promocode.edit',compact(['promocode']));
    }

    public function create(Request $request){
        return view('admin.promocode.create');
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
                'is_active'=>'required|boolean'

            ]);

            $newPromo = new Promocode;
            $newPromo->promocode=$request->promocode;
            //$newPromo->type=$request->type;
            $newPromo->discount=$request->discount;
            $newPromo->minimal_cart_total=$request->minimal_cart_total;
            $newPromo->max_discount=$request->max_discount;
            // $newPromo->start_from=$request->start_from;
            // $newPromo->end_on=$request->end_on;
            $newPromo->start_from=Carbon::parse($request->start_from)->format('Y-m-d H:i:s');
            $newPromo->end_on=Carbon::parse($request->end_on)->format('Y-m-d H:i:s');
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
                'promocode' => 'required|string',
                //'type'=>'string',
                'discount' => 'required|numeric',
                'minimal_cart_total'=>'required|numeric',
                'max_discount'=>'required|numeric',
                //'is_for_new_user'=>'boolean',
                //'start_from'=>'date|after:today',
                //'end_on'=>'date|after:start_from',
                'description'=>'nullable|string',
                //'is_active'=>'boolean'
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
