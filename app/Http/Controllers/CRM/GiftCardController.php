<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;


class GiftCardController extends Controller{

    public function index(Request $request){

        $giftcards = GiftCard::orderBy('id','DESC')->get();
        return view('admin.giftcard.index',compact(['giftcards']));

    }

    public function show(Request $request, $id){

        $giftcard = GiftCard::find($id);
        return view('admin.giftcard.show',compact(['giftcard']));

    }

    public function edit(Request $request, $id){
        $giftcard = GiftCard::find($id);
        return view('admin.giftcard.edit',compact(['giftcard']));
    }

    public function create(Request $request){
        return view('admin.giftcard.create');
    }

    public function store(Request $request){
        try {
            $request->validate([
                'title' => 'required|string',
                'description'=>'nullable|string',
                'purchase_amount' => 'required|numeric|min:0',
                'gift_amount' => 'required|numeric|min:0',
                'validity_days_from_purchase_date' => 'required|numeric|min:0',
                'start_from' => 'required|date|after:today',
                'end_on' => 'required|after:start_from',
                'is_active' => 'required|'
            ]);

            $newGiftcard = new GiftCard;
            $newGiftcard->title=$request->title;
            $newGiftcard->purchase_amount=$request->purchase_amount;
            $newGiftcard->gift_amount=$request->gift_amount;
            $newGiftcard->validity_days_from_purchase_date=$request->validity_days_from_purchase_date;
            $newGiftcard->start_from=Carbon::parse($request->start_from)->format('Y-m-d H:i:s');
            $newGiftcard->end_on=Carbon::parse($request->end_on)->format('Y-m-d H:i:s');
            $newGiftcard->description=$request->has('description')?$request->description:null;
            if($request->is_active == 1){
                $newGiftcard->is_active = true;
            }elseif ($request->is_active == 0) {
                $newGiftcard->is_active = false;
            }
            if($newGiftcard->save()){
                return redirect()->route('giftcard.index')
                        ->with('success','Giftcard created successfully.');
            }else{
                return $this->sendError('Giftcard Creation Failed',[], 422);
            }

        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }


    public function update(Request $request, $id){
        try {
            $request->validate([
                'title' => 'nullable|string',
                'description'=>'nullable|string',
                'purchase_amount' => 'nullable|numeric|min:0',
                'gift_amount' => 'nullable|numeric|min:0',
                'validity_days_from_purchase_date' => 'nullable|numeric|min:0',
                'start_from' => 'nullable|date|after:today',
                'end_on' => 'nullable|after:start_from',
                'is_active' => 'nullable'
            ]);


            $giftcard=GiftCard::find($id);

            if(!is_null($giftcard)){
                $giftcard->title=$request->has('title')?$request->title:$giftcard->title;
                $giftcard->description=$request->has('description')?$request->description:$giftcard->description;
                $giftcard->purchase_amount=$request->has('purchase_amount')?$request->purchase_amount:$giftcard->purchase_amount;
                $giftcard->gift_amount=$request->has('gift_amount')?$request->gift_amount:$giftcard->gift_amount;
                $giftcard->validity_days_from_purchase_date=$request->has('validity_days_from_purchase_date')?$request->validity_days_from_purchase_date:$giftcard->validity_days_from_purchase_date;
                if($request->has('start_from')){
                    $giftcard->start_from = Carbon::parse($request->start_from)->format('Y-m-d H:i:s');
                }
                if($request->has('end_on')){
                    $giftcard->end_on = Carbon::parse($request->end_on)->format('Y-m-d H:i:s');
                }
                if($request->has('is_active')){
                    if($request->is_active == 1){
                        $giftcard->is_active = true;
                    }
                    elseif ($request->is_active == 0){
                        $giftcard->is_active = false;
                    }
                }
                if($giftcard->save()){
                    return redirect()->route('giftcard.index')
                        ->with('success','Giftcard created successfully.');
                }else{
                    return $this->sendError('Giftcard Updation Failed',[], 422);
                }

            }else{
                return $this->sendError('Giftcard Does Not Exist.',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function destroy(Request $request, $id)
    {
        GiftCard::find($id)->delete();
        return redirect()->route('giftcard.index')
                        ->with('success','Giftcard deleted successfully');
    }
}
