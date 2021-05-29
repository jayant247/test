<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use JWTAuth;

class AuthController extends Controller{

    public function login(Request $request){

        if(Auth::user()){
            try {
                if (! $token = JWTAuth::fromUser(Auth::user())) {
                    return $this->sendError('Error in token generation.', ['error'=>"Error in token generationord."]);
                }
            } catch (JWTException $e) {
                return $this->sendError('JWT Token creation failed', ['error'=>"could_not_create_token"]);
            }


            return view('welcome',compact(['token']));
        }else{
            return view('Auth.login');
        }

    }

}
