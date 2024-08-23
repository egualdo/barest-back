<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use App\Traits\ApiResponse;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if($user->status === 'BLOCKED'){
                return $this->error('account-blocked', 401);
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->error('Token is Invalid',401);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->error('Token is Expired',401);
            }else{
                return $this->error('Authorization Token not found',401);
            }
        }

        return $next($request);
    }
}
