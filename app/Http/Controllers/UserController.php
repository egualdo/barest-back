<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\Users\UpdateCVRequest;
use App\Http\Requests\Landing\Users\UpdateRequest;
use App\Http\Requests\Landing\Users\UpdateRoleUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\PlanUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductUser;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Review;

class UserController extends Controller
{
    public function all()
    {
         $user=User::all();

         return $this->showAll($user);

    }

    public function userByUserPivotCategory($id)
    {
        try {
            $users = User::select(
                        'users.*',
                        DB::raw('AVG(reviews.ranking) as rankingProm')
                    )
                    ->join('reviews', 'users.id', '=', 'reviews.ranked_user_id')
                    ->join('posts', 'users.id', '=', 'posts.user_id')
                    ->where('users.id', $id)
                    ->groupBy('users.id')
                    ->orderBy('rankingProm','desc')->first();

            if(!$users)
                return $this->error('Not Found', 404);

            $reviews = Review::where('ranked_user_id',$users->id)->with('comment')->count();

            $users->comments = $reviews;

        return $this->showAll(collect($users));

       } catch (\Exception $th) {
            return $this->error($th->getMessage(), 500);
       }

    }

    public function update(UpdateRequest $request, User $user)
    {
        $validated = $request->validated();

        try {
            $user->update($validated);

            return $this->success($user);

        } catch (\Exception $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function updateRole(UpdateRoleUserRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        try {

            if($user->hasAnyRole('user', 'company'))
                return $this->error('You can not change this role',401);

            $user->assignRole($validated['type_role']);
            $user->type_role = $user->getRoleNames()[0];

            return $this->success($user);

        } catch (\Exception $th) {
            return $this->error($th->getMessage(),401);
        }
    }

    //gestion de images ========================================================================================
    public function uploadImage(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {

            $uploadedImage = $request->validate(['picture' => 'required|image|mimes:jpg,png' ])['picture'];

            if( is_null( $user->picture ) && $request->hasFile('picture') )
                $user->picture = $this->storeImage( $user, $uploadedImage, "users" );
            else if( !is_null( $user->picture ) && $request->hasFile('picture') )
                $user->picture = $this->updateImage( $user, $uploadedImage );

            $user->update();
            DB::commit();

            return $this->success($user->picture);
        }catch (\Exception $th) {
            DB::rollback();
            return $this->error('Ocurrio un error al guardar su archivo: '.$th->getMessage(), 500 );
        }
    }

    public function uploadCv(UpdateCVRequest $request)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {

            $curriculumVitae = $request->safe()['cv'];

            $storedCvPath = null;

            if( is_null( $user->resume ) )
                $storedCvPath = $this->storeCv( $user, $curriculumVitae, "users" );
            else
                $storedCvPath = $this->updateCv( $user, $curriculumVitae );

            $resume = [
                        "path" => $storedCvPath,
                        "size" => $curriculumVitae->getSize(),
                        "nameFile" => trim($curriculumVitae->getClientOriginalName())
                    ];

            if( is_null( $user->resume ) )
                $user->resume()->create( $resume );
            else
                $user->resume->update( $resume );

            DB::commit();
            return $this->success( $resume );
        }catch (\Exception $th) {
            DB::rollback();
            return $this->error('Ocurrio un error al guardar su archivo: '.$th->getMessage(), 500 );
        }

    }

    public function getCV()
    {
       try {

            $userCV = Auth::user()->cv;

            if(!is_null( $userCV )) {
                return $this->success($userCV);
            }else{
                return $this->error('You do not have a cv, please upload one', 401);
            }
       } catch (\Exception $th) {
            return $this->error($th->getMessage(),401);
       }
    }

    public function getPaymentsHistory(Request $request) {

        $payments_history = Auth::user()->payments_made()
                                        ->select(   'id',
                                                    'transaction_number',
                                                    'payment_method',
                                                    'status',
                                                    'mount',
                                                    'payment_histories.created_at',
                                                    'payable_type',
                                                    'payable_id')
                                        ->with(['payable' => function (MorphTo $morphTo) {
                                            $morphTo->morphWith([
                                                PlanUser::class => ['plan:id,name'],
                                                ProductUser::class => ['product:id,name']
                                            ]);
                                        }])
                                        ->orderBy('payment_histories.created_at', 'DESC');

        if( $request->filled('dateFrom') && $request->filled('dateTo'))
            $payments_history->whereDate('payment_histories.created_at','>=',$request->dateFrom)
                             ->whereDate('payment_histories.created_at','<=',$request->dateTo);

        return $this->showAll( Collect( $payments_history->paginate() ) );
    }

    public function show(User $user){

        $user->rankingProm=$user->calculateRanking();
        $user->reviews=$user->reviews_received()->count();

        return $this->showOne($user);
    }

    public function getUserReviewsOrderingRanking($id)
    {
        try{
            $ucom=Review::select(
                                    DB::raw('AVG(reviews.ranking) as rankingProm'),
                                    DB::raw('COUNT(reviews.ranking) as countReviews')
                                )->where('ranked_user_id',$id)
                                ->first();

            $arrReturn=[];
            $arrReturn['rankingProm']=$ucom->rankingProm;
            $arrReturn['countReviews']=$ucom->countReviews;

            for ($i=0; $i < 5; $i++)
            {
                    $calif=[];
                    $rev=Review::select('id','ranking')->where('ranked_user_id',$id)->where('ranking',$i+1)->get();
                    $calif['count_reviews']=count($rev);
                    $calif['results']=$rev;
                    $arrReturn['reviews_'.$i+1]=$calif;
            }

            return $this->showAll(collect($arrReturn));
        } catch (\Exception $th) {
            return $this->error($th->getMessage(),500);
        }
    }

}
