<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPincode;
use Illuminate\Http\Request;
use DB;
use Validator;

class PincodeController extends Controller{

    public function index(Request $request){

        $pincodes = DeliveryPincode::all();
        return view('admin.pincode.index',compact(['pincodes']));

    }

    public function show(Request $request, $id){

        $pincode = DeliveryPincode::find($id);
        return view('admin.pincode.show',compact(['pincode']));

    }

    public function edit(Request $request, $id){
        $pincode = DeliveryPincode::find($id);
        return view('admin.pincode.edit',compact(['pincode']));
    }

    public function create(Request $request){
        return view('admin.pincode.create');
    }

    public function store(Request $request){
        try {
            $request->validate([
                'pincode' => 'required|digits:6',
                'is_active'=>'required'                
            ]);

            $newPincode = new DeliveryPincode;
            $newPincode->pincode=$request->pincode;
            if($request->is_active == 1){
                $newPincode->is_active = true;
            }
            elseif ($request->is_active == 0) {
                $newPincode->is_active = false;
            }
            if($newPincode->save()){
                return redirect()->route('pincode.index')
                        ->with('success','Pincode created successfully.');
            }else{        
                return $this->sendError('Pincode Creation Failed',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }


    public function update(Request $request, $id){
        try {
            $request->validate([
                'pincode' => 'nullable|digits:6',
                'is_active'=>'nullable' 
            ]);
            $pincode=DeliveryPincode::find($id);
            if(!is_null($pincode)){
                $pincode->pincode=$request->pincode;
                if($request->is_active == 1){
                    $pincode->is_active = true;
                }
                elseif ($request->is_active == 0) {
                    $pincode->is_active = false;
                }
                if($pincode->save()){
                    return redirect()->route('pincode.index')
                        ->with('success','Pincode created successfully.');
                }else{
                    return $this->sendError('Pincode Updation Failed',[], 422);
                }

            }else{
                return $this->sendError('Pincode Does Not Exist.',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function destroy(Request $request, $id)
    {
        DeliveryPincode::find($id)->delete();
        return redirect()->route('pincode.index')
                        ->with('success','Pincode deleted successfully');
    }
}
