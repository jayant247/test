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
use App\Models\OrderStatus;
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
        $tempDate = Carbon::now()->subDays(07);

        $chartOrders = DB::table('orders')->select(DB::raw("DATE(created_at) as date"), DB::raw('count(*) as count'))
                    ->where('created_at', '>=', $tempDate)->orderBy('created_at', 'DESC')->groupBy(DB::raw("DATE(created_at)"))->get();
        $result[] = ['Date','Order Count'];
        foreach ($chartOrders as $key => $chartOrder) {
            $result[++$key] = [$chartOrder->date, (int)$chartOrder->count];
        }

        // $chartOrders = DB::table('orders')->select(DB::raw("DATE(created_at) as date"),
        //             DB::raw("SUM(total) as total_amount"))
        //             ->where('created_at', '>=', $tempDate)->orderBy('created_at', 'DESC')->groupBy(DB::raw("DATE(created_at)"))->get();
        //$result[] = ['date','total','orders'];
        // foreach ($chartOrders as $key => $chartOrder) {
        //     $result[++$key] = [$chartOrder->date, (int)$chartOrder->total_amount];
        // }
        //dd($result);

        $ordersByCategory = DB::table('orders')->join('order_statuses', 'orders.order_status', '=', 'order_statuses.id')->select('orders.order_status as status', DB::raw('count(orders.id) as count'))->where('orders.created_at', '>=', $tempDate)->groupBy(DB::raw('order_status'))->get();
        //dd($ordersByCategory);

        foreach($ordersByCategory as $orderByCategory){
            $orderStatus = OrderStatus::where('id','=', $orderByCategory->status)->first();
            //dd($orderStatus->name);
            $orderByCategory->order_status_name = $orderStatus->name;
        }
        //dd($ordersByCategory);

        $result2[] = ['Order Status','count'];
        foreach ($ordersByCategory as $key => $orderByCategory) {
            $result2[++$key] = [$orderByCategory->order_status_name, (int)$orderByCategory->count];
        }
        $is_specific_date = false;
        $orderResult=json_encode($result);
        $orderCategories = json_encode($result2);
        //dd($orderCategories);
        return view('dashboard',compact('productCount','orderValue','newCust','totalOrders','orderResult','orderCategories','is_specific_date'));
    }

    public function dashboardForDate(Request $request)
    {
        $today = Carbon::now()->toDateString();
        $tempDate = Carbon::now()->subDays(30);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $customers = User::whereHas('roles', function ($query) {
            return $query->where('name','=', 'Customer');
        })->get();
        $totalCustomers = count($customers);

        $newCust = 0;
        foreach($customers as $customer){
            if ($customer->created_at->toDateString() >= $start_date && $customer->created_at->toDateString() <= $end_date ) {
                $newCust++;
            }
        }
        //dd($newCust);

        $productItems = OrderItems::all();
        //dd($productItems->quantity);
        $productCount = 0;
        foreach($productItems as $productItem){
            if ($productItem->created_at->toDateString() >= $start_date && $productItem->created_at->toDateString() <= $end_date) {
                $productCount = $productCount + $productItem->quantity;
            }
        }
        //dd($productCount);
        //dd($totalCustomers);
        $orders = Order::all();
        $orderValue = 0;
        $totalOrders = 0;
        foreach($orders as $order){
            if ($order->created_at->toDateString() >= $start_date && $order->created_at->toDateString() <= $end_date) {
                $orderValue = $orderValue + $order->total;
                $totalOrders++;
            }
        }
        $tempDate = Carbon::now()->subDays(30);
        $chartOrders = DB::table('orders')->select(DB::raw("DATE(created_at) as date"), DB::raw('count(*) as count'))
                    ->whereBetween(DB::raw('date(created_at)'), [$start_date, $end_date])->orderBy('created_at')->groupBy(DB::raw("DATE(created_at)"))->get();
        $result[] = ['Date','Order Count'];
        foreach ($chartOrders as $key => $chartOrder) {
            $result[++$key] = [$chartOrder->date, (int)$chartOrder->count];
        }

        $ordersByCategory = DB::table('orders')->join('order_statuses', 'orders.order_status', '=', 'order_statuses.id')->select('orders.order_status as status', DB::raw('count(orders.id) as count'))->whereBetween(DB::raw('date(orders.created_at)'), [$start_date, $end_date])->groupBy(DB::raw('order_status'))->get();
        //dd($ordersByCategory);

        foreach($ordersByCategory as $orderByCategory){
            $orderStatus = OrderStatus::where('id','=', $orderByCategory->status)->first();
            //dd($orderStatus->name);
            $orderByCategory->order_status_name = $orderStatus->name;
        }
        //dd($ordersByCategory);

        $result2[] = ['Order Status','count'];
        foreach ($ordersByCategory as $key => $orderByCategory) {
            $result2[++$key] = [$orderByCategory->order_status_name, (int)$orderByCategory->count];
        }
        $is_specific_date = true;
        $orderResult=json_encode($result);
        $orderCategories = json_encode($result2);
        //dd($orderCategories);
        return view('dashboard',compact('productCount','orderValue','newCust','totalOrders','orderResult','orderCategories','is_specific_date','start_date','end_date'));
    }

}

