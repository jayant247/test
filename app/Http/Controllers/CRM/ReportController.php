<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Validator;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Support\Facades\Auth;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    public function index()
    {
        return view('reports');
    }

    public function exportOrders(Request $request)
    {

        return Excel::download(new OrdersExport, 'orders.xlsx');

    }

    public function exportSoldProducts(Request $request)
    {

        return Excel::download(new OrdersExport, 'orders.xlsx');

    }

}

