<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\Comment\StoreRequest;
use App\Http\Requests\Landing\Comment\UpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\UserRanking;

class UserCommentController extends Controller
{  
   
        public function store(StoreRequest $request) 
        {
                $validated = $request->validated();
                $validated['status']=1;
                
                try{                    
                        $comment = Comment::create($validated);
                        
                        return  $this->success( $comment );
                        
                }catch (\Exception $th) {
                        return  $this->error($th->getMessage(),401);
                }
        }

        public function getCommentsByUserId(User $user)
        {       
                try{
                        $reviews = $user->reviews_received()->with(['comment'])->get();
                        return $this->showAll($reviews);
                        
                }catch (\Exception $th) {
                        return $this->error($th->getMessage(),401);
                }
                
        }

        public function update(UpdateRequest $request, Comment $comment)
        {
                try{                       
                        $comment->update( $request->validated() );
                        
                        return $this->success($comment->with('review'));
                        
                }catch (\Exception $th) {
                        return $this->error($th->getMessage(),401);
                }
        }    
}


