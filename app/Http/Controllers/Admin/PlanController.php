<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Plan\UpdateStatusRequest;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::with('prices:plan_id,price')->get();
        
        $plans = $plans->map(function($plan) {
                            $plan->price = $plan->prices[0]->price;                            
                            $plan->unsetRelation('prices');
                            
                            return $plan;
                        });
        
        return $this->showAll( $plans );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        return $this->success( $plan );
    }

    /**
     * Update the status on multiple resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(UpdateStatusRequest $request)
    {   
        $plansUpdated = Collect( $request->validated()['plans_updated'] );
        
        DB::beginTransaction();        
        try {
            
            $plans = Plan::whereIn('id', $plansUpdated->pluck('id') )->get();
            
            foreach( $plans as $plan ) {                
                $plan->active = !$plan->active;                
                $plan->update();
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->error( $th->getMessage(), 400 );
        }
        
        return $this->success( true );
    }    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
