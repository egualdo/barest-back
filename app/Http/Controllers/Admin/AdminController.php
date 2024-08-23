<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Models\Admin;
use App\Models\Motive;
use App\Models\PostType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class AdminController extends Controller
{
    public function getPermissions() {
        $permissions = Permission::select('id', 'name')
                                ->where('id', '>', 4)
                                ->get();

        return $this->showAll( $permissions );
    }

    public function getPostTypes() {
        $postTypes = PostType::select('id', 'name')
                            ->get();

        return $this->showAll( $postTypes );
    }

    public function getReportMotives() {
        $postTypes = Motive::activos()
                            ->review()
                            ->select('id', 'name')
                            ->where('type', 'reported')                            
                            ->orderBy('name')
                            ->get();

        return $this->showAll( $postTypes );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAdmins()
    {
        //$this->authorize('fetch', User::class);

        $admins = Admin::role('admin')
                    ->select(
                        'admins.id',
                        'admins.username',
                        'admins.email',
                        'admins.country_code',
                        'admins.phone_number',
                        'allocator.username as allocator_username',
                        'admins.created_at',                        
                    )
                    ->leftJoin('admins as allocator', 'admins.assigned_by_id', '=', 'allocator.id')
                    ->with('permissions:id,name')
                    ->orderBy('admins.created_at', 'desc')
                    ->get();

        return $this->showAll( $admins );
    }

    public function storeAdmin(StoreRequest $request) {
        
        $user_info = $request->safe()->except([ 'access' ]);

        DB::beginTransaction();
        try {
            
            $user_info['assigned_by_id'] = Auth::guard('admin_api')->user()->id;            
                        
            $user = Admin::create( $user_info );

            $user->assignRole('admin');
            $user->syncPermissions( $request->safe()['access'] );

            DB::commit();
        } catch (\Exception $th) {
            DB::rollback();
            return $this->error( $th->getMessage(), 401 );
        }

        return $this->success( $user );
    }

    public function updateAdmin(UpdateRequest $request, Admin $admin) {
        
        $user_info = $request->safe()->except([ 'access' ]);

        DB::beginTransaction();
        try {
            
            $admin->update( $user_info );

            $admin->syncPermissions( $request->safe()['access'] );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->error( $th->getMessage(), 401 );
        }

        return $this->success( $admin );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showAdmin(Admin $admin)
    {
        $admin->load('permissions:id,name');

        $admin->access = $admin->permissions->map( fn( $permission ) => $permission->id );        
        $admin->unsetRelations([ 'permissions ']);

        return $this->showOne( $admin );
    }

    public function destroyAdmin(Admin $admin) {
        //$this->authorize( 'destroy', $user );
        
        DB::beginTransaction();
        try {
            
            $admin->removeRole('admin');
            $admin->permissions()->detach();
            
            $admin->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 400);
        }

        return $this->success( true );
    }
}
