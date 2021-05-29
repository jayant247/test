<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller{

    public function index(Request $request){

        $categories = Category::with(['subCategory'])->whereNull('parent_id')->get();
        return view('admin.category.index',compact(['categories']));

    }

    public function show(Request $request, $id){

        $category = Category::with(['subCategory'])->find($id);
        return view('admin.category.show',compact(['category']));

    }

    public function edit(Request $request, $id){

       dd('edit');

    }

}
