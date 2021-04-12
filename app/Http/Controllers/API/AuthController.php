<?php

namespace App\Http\Controllers\API;



use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Hash;
class AuthController extends BaseController
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */

    public function adminLogin(Request $request){
        try{
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    Auth::login($user);
                    $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                    $response = ['token' => $token];
                    $response['userData']=$user;
                    return $this->sendResponse($response,'Login Success', true);
                } else {
                    return $this->sendError('Password mismatch',[], 422);
                }
            } else {
                $response = ["message" =>''];
                return $this->sendError('User does not exist',[], 422);
            }
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function adminRegister (Request $request) {
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'mobile_no'=>'required|digits:10|unique:users',
            ]);
            if ($validator->fails())
            {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $request['password']=Hash::make($request['password']);
            $user = User::create($request->toArray());
            $token = $user->createToken('Laravel Password Grant Client')->accessToken;
            $response = ['token' => $token];
            $response['userData']=$user;
            return $this->sendResponse($response,'Registered Successfully', true);
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function logout (Request $request) {
        try{
            $token = $request->user()->token();
            $token->revoke();
            return $this->sendResponse([], 'User Logout successfully.');
        }
        catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function checkExistingUser(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile_number'=>'required|digits:10',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = User::where('mobile_number',$request->mobile_number)->with('languages','category')->first();

            if($user){
                return $this->sendResponse(['userData'=>$user], 'User Exist');
            }else{
                return $this->sendError('No User Found. Something Went Wrong', ['error'=>"No User Found"]);
            }

        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }

    }

    public function customerRegistration(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'mobile_no'=>'required|digits:10|unique:users',
                'city'=>'string',
                'firebase_token'=>'required|string'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $newUser = new User;
            $newUser->name = $request->name;
            $newUser->firebase_token = $request->has('firebase_token')?$request->firebase_token: null ;
            $newUser->mobile_no=$request->mobile_no;
            $newUser->email=$request->email;
            $newUser->city = $request->has('city')?$request->city: null;
            $newUser->city = $request->has('imei_number')?$request->imei_number: null;
            $newUser->mobile_otp = rand(100000,999999);
            $newUser->mobile_otp_time = Carbon::now();
            $newUser->save();

            $role = Role::where('name','Customer')->first();
            $userData = User::query()->whereId($newUser->id)->first();
            if(!is_null($userData)){
                $userData->assignRole($role);
                $response=['newUser'=>$userData];
                return $this->sendResponse($response,'OTP Sent Successfully', true);
            }
            else{
                return $this->sendError('Something Went Wrong While Registration', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function verifyMobileOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no'=>'required|digits:10',
                'otp'=>'required|numeric',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = User::whereMobileNo($request->mobile_no)->first();
            if(!is_null($user)){
                if($user->mobile_otp){
                    $start_date = new \DateTime();
                    $since_start = $start_date->diff(new \DateTime($user->mobile_otp_time));
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    if($minutes>10){
                        return $this->sendError('OTP Timeout. Please Generate New OTP', ['error'=>"OTP Timeout. Please Generate New OTP"]);
                    }else{
                        if($user->mobile_otp==$request->otp.''){
                            $user->mobile_otp=null;
                            $user->save();
                            $response= ['userInfo'=>$user->toArray()];
                            try {
                                if (! $token = JWTAuth::fromUser($user)) {
                                    return $this->sendError('Wrong OTP', ['error'=>"Wrong OTP"]);
                                }
                            } catch (JWTException $e) {
                                return $this->sendError('JWT Token creation failed', ['error'=>"could_not_create_token"]);
                            }
                            $response['token']=$token;
                            return $this->sendResponse($response, 'OTP Verified Successfully');
                        }else{
                            return $this->sendError('Wrong OTP', ['error'=>"Wrong OTP"]);
                        }
                    }
                }else{
                    return $this->sendError('Please Generate New OTP', ['error'=>"OTP Timeout. Please Generate New OTP"]);
                }
            }else{
                return $this->sendError('User Does Not Exist', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function resendOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no'=>'required|digits:10',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = User::whereMobileNo($request->mobile_no)->first();
            if(!is_null($user)){
                if($user->mobile_otp) {
                    $start_date = new \DateTime();
                    $since_start = $start_date->diff(new \DateTime($user->mobile_otp_time));
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    if ($minutes > 10) {
                        $user->mobile_otp = rand(100000,999999);
                        $user->mobile_otp_time = Carbon::now();
                        $user->save();
                        return $this->sendResponse([], 'New OTP Send Successfully');
                    }else{

                        return $this->sendResponse([], 'OTP Send Successfully');
                    }
                }else{
                    $user->mobile_otp = rand(100000,999999);
                    $user->mobile_otp_time = Carbon::now();
                    $user->save();
                    return $this->sendResponse([], 'OTP Send Successfully');
                }
            }else{
                return $this->sendError('User Does Not Exist', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }


    public function customerLoginWithOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no'=>'required|digits:10',

            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = User::whereMobileNo($request->mobile_no)->first();
            if(!is_null($user)){
                $user->mobile_otp = rand(100000,999999);
                $user->mobile_otp_time = Carbon::now();
                $user->save();
                return $this->sendResponse([],'Please verify otp', true);
            }
            else{
                return $this->sendError('No User Found', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function updateProfile(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'experience'=>'numeric|max:90',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = Auth::guard('api')->user();
            if(is_null($user)){
                return $this->sendError('No User Found', [],200);
            }
            $allwedFieldList = ['name','email','city','marital_status','education','interest','experience','short_bio'];
            foreach ($request->all() as $key=>$field){
                if(in_array($key,$allwedFieldList)){
                    $user->$key=$field;
                }
            }
            $user->save();
            if($request->has('languageSpoken')){
                $languageArray = [];
                foreach (array_map('intval',explode(',',$request->languageSpoken)) as $key=>$eachLanguageId){

                    if(!in_array($eachLanguageId, $languageArray)){
                        array_push($languageArray,$eachLanguageId);
                    }
                }
                $user->languages()->sync($languageArray);
            }
            if($request->has('interest')){
                $interestArray= [];
                foreach (array_map('intval',explode(',',$request->interest)) as $key=>$eachLanguageId){

                    if(!in_array($eachLanguageId, $interestArray)){
                        array_push($interestArray,$eachLanguageId);
                    }
                }
                $user->category()->sync($interestArray);
            }

            $userData = User::query()->whereId($user->id)->with('languages','category')->first();
            if(!is_null($userData)){

                $response['userData']=$userData;
                return $this->sendResponse($response,'Registered Successfully', true);
            }
            else{
                return $this->sendError('Something Went Wrong While Registration', [],200);
            }


        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function createRole(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name'=>'required|string',
                'permission_ids'=>'required|string'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $permissionArray = [];
            foreach (array_map('intval',explode(',',$request->permission_ids)) as $key=>$eachPermissionId){

                if(!in_array($eachPermissionId, $permissionArray)){
                    array_push($permissionArray,$eachPermissionId);
                }
            }
            $role =Role::create(['name' => $request->name,'guard_name'=>'api']);
            $role->syncPermissions($permissionArray);

            if(!is_null($role)){
                return $this->sendResponse([],'Role Created Successfully', true);
            }else{
                return $this->sendError('Role Not Created.', [],200);
            }

        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function createPermission(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name'=>'required|string',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            if(Permission::create(['name' => $request->name,'guard_name'=>'api'])){
                return $this->sendResponse([],'Permission Created Successfully', true);
            }else{
                return $this->sendError('Permission Not Created.', [],200);
            }


        }catch (\Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function getPermissionsList(Request $request){
        try{
            $permissions = Permission::all();
            if(count($permissions)>0){
                $response = ['data' => $permissions];
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }
        }catch (\Exception $exception){
            return $this->sendError('Something Went Wrong', $exception->getMessage(),413);
        }
    }

    public function assignRole(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'role_id'=>'required|numeric',
                'user_id'=>'required|numeric',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $user = User::find($request->user_id);
            $role = Role::find($request->role_id);
            if(is_null($role)){
                return $this->sendError('No Role Available', [],200);
            }
            if(!is_null($user)){

                if($user->syncRoles([$role])){
                    return $this->sendResponse([],'Role Assigned Successfully', true);
                }else{
                    return $this->sendError('Role Not Assigned', [],200);
                }
            }else{
                return $this->sendError('No User Available', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function updateRole(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'name'=>'required|string',
                'permission_ids'=>'required|string'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $permissionArray = [];
            foreach (array_map('intval',explode(',',$request->permission_ids)) as $key=>$eachPermissionId){

                if(!in_array($eachPermissionId, $permissionArray)){
                    array_push($permissionArray,$eachPermissionId);
                }
            }
            $role = Role::with('permissions')->find($id);
            if(!is_null($role)){
                $role->name = $request->name;
                if($role->syncPermissions($permissionArray)){
                    return $this->sendResponse([],'Role Updated Successfully', true);
                }else{
                    return $this->sendError('Role Not Updated.', [],200);
                }
            }else{
                return $this->sendError('No Role Found', [],200);
            }

        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function getRoles(Request $request){
        try{
            $roles = Role::with('permissions')->get();
            if(count($roles)>0){
                $response = ['data' => $roles];
                return $this->sendResponse($response,'Data Fetched Successfully', true);
            }else{
                return $this->sendError('No Data Available', [],200);
            }
        }catch (\Exception $exception){
            return $this->sendError('Something Went Wrong', $exception->getMessage(),413);
        }
    }



}
