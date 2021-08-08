<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Lcobucci\JWT\Exception;
use Validator;
use Redirect;

class OrderController extends Controller{

    public function orderindex(Request $request,$id){

        $filter = '';

        if($request->has('filter')){
            $searchQuery = $request->search;
            $orders = Order::sortable()
                ->orWhere('orderRefNo','like', '%' . $request->search. '%')
                ->orWhere('id','like', '%' . $request->search. '%')
                ->orWhere('total','like', '%' . $request->search. '%')
                ->orWhere('orderRefNo','like', '%' . $request->search. '%')
                ->with('addressDetails',function ($query,$searchQuery){
                    $query->orWhere('pincode','like', '%' . $searchQuery. '%')
                    ;
                })
                ->with(['customer','addressDetails'])->withCount(['orderItems'])->paginate(2);
        }else{
            $orders = Order::sortable()->with(['customer','addressDetails'])->withCount(['orderItems'])->paginate(5);
        }


        return view('admin.order.index',compact(['orders','filter','id']));

    }

    public function getOrders(Request  $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'payment_status'=>'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $limit = (int)$request->limit;
            $pageNo = $request->pageNo;
            $skip = $limit*$pageNo;
            $filtersVariable =$request->all();
            $query = Order::with(['orderStatus','customer','paymentStatus','orderItems','orderItems.productVariable','orderItems.productVariable.productDetails'])
                ->withCount(['orderItems'])
                ->where('payment_status',$request->payment_status)
                ->where('order_status',$request->order_status);

            $query = $query->with('addressDetails')->whereHas('addressDetails',function($qu) use ($request) {
                if($request->has('pincode')){
                    $qu->where('pincode',$request->pincode);
                }
            });
            $query = $query->with('customer')->whereHas('customer',function($qu) use ($request) {
                if($request->has('mobile_no')){
                    $qu->where('mobile_no','like','%'.$request->mobile_no.'%');
                }
            });
            if($request->has('startDate') && $request->has('endDate')){
                $query = $query->whereDate('created_at','<=',Carbon::createFromFormat('m/d/Y',$request->startDate));
                $query = $query->whereDate('created_at','>=',Carbon::createFromFormat('m/d/Y',$request->endDate));
            }
            if($request->has('order_ref')){
                $query =$query->where('order_ref','like','%'.$request->order_ref.'%');
            }
            $count = $query->count();

            $orders = $query->orderBy('id','DESC')->skip($skip)->limit($limit)->get()->toArray();

            if(count($orders)>0){
                return $this->sendResponse(['orders'=>$orders,'count'=>$count],'Data Fetched Successfully', true);
            }else{
                return $this->sendResponse(['orders'=>$orders,'count'=>$count],'No Orders Available available', false);
            }
        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', [$e->getTrace()],413);
        }
    }

    public function show(Request $request, $id){
        try{
            $order = Order::with(['orderStatus','customer','paymentStatus','orderItems','orderItems.productVariable','orderItems.productVariable.productDetails','addressDetails'])
                ->withCount(['orderItems'])->find($id);

            return view('admin.order.show',compact(['order']));
        }catch (\Exception $e){
            print_r($e);
        }

    }

    public function confirmOrder(Request $request, $id){
        $order = Order::find($id);
        $order->order_status=6;
        $order->height=$request->height;
        $order->breadth=$request->breadth;
        $order->length=$request->length;
        $order->weight=$request->weight;
        $order->save();
        return Redirect::back();
    }

}
