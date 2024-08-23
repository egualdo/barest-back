<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserFromSocialNetworkRequest;
use App\Http\Requests\Landing\Users\StoreRequest;
use App\Http\Requests\Landing\Users\SearchUsersByPhoneNumberRequest;
use App\Http\Requests\Landing\Users\ResetNewPasswordRequest;
use App\Http\Requests\Landing\Users\RequestSMSRequest;
use App\Http\Requests\Landing\Users\VerifyAddressRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\SocialProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Rest\Client;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function searchUsersByPhoneNumber(SearchUsersByPhoneNumberRequest $request) {

        $data = $request->validated();

        try {
            $usersFound = User::activos()->
                                where('country_code',$data['country_code'])->
                                where('phone_number',$data['phone_number'])->
                                get();

        } catch (\Exception $th) {
            return $this->error($th->getMessage(),401);
        }finally{
            return $this->showAll($usersFound);
        }
    }

    public function requestSms(RequestSMSRequest $request){

        $data = $request->validated();

        try {
            $userfind = User::where('id',$data['user_id'])->withTrashed()->first();

            if( is_null($userfind) )
                return $this->error('account-unauthorized', 401);

            PasswordReset::where('email',$userfind->email)->delete();

            if( $userfind->status === 'BLOCKED' )
                return $this->error('account-blocked', 401);

            if( !is_null($userfind->deleted_at) )
                return $this->error('account-eliminated', 401);

            if( $userfind->country_code !== $data['country_code'] || $userfind->phone_number !== $data['phone_number'] )
                return $this->error('phone-number-invalid', 401);

            $token = (string) mt_rand(10000, 99999);
            PasswordReset::create([ "token" => Hash::make($token), "email" => $userfind->email ]);

            $account_sid = env("TWILIO_SID");
            $auth_token = env("TWILIO_TOKEN");
            $twilio_number = env("TWILIO_FROM");
            $message = "Your code for validation is: ".$token;
            $receiverNumber = (string)$data['country_code'].$data['phone_number'];

            $client = new Client($account_sid, $auth_token);
            $client->messages->create($receiverNumber, [
                                    "messagingServiceSid" => 'MG0ed5fda8b933b2a4e68e3491df844b1e', //?pasar a environment
                                    'from' => $twilio_number,
                                    'body' => $message
                                ]);

            return $this->success('SMS has been sent');

        } catch (\Exception $e) {
            return $this->error($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    public function resetNewPassword(ResetNewPasswordRequest $request){

        $data = $request->validated();

        try {

            $userFinded = User::where('id', $data['user_id'])
                                ->withTrashed()
                                ->first();

            if( is_null($userFinded) )
                return $this->error('account-unauthorized', 404);

            if( $userFinded->status === 'BLOCKED' )
                return $this->error('account-blocked', 401);

            if( !is_null($userFinded->deleted_at) )
                return $this->error('account-eliminated', 401);

            $passwordReset = PasswordReset::firstWhere('email', $userFinded->email);

            if( is_null($passwordReset) )
                return $this->error('token not found', 404);

            if(!Hash::check( $data['token'], $passwordReset->token ) )
                return $this->error( 'This code is invalid', 422 );

            if( Hash::check($data['password'], $userFinded->password) )
                return $this->error('You cannot set a new password equal to latest registered', 422);

            if( Carbon::create($passwordReset->created_at)->diffInMinutes() > 30 ) {
                $passwordReset->delete();
                return $this->error( 'This code is expired', 422);
            }

            $userFinded->update([ 'password' => $data['password'] ]);

            $passwordReset->delete();

            return $this->success('password has been successfully reset', 200);

        } catch (\Exception $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function register(StoreRequest $request){

        $validated = $request->safe()->except('type_role');

        try{

            $userfind = User::where('email', $validated['email'])->withTrashed()->first();

            if($userfind !== null && $userfind->deleted_at !== null)
                return $this->error('account-eliminated',401);

            if($userfind !== null && $userfind->status === 'BLOCKED')
                return $this->error( 'account-blocked', 401 );

            if($userfind !== null && $userfind->status == 'ACTIVE')
                return $this->error('account-registered',401);

            $user=User::create($validated);
            $user->assignRole($request->safe()['type_role']);

            $token = JWTAuth::fromUser($user);

            if($token){
                $user->online=1;
                $user->update();
            }

            $response=['token'=>$token,'user'=>$user];

            return $this->success($response, 201);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    public function checkEmail(Request $request){

        $data = $request->validate([
            'email' => 'required|string'
        ]);

        try {
            $userfind = User::where('email',$request->email)->withTrashed()->first();

            if( is_null($userfind) )
                return $this->success('Success');

            if( $userfind->status === 'BLOCKED' )
                return $this->error("account-blocked", 401);

            if( !is_null($userfind->deleted_at) )
                return $this->error("account-eliminated", 401);

            if( $userfind->status === 'ACTIVE' && is_null($userfind->deleted_at)  )
                return $this->error('account-registered', 401);

            return $this->error('invalid-response', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }
    /**
 * @OA\Post(
 * path="/login",
 * summary="Sign in",
 * description="Login by email, password",
 * operationId="authLogin",
 * tags={"auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *       @OA\Property(property="persistent", type="boolean", example="true"),
 *    ),
 * ),
 * @OA\Response(
 *    response=422,
 *    description="Wrong credentials response",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
 *        )
 *     )
 * )
 */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'email' => 'required|string|max:255'
        ]);

        if($validator->fails()){
            return $this->error($validator->errors()->toJson(), 422);
        }

        $credentials = request(['email', 'password']);

        $token=null;
        $userfind = User::where('email',$credentials['email'])->withTrashed()->first();

        try {

            if( !$userfind )
                return $this->error('invalid-credentials', 400);

            if( $userfind->deleted_at !== null )
                return $this->error('account-eliminated', 400);

            if( $userfind->status === 'BLOCKED' )
                return $this->error('account-blocked', 400);

            if( is_null($userfind->password) )
                return $this->error('login-error', 400);                
            else
                $token = auth()->attempt($credentials);

            if(!$token)
                return $this->error('invalid-credentials', 400);                

            $userfind->online = 1;
            $userfind->update();

        } catch (\Exception $exc) {
            return $this->error($exc->getMessage(), 500);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        try {
          if (!$user = JWTAuth::parseToken()->authenticate()) {
                  return response()->json(['state'=>'user_not_found'], 404);
          }
        } catch (TokenExpiredException $e) {
                return response()->json(['state'=>'token_expired'], $e->getCode());
        } catch (TokenInvalidException $e) {
                return response()->json(['state'=>'token_invalid'], $e->getCode());
        } catch (JWTException $e) {
                return response()->json(['state'=>'token_absent'], $e->getCode());
        }

        if($user->getRole())
            $user->type_role = $user->getRole()->name;

        $authUser = $user;
        $authUser->rankingProm = $authUser->calculateRanking();
        $authUser->reviews = $authUser->reviews_received()->count();
        $authUser->newUser = !Carbon::create($authUser->created_at)->diffInMinutes();

        return $this->success($authUser);
    }

    public function logout()
    {
        $updateOnline = Auth::user();
        $updateOnline->online=0;
        $updateOnline->update();

        auth()->logout();

        return $this->success('Success',200);

    }

    public function sendResetPasswordEmail(Request $request ){

        $data = $request->validate([ 'email' => 'required|email|exists:users' ]);

        try {
            $error = '';
            $user = User::whereEmail($data['email'])->withTrashed()->first();
            $data = array();

            PasswordReset::where('email', $user->email)->delete();

            if( !$user )
                return $this->error('account-unauthorized', 401);

            if(!$user->status)
                return $this->error( 'account-status-invalid', 401 );

            if($user->deleted_at !== null)
                return $this->error('account-eliminated',401);

            if($user->status === 'BLOCKED')
                return $this->error( 'account-blocked', 401 );

            $token = (string) mt_rand(10000, 99999);
            PasswordReset::create(["token"=>Hash::make($token),"email"=>$user->email]);

            $data['name'] = $user->name;
            $data['token'] = $token;
            $data['email'] = $request->email;

            Mail::send('mails.passwordReset', $data, function ($message) use ($data) {
                $message->to($data['email'], $data['name']);
                $message->subject('Recuperación de contrase#a');
            });

        }catch (\Exception $error) {
            return $this->error($error->getMessage(),401);
        }

        return $this->success("The email has been send",200);
    }

    public function refresh()
    {
        try {
            $newToken = auth()->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error'=>$e->getMessage()], 401);
        }

        return $this->respondWithToken($newToken);
    }

    protected function respondWithToken($token)
    {
        return $this->success([
            'token' => $token,
            'expires_in' => auth()->factory()->getTTL()
        ]);
    }

    public function verifyTokenIsCorrect(Request $request){

        $data = $request->validate([
                            'token' => 'required|numeric',
                            'email' => 'required|email|exists:users'
                        ]);

        $userFinded = User::activos()->whereEmail($data['email'])->first();

        if(is_null($userFinded) )
            return $this->error('This users is not active.', 401);

        $pw = PasswordReset::where("email",$userFinded->email)->first();

        if( !Hash::check($data['token'], $pw->token) )
            return $this->error('Token is incorrect.', 400);

        return $this->success([ 'user_id' => $userFinded->id ]);
    }


    public function reactivate(User $user)
    {
        if($user->status === 0){
            $user->status = 1;
            $user->save();
            return $this->success('success');
        }else{
            return $this->success('user not found');
        }
    }

    public function checkActualPassword(Request $request){

        $data = $request->validate([ 'password' => 'required|min:8' ]);

        try {
            if(Hash::check($data['password'], Auth::user()->password) == true){
                    return $this->success('Success');
                }else{
                    return $this->error("invalid actual password", 413);
            }
        } catch (\Exception $th) {
            return $this->error($th, 500);
        }

    }

    public function redirectToSocialNetwork($socialNetwork)
    {
        return Socialite::driver($socialNetwork)->redirect();
    }

    public function handleSocialNetworkCallback($socialNetwork)//login por rrss
    {
        if( !Collect( SocialProfile::$social_networks )->contains($socialNetwork) )
            return $this->error('Social network not supported', 401);

        if( !request()->filled('code') )            
            return redirect()->away('https://dev.barest.es/?error=invalid-response');

        try {
            $social_user = Socialite::driver($socialNetwork)->stateless()->user();

            $user = User::where('email', $social_user->getEmail())->withTrashed()->first();

            if($user){
                if( $user->status == 'BLOCKED' )        
                    return redirect()->away('https://dev.barest.es/?error=user-account-is-blocked');

                if( $user->deleted_at !== null )                    
                    return redirect()->away('https://dev.barest.es/?error=user-account-eliminated');

            }

            if( !$user ){

                $nameExplode = explode(' ',$social_user->getName());

                $name = '';
                $lastname = '';

                if(count($nameExplode)>2){
                    $name = $nameExplode[0];
                    $lastname = $nameExplode[2];
                }else{
                    $name = $nameExplode[0];
                    $lastname = $nameExplode[1];
                }

                $user = User::create([
                    'username' => $name.' '.$lastname,
                    'email' => $social_user->getEmail(),
                ]);


                $image = $this->storeImage($user,$social_user->getAvatar(),'users');
                $user->update([ 'picture' => $image ]);

            }

            $social_profile = $user->social_profiles()
                                ->firstOrNew([
                                    'social_network' => $socialNetwork,
                                    'social_network_user_id' => $social_user->getId()
                                ]);

            if(!$social_profile->exists){
                $social_profile->avatar =$social_user->getAvatar();
                $social_profile->save();
            }

            return $this->authAndRedirect($social_profile->user);

        } catch (\Exception $exc) {
            return $this->error($exc->getMessage(), 500);
        }
    }

    public function authAndRedirect($user)
    {
        $token = Auth::login($user);
        return redirect()->away('https://dev.barest.es/auth/authenticate/'.$token);
    }

    public function checkAuthToken() {
        try {
            return $this->success( Auth::check() );
        } catch (\Exception $th) {
            return $this->error('Unauthorized', 403);
        }
    }

    public function registerFromRRSS(StoreUserFromSocialNetworkRequest $request){

        $validated = $request->safe()->except('type_role');

        $validated['username'] = $validated['name'];

        if( !Collect( SocialProfile::$social_networks )->contains( $validated['social_network'] ) )
            return $this->error('Social network not supported.', 401);

        $user = User::where('email', $validated['email'])->withTrashed()->first();

        if($user !== null && $user->deleted_at !== null)
            return $this->error('account-eliminated',401);

        if($user !== null && $user->status === 'BLOCKED')
            return $this->error( 'account-blocked', 401 );

        if(!$user){
            $user= User::create($validated);
            $user->assignRole($request->validated()['type_role']);

            $image=$this->storeImage($user, $validated['avatar'], 'users');
            $user->update(['picture'=>$image]);
        }

        $social_profile = $user->social_profiles()
                                ->firstOrNew([
                                    'social_network' => $validated['social_network'],
                                    'user_id' => $user->id
                                ]);

        if( !$social_profile->exists ) {
            $social_profile->avatar = $validated['avatar'];
            $social_profile->save();
        }

        $token = JWTAuth::fromUser($user);

        return $this->success([ "token" =>  $token, "new_user" => !Carbon::create($user->created_at)->diffInMinutes() ]);
    }

}
