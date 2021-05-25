<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller{

    public function login(Request $request){

        if(Auth::user()){
            return view('welcome');
        }else{
            return view('Auth.login');
        }

    }

}
