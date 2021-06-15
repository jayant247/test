<?php
namespace App\Http\Controllers\API;



use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Validator;

class CategoryController extends BaseController{

    public function createCategory(Request $request){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'category_name' => 'required|string',
                'category_type'=>'string',
                'parent_id' => 'numeric',
                'category_thumbnail'=>'required|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'big_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'new_page_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'square_thumbnail'=>'required|file|max:2048|mimes:jpeg,bmp,png,jpg',
                'is_bigthumbnail_show'=>'boolean'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newCategory = new Category;
            $newCategory->category_name=$request->category_name;
            $newCategory->type=$request->has('category_type')?$request->category_type:null;
            $newCategory->parent_id=$request->has('parent_id')?$request->parent_id:null;
            $newCategory->category_thumbnail =$this->saveImage($request->category_thumbnail);
            $newCategory->new_page_thumbnail = $request->has('new_page_thumbnail')?$this->saveImage($request->new_page_thumbnail):null;
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
                return $this->sendResponse([],'Category Created Successfully.', true);
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

    function saveImage($image){
        $image_name = 'category'.time().'.'.$image->getClientOriginalExtension();
        $destinationPath = public_path('images/category/');
        $image->move($destinationPath, $image_name);
        $imageURL='/images/category/'.$image_name;
        return $imageURL;
    }

    public function updateCategory(Request $request, $id){
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'category_name' => 'string',
                'category_type'=>'string',
                'parent_id' => 'numeric',
                'category_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'big_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'square_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
                'is_bigthumbnail_show'=>'boolean',
                'new_page_thumbnail'=>'file|max:2048|mimes:jpeg,bmp,png,jpg',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $category=Category::find($id);

            if(!is_null($category)){
                $category->category_name=$request->has('category_name')?$request->category_name:$category->category_name;
                $category->type=$request->has('category_type')?$request->category_type:$category->type;
                $category->parent_id=$request->has('parent_id')?$request->parent_id:$category->parent_id;
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
                $category->is_bigthumbnail_show = $request->has('is_bigthumbnail_show')?$request->is_bigthumbnail_show:false;

                if($category->save()){
                    return $this->sendResponse([],'Category Updated Successfully.', true);
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

    public function getCategory(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'pageNo'=>'numeric',
                'limit'=>'numeric',
                'fields'=>'string',
                'name'=>'string',
                'type'=>'string',
                'parent_id'=>'numeric',
                'mainCategory'=>'required|numeric'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $query = Category::query();
            if($request->mainCategory){
                $query = $query->whereNull('parent_id')->with(['subCategory']);
            }else{
                if(!$request->has('parent_id')){
                    return $this->sendError('Parent Id Required.', ["parent_id"=> [
                        "The parent id field is required."
                    ]],200);
                }
                $query = $query->whereNotNull('parent_id')->where('parent_id',$request->parent_id);
            }
            $fieldsArray=[];
            if($request->has('category_name')){
                $query =$query->where('category_name','like','%'.$request->category_name.'%');
            }
            if($request->has('type')){
                $query =$query->where('type','like','%'.$request->type.'%');
            }
            if($request->has('fields')){
                $fieldsArray=explode(',',$request->fields);
                $query = $query->select($fieldsArray);
            }
            if($request->has('pageNo') && $request->has('limit')){
                $limit = $request->limit;
                $pageNo = $request->pageNo;
                $skip = $limit*$pageNo;
                $query= $query->skip($skip)->limit($limit);
            }
            $data= $query->orderBy('id','ASC')->get();
            if(count($data)>0){
                $response =  $data;
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    

}
