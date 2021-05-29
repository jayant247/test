<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class SubCategoryController extends Controller{

    public function index(Request $request){

        $categories = Category::with(['parentCategory'])->whereNotNull('parent_id')->get();
        return view('admin.subcategory.index',compact(['categories']));

    }

    public function show(Request $request, $id){

        $category = Category::with(['parentCategory'])->find($id);
        return view('admin.subcategory.show',compact(['category']));

    }

    public function edit(Request $request, $id){

       dd('edit');

    }

}
