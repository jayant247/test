<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(['message' => 'Token is Invalid','status'=>false],401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                try
                {
                    $refreshed = JWTAuth::refresh(JWTAuth::getToken());
                    $user = JWTAuth::setToken($refreshed)->toUser();
                    return response()->json(['data'=>['token'=>$refreshed],'message' => 'Token is Refreshed','status'=>true],401);
                }catch (JWTException $e){
                    return response()->json(['message' => 'Token Expired Can\'t be refreshed.' ,'status'=>false],401);
                }

            }else{
                return response()->json(['message' => 'Authorization Token not found','status'=>false],401);
            }
        }
        return $next($request);
    }
}
