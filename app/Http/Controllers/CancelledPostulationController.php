<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\CancelledPostulationRequest;
// use App\Models\CancelledPostulation;
use Illuminate\Support\Facades\Auth;
use App\Models\Motive;
use App\Models\Postulation;

class CancelledPostulationController extends Controller
{
        public function store(CancelledPostulationRequest $request) 
        {           
                $user = $request->user();
                

                try{
                        $postulationCanceled = Postulation::withTrashed()
                                                                ->where('postulation_id',$request->postulation_id)
                                                                ->where('user_id',$user->id)
                                                                ->exists();
                        
                        if( $postulationCanceled )
                                return $this->error('This postulation already has cancelled by you',401);
                        
                        $ap = Postulation::find($request->postulation_id);
                        $ap->delete();
                        // $ap->user_id = $user->id;
                        // $ap->postulation_id = $request->postulation_id;
                        // Pidele a dominyel que los campos que vaya a enviar nulos o vacios
                        // no los agregue a la peticion de forma que no debamos validar esos casos

                        // if( $request->filled('motive_id') )//pendiente si se va a agregar motivo de cancelacion o no ==================

                        //         $ap->motive_id = $request->motive_id;
                        
                        // $ap->other_motive = $request->textMotive;
                        // $ap->status = 1;

                        // $ap->save();
                        return $this->success( $ap );

                }catch (\Exception $exception) {
                       return $this->error($exception->getMessage(), 401);
                }
        }

        // public function getCancelationMotives()
        // {                
        //         try{
        //                 $motives = Motive::activos()->where('type','cancelled')->get();
                        
        //                 if( !$motives->count() )
        //                         return $this->error('Motives not found',401);                                
                        
        //                 return $this->showAll($motives);
        //         }
        //         catch (\Exception $th) {
        //               return $this->error($th->getMessage(), 401);
        //         }
        // }

        public function getCancelationMotivesByPostulation(Postulation $postulation)
        {
                try{
                        $postulation->load(['motive']);
                        
                        if($postulation->motive->type == 'cancelled'){
                                return $this->showOne($postulation->motive);
                        }else{
                                return $this->error('Motives not found',401);
                        }
                }
                catch (\Exception $th) {
                        return $this->error($th->getMessage(), 401);
                }
                
        }
}
