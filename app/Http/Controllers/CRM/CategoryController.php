<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use App\Http\Controllers\API\CategoryController as ApiCategorycontroller;

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
        $category = Category::find($id);
        return view('admin.category.edit',compact(['category']));
    }

    public function create(Request $request){
        return view('admin.category.create');
    }

    public function store(Request $request){
        try {
            //dd($request);
            $request->validate([
                'category_name' => 'required|string',
                'category_type'=>'string',
                'category_thumbnail'=>'required|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'big_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'new_page_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'square_thumbnail'=>'required|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'is_bigthumbnail_show'=>'boolean'
            ]);
            $newCategory = new Category;
            $newCategory->category_name=$request->category_name;
            $newCategory->type=$request->has('category_type')?$request->category_type:null;
            $newCategory->parent_id=$request->has('parent_id')?$request->parent_id:null;
            $newCategory->category_thumbnail = $this->saveImage($request->category_thumbnail);
            $newCategory->new_page_thumbnail = $request->hasFile('new_page_thumbnail')?$this->saveImage($request->new_page_thumbnail):null;
            if($request->has('is_bigthumbnail_show') && $request->is_bigthumbnail_show==true){
                $newCategory->is_bigthumbnail_show = $request->has('is_bigthumbnail_show')?$request->is_bigthumbnail_show:false;
                if($request->hasFile('big_thumbnail')){
                    $newCategory->big_thumbnail =$this->saveImage($request->big_thumbnail);
                }else{
                    return $this->sendError('Validation Error.', ['big_thumbnail'=>'Big Thumbnail Image Required']);
                }
            }

            if($request->hasFile('square_thumbnail')){
                $newCategory->square_thumbnail =$this->saveImage($request->square_thumbnail);
            }


            if($newCategory->save()){
                return redirect()->route('category.index')
                        ->with('success','Category created successfully.');
            }else{
                if(file_exists(public_path().$newCategory->category_thumbnail)){
                    unlink(public_path().$newCategory->category_thumbnail);
                }
                if(file_exists(public_path().$newCategory->square_thumbnail)){
                    unlink(public_path().$newCategory->square_thumbnail);
                }
                if(file_exists(public_path().$newCategory->big_thumbnail)){
                    unlink(public_path().$newCategory->big_thumbnail);
                }
                if(file_exists(public_path().$newCategory->new_page_thumbnail)){
                    unlink(public_path().$newCategory->new_page_thumbnail);
                }

                return $this->sendError('Category Creation Failed',[], 422);
            }

        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }


    public function update(Request $request, $id){
        //dd($request->category_thumbnail);
        try {
            $request->validate([
                'category_name' => 'string',
                'category_type'=>'string',
                'category_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'big_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'new_page_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'square_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'is_bigthumbnail_show'=>'boolean|nullable'
            ]);


            $category=Category::find($id);

            if(!is_null($category)){
                $category->category_name=$request->has('category_name')?$request->category_name:$category->category_name;
                $category->type=$request->has('type')?$request->type:$category->type;
                if($request->hasFile('category_thumbnail')){
                    $oldFile = $category->category_thumbnail;
                    $category->category_thumbnail =$this->saveImage($request->category_thumbnail);
                    if($oldFile && file_exists(public_path().$oldFile)){
                        unlink(public_path().$oldFile);
                    }

                }
                if($request->hasFile('big_thumbnail')){
                    $oldFile = $category->big_thumbnail;
                    $category->big_thumbnail =$this->saveImage($request->big_thumbnail);
                    if($oldFile && file_exists(public_path().$oldFile)){
                        unlink(public_path().$oldFile);
                    }
                }
                if($request->hasFile('square_thumbnail')){
                    $oldFile = $category->square_thumbnail;
                    $category->square_thumbnail =$this->saveImage($request->square_thumbnail);
                    if($oldFile && file_exists(public_path().$oldFile)){
                        unlink(public_path().$oldFile);
                    }
                }
                if($request->hasFile('new_page_thumbnail')){
                    $oldFile = $category->new_page_thumbnail;
                    $category->new_page_thumbnail =$this->saveImage($request->new_page_thumbnail);
                    if($oldFile && file_exists(public_path().$oldFile)){
                        unlink(public_path().$oldFile);
                    }
                }
                if ($request->has('is_bigthumbnail_show')) {
                    if($request->is_bigthumbnail_show == 1){
                        $category->is_bigthumbnail_show = true;
                    }
                    elseif ($request->is_bigthumbnail_show == 2) {
                        $category->is_bigthumbnail_show = false;
                    }
                }
                
                //$category->is_bigthumbnail_show = $request->has('is_bigthumbnail_show')?$request->is_bigthumbnail_show:false;

                if($category->save()){
                    return redirect()->route('category.index')

                        ->with('success','Category created successfully.');
                }else{
                    unlink(public_path().$category->category_thumbnail);
                    unlink(public_path().$category->square_thumbnail);
                    unlink(public_path().$category->big_thumbnail);
                    return $this->sendError('Category Updation Failed',[], 422);
                }

            }else{
                return $this->sendError('Category Does Not Exist.',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function saveImage($image){
        
        $image_name = 'category'.time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/category/');
        $image->move($destinationPath, $image_name);
        $imageURL='/images/category/'.$image_name;
        //dd($imageURL);
        return $imageURL;
    }


    public function destroy(Category $category){
        $category->delete();
        return redirect()->route('category.index')
                        ->with('success','Product deleted successfully');
    }

}
