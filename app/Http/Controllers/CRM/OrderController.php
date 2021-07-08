<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Permission;
use Illuminate\Http\Request;
use Validator;

class OrderController extends Controller{

    public function index(Request $request){

        $filter = '';

        if($request->has('filter')){
            $searchQuery = $request->search;
            $orders = Order::sortable()
                ->orWhere('orderRefNo','like', '%' . $request->search. '%')
                ->orWhere('id','like', '%' . $request->search. '%')
                ->orWhere('total','like', '%' . $request->search. '%')
                ->orWhere('orderRefNo','like', '%' . $request->search. '%')
                ->with('customer',function ($query,$searchQuery){
                    $query->orWhere('name','like', '%' . $searchQuery. '%')
                        ->orWhere('email','like', '%' . $searchQuery. '%')
                        ->orWhere('mobile','like', '%' . $searchQuery. '%')
                    ;
                })
                ->with('addressDetails',function ($query,$searchQuery){
                    $query->orWhere('pincode','like', '%' . $searchQuery. '%')
                    ;
                })
                ->with(['customer','addressDetails'])->withCount(['orderItems'])->paginate(2);
        }else{
            $orders = Order::sortable()->with(['customer','addressDetails'])->withCount(['orderItems'])->paginate(5);
        }


        return view('admin.order.index',compact(['orders','filter']));

    }

}
