<?php
namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller{

    public function index(Request $request){

        $permissions = Permission::orderBy('id','DESC')->get();
        return view('admin.permission.index',compact(['permissions']));

    }

    public function show(Request $request, $id){

        $permission = Permission::find($id);
        return view('admin.permission.show',compact(['permission']));

    }

    public function edit(Request $request, $id){
        $permission = Permission::find($id);
        return view('admin.permission.edit',compact(['permission']));
    }

    public function create(Request $request){
        return view('admin.permission.create');
    }

    public function store(Request $request){
        try {
            $request->validate([
                'name' => 'required|string',
                'guard_name'=>'string'                
            ]);

            $newPermission = new Permission;
            $newPermission->name=$request->name;
            $newPermission->guard_name=$request->has('guard_name')?$request->guard_name:null;
            if($newPermission->save()){
                return redirect()->route('permission.index')
                        ->with('success','Permission created successfully.');
            }else{        
                return $this->sendError('Permission Creation Failed',[], 422);
            }

        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }


    public function update(Request $request, $id){
        try {
            $request->validate([
                'name' => 'string',
                'guard_name'=>'nullable|string' 
            ]);


            $permission=Permission::find($id);

            if(!is_null($permission)){
                $permission->name=$request->has('name')?$request->name:$permission->name;
                $permission->guard_name=$request->has('guard_name')?$request->guard_name:$permission->guard_name;
                if($permission->save()){
                    return redirect()->route('permission.index')
                        ->with('success','Permission created successfully.');
                }else{
                    return $this->sendError('Permission Updation Failed',[], 422);
                }

            }else{
                return $this->sendError('Permission Does Not Exist.',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }
}
