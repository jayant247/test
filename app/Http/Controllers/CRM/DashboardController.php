<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->toDateString();
        // $today = '2021-05-17';
        $customers = User::whereHas('roles', function ($query) {
            return $query->where('name','=', 'Customer');
        })->get();
        $totalCustomers = count($customers);

        $newCust = 0;
        foreach($customers as $customer){
            if ($customer->created_at->toDateString() == $today) {
                $newCust++;
            }
        }
        //dd($newCust);

        $productItems = OrderItems::all();
        //dd($productItems->quantity);
        $productCount = 0;
        foreach($productItems as $productItem){
            if ($productItem->created_at->toDateString() == $today) {
                $productCount = $productCount + $productItem->quantity;
            }
        }
        //dd($productCount);
        //dd($totalCustomers);
        $orders = Order::all();
        $orderValue = 0;
        $totalOrders = 0;
        foreach($orders as $order){
            if ($order->created_at->toDateString() == $today) {
                $orderValue = $orderValue + $order->total;
                $totalOrders++;
            }
        }
        //dd($orderValue);
        // $orderCount = count($orders);
        // dd($orderCount);
        //$jobs = Jobs::get()->where('$dt', '<', 'expire');
        $tempDate = Carbon::now()->subDays(70);

        $chartOrders = DB::table('orders')->select(DB::raw("DATE(created_at) as date"),
                    DB::raw("SUM(total) as total_amount"), DB::raw('count(*) as count'))
                    ->where('created_at', '>=', $tempDate)->orderBy('created_at', 'DESC')->groupBy(DB::raw("DATE(created_at)"))->get();
        //$result[] = ['date','total','orders'];
        foreach ($chartOrders as $key => $chartOrder) {
            $result[++$key] = [$chartOrder->date, (int)$chartOrder->total_amount, (int)$chartOrder->count];
        }
        $orderResult=json_encode($result);
        //($orderResult);
        return view('dashboard',compact('productCount','orderValue','newCust','totalOrders','orderResult'));
    }

}

