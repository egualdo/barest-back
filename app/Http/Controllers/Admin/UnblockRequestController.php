<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnblockRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show(Petition $petition)
    {  
        $petition->load([
            'applicant:id,username,picture,company_name,profession,city,block_reason'
        ]);

        $petition->applicant->role = $petition->applicant->getRole()->name;

        return $this->showOne( $petition );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUnblockRequests(Request $request)
    {
        $requests = Petition::select(
                                'petitions.id',
                                'users.username',
                                DB::raw('
                                    CASE
                                        WHEN roles.name = "user" THEN "Persona"
                                        ELSE "Empresa"
                                    END role
                                '),
                                'users.block_reason',
                                'petitions.created_at'                        
                            )
                            ->join('users', 'user_id', '=', 'users.id')
                            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                            ->where('users.status', 'BLOCKED')
                            ->where('petitions.status', 'PENDANT')
                            ->orderBy('petitions.created_at', 'desc');
        
        if( isset( $request['input'] ) && strlen( $request['input'] ) )
            $requests->where(function($query) use( $request ) {
                        $query->where('users.username', 'LIKE', "%{$request->input}%" );
                    });
        
        if( isset( $request['user_type'] ) && !in_array( 'admin', $request->input('user_type') ) )
            $requests->whereIn('roles.name', $request->input('user_type'));
        else if ( !isset( $request['user_type'] ) )
            $requests->where('roles.name', '!=', 'admin');

        $requests = $requests->paginate();

        return $this->showAll( Collect( $requests ) );
    }

    public function reject(Petition $petition) {

        try {
            
            $petition->status = 'REJECTED';
            $petition->update();
            
        } catch (\Exception $exc) {
            return $this->error($exc->getMessage(), 401);
        }

        return $this->success( true );
    }

    public function accept(Petition $petition) {

        DB::beginTransaction();
        try {
            
            $petition->status = 'ACCEPTED';
            $petition->update();
            
            $petition->load('applicant');

            $petition->applicant->status = 'ACTIVE';
            $petition->applicant->block_reason = NULL;
            $petition->applicant->update();            

            DB::commit();
        } catch (\Exception $exc) {
            DB::rollback();
            return $this->error($exc->getMessage(), 401);
        }

        return $this->success( true );
    }

}
