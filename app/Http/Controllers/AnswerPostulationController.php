<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\StoreAnswerPostulationRequest;
use App\Http\Requests\Landing\UpdateAnswerPostulationRequest;
use App\Models\AnswerPostulation;
use Illuminate\Support\Facades\Auth;

class AnswerPostulationController extends Controller
{

    public function store(StoreAnswerPostulationRequest $request)
    {
        $user = Auth::user();
        $answer = $request->validated(); 
        //safe es para manipular la data validada y el validated es para retornar el array plano de los datos validados

        try {
            $answer['cancelled_by_user_id'] = $user->id;

            AnswerPostulation::create($answer);

            return $this->success( $answer );
        
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 401);
        }       
    }

    public function show(AnswerPostulation $answer)
    {
        try{

            $answer->load([ 'question', 'user' ]);

            return $this->success( $answer );

        }catch(\Exception $exception){
            return $this->error($exception->getMessage(), 401);
        }
    }

    public function update(UpdateAnswerPostulationRequest $request,AnswerPostulation $answer)//AnswerPostulation $answerPostulation
    {
        $validated = $request->validated();
        
        try {
            
            $answer->update($validated);
        
            return $this->success( $answer );
        
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 401);
        }
    }
   
    public function destroy(AnswerPostulation $answer)//tomar de guia para softdeletes en los modelos  y route model binding
    {        
        try{
            $answer->update(["status"=>0]);
          
            return $this->success( true );
        
        }catch(\Exception $exception){
            return $this->error($exception->getMessage(), 401);
        }
    }
    
}
