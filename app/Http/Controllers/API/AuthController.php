<?php

namespace App\Http\Controllers\API;



use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
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
                    return $this->sendError('Password mismatch',[], 200);
                }
            } else {
                $response = ["message" =>''];
                return $this->sendError('User does not exist',[], 200);
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
            $request['password']=bcrypt($request['password']);
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
                return $this->sendError('No User Found. Something Went Wrong', ['error'=>"No User Found"],200);
            }

        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }

    }

    public function loginWithMobileNoPassword(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no'=>'required|digits:10',
                'password'=>'required|string',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            if(Auth::attempt(['mobile_no'=>$request->mobile_no, 'password'=>$request->password])){
                $user = Auth::user();
                try {
                    if (! $token = JWTAuth::fromUser($user)) {
                        return $this->sendError('Wrong Email or Password.', ['error'=>"Wrong Email or Password."],200);
                    }
                } catch (JWTException $e) {
                    return $this->sendError('JWT Token creation failed', ['error'=>"could_not_create_token"],413);
                }
                $response['token']=$token;
                $response['userData']=$user;
                return $this->sendResponse($response, 'Login Successfully');
            }else{
                return $this->sendError('Wrong Mobile Number or Password.', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function loginWithEmailPassword(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email'=>'required|email',
                'password'=>'required|string',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
//            $user = User::whereMobileNo($request->mobile_no)->wherePassword(bcrypt($request->password))->first();
            if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password])){
                $user = Auth::user();
                try {
                    if (! $token = JWTAuth::fromUser($user)) {
                        return $this->sendError('Wrong Email or Password.', ['error'=>"Wrong Email or Password."],200);
                    }
                } catch (JWTException $e) {
                    return $this->sendError('JWT Token creation failed', ['error'=>"could_not_create_token"],413);
                }
                $response['token']=$token;
                $response['userData']=$user;
                return $this->sendResponse($response, 'Login Successfully');
            }else{
                return $this->sendError('Wrong Email or Password.', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function createPassword(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'password'=>'required|string',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = Auth::user();
            if(!is_null($user)){
                $user->password = bcrypt($request->password);
                $user->save();
                return $this->sendResponse([], 'Password Generated Successfully');
            }else{
                return $this->sendError('No User Available', [],200);
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
                'firebase_token'=>'required|string',
                'imei_number'=>'string|required',
                'device_type'=>'string|required',
                'password'=>'string|required'
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            ;
            if(!is_null(User::where('imei_number',$request->imei_number)->first())){
                $newUser = User::where('imei_number',$request->imei_number)->first();
            }else{
                $newUser = new User;
            }
            $newUser->name = $request->name;
            $newUser->firebase_token = $request->has('firebase_token')?$request->firebase_token: null ;
            $newUser->mobile_no=$request->mobile_no;
            $newUser->email=$request->email;
            $newUser->imei_number = '';
            $newUser->device_type = $request->device_type;
            $newUser->password= bcrypt($request->password);
            $newUser->city = $request->has('city')?$request->city: null;
            $newUser->mobile_otp = rand(100000,999999);
            $newUser->mobile_otp_time = Carbon::now();
            $newUser->save();

            $role = Role::where('name','Customer')->first();
            $userData = User::query()->whereId($newUser->id)->first();
            if(!is_null($userData)){


                $userData->assignRole($role);
                $response=['newUser'=>$userData];
                if($this->sendOtp($userData)){
                    return $this->sendResponse($response, 'OTP Send Successfully');
                }else{
                    return $this->sendResponse([], 'OTP Send Failed',false);
                }

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
                        return $this->sendError('OTP Timeout. Please Generate New OTP', ['error'=>"OTP Timeout. Please Generate New OTP"],200);
                    }else{
                        if($user->mobile_otp==$request->otp.''){
                            $user->mobile_otp=null;
                            $user->save();
                            $response= ['userInfo'=>$user->toArray()];
                            try {
                                if (! $token = JWTAuth::fromUser($user)) {
                                    return $this->sendError('Wrong OTP', ['error'=>"Wrong OTP"],200);
                                }
                            } catch (JWTException $e) {
                                return $this->sendError('JWT Token creation failed', ['error'=>"could_not_create_token"],413);
                            }
                            $response['token']=$token;
                            return $this->sendResponse($response, 'OTP Verified Successfully');
                        }else{
                            return $this->sendError('Wrong OTP', ['error'=>"Wrong OTP"],200);
                        }
                    }
                }else{
                    return $this->sendError('Please Generate New OTP', ['error'=>"OTP Timeout. Please Generate New OTP"],200);
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
                        if($this->sendOtp($user)){
                            return $this->sendResponse([], 'New OTP Send Successfully');
                        }else{
                            return $this->sendResponse([], 'OTP Send Failed',false);
                        }

                    }else{

                        if($this->sendOtp($user)){
                            return $this->sendResponse([], 'OTP Send Successfully');
                        }else{
                            return $this->sendResponse([], 'OTP Send Failed',false);
                        }
                    }
                }else{
                    $user->mobile_otp = rand(100000,999999);
                    $user->mobile_otp_time = Carbon::now();
                    $user->save();
                    if($this->sendOtp($user)){
                        return $this->sendResponse([], 'New OTP Send Successfully');
                    }else{
                        return $this->sendResponse([], 'OTP Send Failed',false);
                    }
                }
            }else{
                return $this->sendError('User Does Not Exist', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function verifyEmailOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email'=>'required|email',
                'otp'=>'required|numeric',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = User::whereEmail($request->email)->first();
            if(!is_null($user)){
                if($user->email_otp){
                    $start_date = new \DateTime();
                    $since_start = $start_date->diff(new \DateTime($user->email_otp_time));
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    if($minutes>10){
                        return $this->sendError('OTP Timeout. Please Generate New OTP', ['error'=>"OTP Timeout. Please Generate New OTP"],200);
                    }else{
                        if($user->email_otp==$request->otp.''){
                            $user->email_otp=null;
                            $user->email_verified_at=Carbon::now();
                            $user->save();
                            $response= ['userInfo'=>$user->toArray()];

                            return $this->sendResponse($response, 'Email OTP Verified Successfully');
                        }else{
                            return $this->sendError('Wrong OTP', ['error'=>"Wrong OTP"],200);
                        }
                    }
                }else{
                    return $this->sendError('Please Generate New OTP', ['error'=>"OTP Timeout. Please Generate New OTP"],200);
                }
            }else{
                return $this->sendError('User Does Not Exist', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

    public function resendEmailOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email'=>'required|email',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $user = User::whereEmail($request->email)->first();
            if(!is_null($user)){
                if($user->email_otp) {
                    $start_date = new \DateTime();
                    $since_start = $start_date->diff(new \DateTime($user->email_otp_time));
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    if ($minutes > 10) {
                        $user->email_otp = rand(100000,999999);
                        $user->email_otp_time = Carbon::now();
                        $user->save();
                        Mail::send('email.mailOTP', ['otp' => $user->email_otp], function ($m) use ($user) {
                            $m->from(env('MAIL_FROM_ADDRESS','info@bloomapp.in'), 'Bloom App | OTP verify mail');

                            $m->to($user->email, $user->fullname)->subject('Email Verification OTP.');
                        });
                        return $this->sendResponse([], 'New OTP Send Successfully');
                    }else{
                        Mail::send('email.mailOTP', ['otp' => $user->email_otp], function ($m) use ($user) {
                            $m->from(env('MAIL_FROM_ADDRESS','info@bloomapp.in'), 'Bloom App | OTP verify mail');

                            $m->to($user->email, $user->fullname)->subject('Email Verification OTP.');
                        });
                        return $this->sendResponse([], 'OTP Send Successfully');
                    }
                }else{
                    $user->email_otp = rand(100000,999999);
                    $user->email_otp_time = Carbon::now();
                    $user->save();
                    Mail::send('email.mailOTP', ['otp' => $user->email_otp], function ($m) use ($user) {
                        $m->from(env('MAIL_FROM_ADDRESS','info@bloomapp.in'), 'Bloom App | OTP verify mail');

                        $m->to($user->email, $user->fullname)->subject('Email Verification OTP.');
                    });
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
                if($this->sendOtp($user)){
                    return $this->sendResponse([], 'OTP Send Successfully');
                }else{
                    return $this->sendResponse([], 'OTP Send Failed',false);
                }
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

    function sendOtp($user){
        $curl = curl_init();
            $url = "https://2factor.in/API/V1/".env("MESSAGE_API_KEY")."/SMS/+91".$user->mobile_no."/".$user->mobile_otp;
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
              CURLOPT_HTTPHEADER => array(

              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            $response = json_decode($response,true);
            if($response["Status"]=="Success"){
                return true;
            }else{
                return false;
            }
    }

    public function customerRegistrationWithImei(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'imei_number'=>'string|required',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $response=[];
            $response['token']=null;
            if(!is_null(User::where('imei_number',$request->imei_number)->first())){
                $newUser = User::where('imei_number',$request->imei_number)->first();
                try {
                    if (! $token = JWTAuth::fromUser($newUser)) {
                        return $this->sendError('Error in token generation.', ['error'=>"Error in token generationord."],413);
                    }
                } catch (JWTException $e) {
                    return $this->sendError('JWT Token creation failed', ['error'=>"could_not_create_token"],413);
                }
                $response['token']=$token;
            }else{
                $newUser = new User;
                $newUser->name = $request->imei_number;
                $newUser->mobile_no=$request->imei_number;
                $newUser->email=$request->imei_number;
                $newUser->imei_number = $request->imei_number;
                $newUser->save();

                try {
                    if (! $token = JWTAuth::fromUser($newUser)) {
                        return $this->sendError('Error in token generation.', ['error'=>"Error in token generationord."],413);
                    }
                } catch (JWTException $e) {
                    return $this->sendError('JWT Token creation failed', ['error'=>"could_not_create_token"],413);
                }
                $response['token']=$token;
            }



            if(!is_null($response['token'])){
                return $this->sendResponse($response, 'Guest User Login Successfully');
            }
            else{
                return $this->sendError('Something Went Wrong While Registration', [],200);
            }
        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }


    public function checkToken(Request $request){
        return $this->sendResponse([], 'Token Valid');
    }

    public function customerRegistrationWithMobileOnly(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'mobile_no'=>'required|digits:10',
            ]);
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $response=[];

            if(!is_null(User::where('mobile_no',$request->mobile_no)->first())){
                $newUser = User::where('mobile_no',$request->mobile_no)->first();
                $newUser->mobile_otp = rand(100000,999999);
                $newUser->mobile_otp_time = Carbon::now();
                $newUser->save();

            }else{
                $newUser = new User;
                $newUser->mobile_no=$request->mobile_no;
                $newUser->mobile_otp = rand(100000,999999);
                $newUser->mobile_otp_time = Carbon::now();
                $newUser->save();

            }


            if($this->sendOtp($newUser)){

                return $this->sendResponse($response, 'OTP Send Successfully');
            }else{
                return $this->sendResponse([], 'OTP Send Failed',false);
            }

        }catch (Exception $e){
            return $this->sendError('Something Went Wrong', $e,413);
        }
    }

}

