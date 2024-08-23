<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\QuestionPublication\StoreRequest;
use App\Http\Requests\Landing\QuestionPublication\UpdateRequest;
use App\Models\Post;
use App\Models\QuestionPost;

class QuestionPublicationController extends Controller
{   
    public function store(StoreRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['status']=1;

        try{
            $question = $post->questions()->create( $data );
            return $this->success($question);
            
        }catch (\Exception $th) {
            return $this->error($th->getMessage(),401);
        }           
    }

    public function getQuestionsByPostId(Post $post)
    {
        try{
            return $this->showAll( $post->questions()->get() );            
        }catch (\Exception $th) {
              return $this->error($th->getMessage(),401);
        }
    }

    public function update(UpdateRequest $request, QuestionPost $questionPost)
    {
        $data = $request->validated();

        try{
            $questionPost->update( $data );

            return  $this->success($questionPost);                
        } catch (\Exception $th) {
            return  $this->error($th->getMessage(), 401);
        }
    }

    public function destroy(QuestionPost $questionPost)
    {
        try{
            $questionPost->delete();

            return  $this->success(true);       
        }catch (\Exception $th) {
            return  $this->error($th->getMessage(),401);
        }
    }
    
}
