<?php 

namespace App\Http\Controllers\CRM;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Validator;

    

class RoleController extends Controller

{


    // function __construct()

    // {

    //      $this->middleware('permission:role-list|role-create|role-edit|role-delete', ['only' => ['index','store']]);

    //      $this->middleware('permission:role-create', ['only' => ['create','store']]);

    //      $this->middleware('permission:role-edit', ['only' => ['edit','update']]);

    //      $this->middleware('permission:role-delete', ['only' => ['destroy']]);

    // }



    public function index(Request $request)

    {

        $roles = Role::orderBy('id','DESC')->get();
        foreach ($roles as  $role) {
            $role->permissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$role->id)->get();
        }
        return view('admin.role.index',compact('roles'));

    }



    public function create()
    {
        $permissions = Permission::get();
        //dd($permissions);
        return view('admin.role.create',compact('permissions'));
    }



    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'guard_name' => 'nullable',
            'permissions' => 'nullable'
        ]);

        $newRole = new Role;
        $newRole->name = $request->input('name');
        $newRole->guard_name=$request->has('guard_name')?$request->guard_name:'api';
        foreach($request->permissions as $key=>$permission){
            $newRole->syncPermissions($request->input('permission'));
        }
        $newRole->save();

        return redirect()->route('role.index')
                        ->with('success','Role created successfully');
    }
    
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return view('admin.role.show',compact('role','rolePermissions'));
    }


    public function edit($id)
    {
        $role = Role::find($id);
        $permissions = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('admin.role.edit',compact('role','permissions','rolePermissions'));
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        $role->syncPermissions($request->input('permission'));
        return redirect()->route('role.index')
                        ->with('success','Role updated successfully');
    }


    public function destroy(Request $request, $id)
    {
        Role::find($id)->delete();
        return redirect()->route('role.index')
                        ->with('success','Role deleted successfully');
    }
}