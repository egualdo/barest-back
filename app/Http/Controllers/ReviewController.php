<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\Review\StoreAnswerRequest;
use App\Http\Requests\Landing\Review\StoreRequest;
use App\Http\Requests\Landing\Review\UpdateRequest;
use App\Models\Review;
use App\Models\User;
use App\Notifications\ReviewReceived;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReviewController extends Controller
{   
    public function store(StoreRequest $request) 
    {        
        $user = Auth::user();
        $validated = $request->validated();
        
        try{
            if($validated['ranked_user_id'] === $user->id)
                return $this->error('you can not calificate yourself', 400);
            
            $review = $user->reviews_given()
                            ->where('ranked_user_id', $validated['ranked_user_id'] )
                            ->first();

            if( !is_null($review) )
                return $this->error('you already calificated or commented this user', 400);
                       
            $review = $user->reviews_given()->create($validated);            

            $userRanked = User::find( $validated['ranked_user_id'] );
            $userRanked->notify(new ReviewReceived($user->name));
                
            return $this->success($review); 

        }catch(\Exception $th) {
            return $this->error($th->getMessage(), 401);
        }
    }
    //Se mantiene el metodo hasta ver si es necesario, de momento se elimina la ruta que lo implementa
    public function calculateRanking(User $user) {
        try{
            $user->ranking = $user->calculateRanking();
            
            return $this->success($user);
              
        } catch (\Exception $th) {
            return $this->error($th->getMessage(),401);
        }
    }

    public function getByUserId(User $user,$ranking=null)
    {
        try{
            $reviews = $user->reviews_received();
             
            if( !is_null($ranking) )
                $reviews->where('ranking', $ranking);
            
            $reviews->with([                        
                        'user' => function($query) {
                            $query->select(
                                        'id',
                                        'picture',
                                        DB::raw('
                                            CASE
                                                WHEN company_name is NULL THEN CONCAT(username)
                                                ELSE company_name
                                            END username
                                        '),
                                    );
                        },
                        'rankedUser'
                    ])->orderBy('created_at','desc');
                                
            return $this->showAll(collect( $reviews->paginate(8) ));
        }
        catch (\Exception $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
       
    public function destroy(Review $review )
    {
        try{
            $review->delete();

            return $this->success(true);
        }catch (\Exception $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function filterUserByCategoryId($category_id)
    {           
        try {   
            $users = User::select(
                                'users.id',
                                'users.username',
                                DB::raw('AVG(reviews.ranking) as rate')
                            )
                            ->join('reviews', 'users.id', '=', 'reviews.ranked_user_id')
                            ->join('posts', 'users.id', '=', 'posts.user_id')
                            ->where('category_id', $category_id)
                            ->groupBy('users.id')
                            ->orderBy('rate','desc');
          
            return $this->showAll(collect( $users->paginate(8) ));
       } catch (\Exception $th) {
            return $this->error($th->getMessage(),401);
       }      
    }

    public function validateIfCommented(Request $request) 
    {        
        $user = Auth::user();
        
        $validatedData = $request->validate([ 'user_id' => 'required|numeric|exists:users,id' ]);
        $user_id = $validatedData['user_id'];
        
        try{
            
            $review = $user->reviews_given()
                            ->where('ranked_user_id', $user_id )
                            ->first();

            if( !is_null($review) )
                return $this->error('you already calificated or commented this user', 400);           
                
            return $this->success('success');

        }catch(\Exception $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

    public function storeAnswer(StoreAnswerRequest $request) 
    {           
        $validated = $request->validated();
        $authUser= Auth::user();
        
        try{
            $review = Review::find($validated['review_id']);
            
            if($review->ranked_user_id != $authUser->id)
                return $this->error('you can not answer this review', 400);
            
            if( !is_null($review->answer) )
                return $this->error('you already answered to this review', 400);
                        
            $review->answer = $validated['answer'];
            $review->answered_at = $validated['answered_at'];
            $review->update();
            
            return $this->success( true ); 

        }catch(\Exception $th) {
            return $this->error($th->getMessage(), 400);
        }
    }

     public function destroyAnswer(Review $review )
    {
        try{
            $review->answer = null;
            $review->answered_at = null;
            $review->update();

            return $this->success('answer deleted');
        }catch (\Exception $th) {
            return $this->error($th->getMessage(), 400);
        }
    }
}
