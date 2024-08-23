<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function adminVerification(Request $request) {
        
        if( !auth()->guard('admin_api')->user()->hasRole('admin') )
            abort(response::HTTP_UNAUTHORIZED, "You're not an admin user");

        $request->validate([ 'password' => 'required|string|current_password:admin_api' ]);

        return $this->success(true);
    }

    public function authenticate_admin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
        try {

            $admin = Admin::where('email', $credentials['email'])->first();

            if (is_null($admin)) {
                return response()->json(['error' => 'user_not_found'], Response::HTTP_NOT_FOUND);
            }

            if (!$token = auth()->guard('admin_api')->attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], Response::HTTP_UNAUTHORIZED);
            }
            
            if ( !$admin->hasRole('admin') ) {
                return response()->json(['error' => 'invalid_role'], Response::HTTP_FORBIDDEN);
            }
            
            $user = auth()->guard('admin_api')->user()->load('permissions:id,name');

        } catch (JWTException $e) {
            info($e);
            return response()->json(['error' => 'could_not_create_token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        return $this->success(
            [
                "token" => $token,
                "user" => $user
            ],
            200);
    }

    public function logout()
    {        
        auth('admin_api')->logout();

        // return response()->json(['message' => 'Successfully logged out']);
        return response()->json(['state'=>'success'], 200);
    }

    public function refresh()
    {        
        try {
            $newToken = auth('admin_api')->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error'=>$e->getMessage()], 401);
        }
       
        return $this->respondWithToken($newToken);
    }

    public function checkAuthAdmin(Request $request) {
        
        if( !$request->filled('token') )
            return $this->error('token_required', 422);

        try {
            if (!$user = auth()->guard('admin_api')->user())
                return $this->error('user_not_found', 404);
            
            if( !$user->hasRole('admin') )
                return $this->error('not-authorized', 403);
            
            $user->load('permissions:id,name');

            return $this->success([
                            'id' => $user->id,
                            'username' => $user->username,
                            'email' => $user->email,
                            'permissions' => $user->permissions
                        ]);

        } catch (JWTException $e) {
            return $this->error('token_not_parsed', $e->getCode());
        } catch (TokenExpiredException $e) {
            return $this->error('token_expired', $e->getCode());
        } catch (TokenInvalidException $e) {
            return $this->error('token_invalid', $e->getCode());
        }
    }
}
