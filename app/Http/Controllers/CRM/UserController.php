<?php
namespace App\Http\Controllers\CRM;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
  

class UserController extends Controller

{

    public function index(Request $request)

    {

        $users = User::whereHas('roles', function ($query) {
            return $query->where('name','!=', 'Customer');
        })->get();
        //$users = User::role('Customer')->get();
        // $users = User::orderBy('id','DESC')->get();
        // return view('admin.user.index',compact('users'));
        // dd($users);
        //$users = User::all();
        //dd($users);
        return view('admin.user.index',compact('users'));

    }



    public function create()
    {

        $roles = Role::all();
        // dd($roles);

        return view('admin.user.create',compact('roles'));

    }



    public function store(Request $request)

    {

        $this->validate($request, [

            'name' => 'required',

            'email' => 'required|email|unique:user,email',

            'password' => 'required|same:confirm-password',

            'roles' => 'required'

        ]);

    

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

    

        $user = User::create($input);

        $user->assignRole($request->input('roles'));

    

        return redirect()->route('admin.user.index')

                        ->with('success','User created successfully');

    }


    public function show($id)

    {

        $user = User::find($id);

        return view('user.show',compact('user'));

    }



    public function edit($id)

    {

        $user = User::find($id);

        $roles = Role::all();

        $userRole = $user->roles->first();
        //$userRole = $user->roles->get();

        //dd($userRole);

        return view('admin.user.edit',compact('user','roles','userRole'));

    }


    public function update(Request $request, $id)

    {

        $this->validate($request, [

            'name' => 'nullable',

            'email' => 'email|unique:user,email,'.$id,

            'password' => 'same:confirm-password',

            'roles' => 'required'

        ]);

    

        $input = $request->all();

        if(!empty($input['password'])){ 

            $input['password'] = Hash::make($input['password']);

        }else{

            $input = Arr::except($input,array('password'));    

        }

    

        $user = User::find($id);

        $user->update($input);

        DB::table('model_has_roles')->where('model_id',$id)->delete();

    

        $user->assignRole($request->input('roles'));

    

        return redirect()->route('admin.user.index')

                        ->with('success','User updated successfully');

    }



    public function destroy($id)

    {

        User::find($id)->delete();

        return redirect()->route('admin.user.index')

                        ->with('success','User deleted successfully');

    }

}