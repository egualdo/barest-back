<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Http\Requests\Admin\User\BlockUserRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsers(Request $request, $type)
    {
        //$this->authorize('fetch', User::class);
        
        $users = User::role($type)
                    ->select(
                        'users.id',
                        'users.username as name',
                        'users.email',
                        DB::raw('CONCAT(users.country_code, " ", users.phone_number) phone_number'),
                        'users.city',
                        'users.status',
                        DB::raw('
                            CASE
                                WHEN roles.id = 1 THEN users.profession
                                ELSE users.company_name
                            END prof_or_comp
                        '),
                        'roles.name as role'
                    )
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->groupBy('users.id')
                    ->orderBy('users.created_at', 'desc');
        
        if( $request->filled('input') )
            $users->where(function($query) use( $request, $type ) {
                $query->where(DB::raw('users.username'), 'LIKE', "%{$request->input}%")
                    ->orWhere('email', 'LIKE', "%{$request->input}%" )
                    ->orWhere(DB::raw('CONCAT(users.country_code, users.phone_number)'), 'LIKE', "%{$request->input}%" )
                    ->orWhere(DB::raw('CONCAT(users.country_code, " ", users.phone_number)'), 'LIKE', "%{$request->input}%" )
                    ->orWhere('city', 'LIKE', "%{$request->input}%" );

                if( $type == 'user' )
                    $query->orWhere('profession', 'LIKE', "%{$request->input}%" );
                else if( $type == 'company' )
                    $query->orWhere('company_name', 'LIKE', "%{$request->input}%" );
            });

        $users->where(function( $query ) use( $request ) {
            if( isset( $request['status'] ) && in_array( 'ACTIVE', $request['status'] ) )
                $query->where('status', 'ACTIVE');
    
            if( isset( $request['status'] ) && in_array( 'BLOCKED', $request['status'] ) )
                $query->orWhere('status', 'BLOCKED');
        });

        if( $request->filled('cities') )
            $users->whereIn('city', $request->query('cities') );

        $users = $users->paginate();

        return $this->showAll(Collect( $users ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {   
        $user->load([            
            'payments_made:id,user_id,concept,mount,payment_method,created_at',
            'reviews_received:id,ranking,comment,answer,created_at,answered_at,reviewer_user_id,ranked_user_id',
            'reviews_received.user:id,username,username,picture',
            'reviews_received.rankedUser:id,username,picture',
        ]);

        // $user->name = "$user->name $user->last_name";

        $user->review_rating = $user->calculateRanking();

        $user->payments_made = $user->payments_made->sortBy([
                                                        ['created_at', 'desc'],
                                                        ['mount', 'desc']
                                                    ])
                                                    ->values()
                                                    ->all();        
        $user->unsetRelation('payments_made');
        
        return $this->showOne( $user );
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showPublisher(User $user)
    {   
        $user->load([
            'posts' => [
                'categoryLevel1:id,name',
                'categoryLevel2:id,name',
                'categoryLevel3:id,name',
                'experience:id,name',
                'contractTypes:id,name',
                'paymentModality:id,abbrev_name',
                'images:id,imageable_id,path',
                'condition:id,name'
            ]
        ]);

        // $user->name = "$user->name $user->last_name";
        $user->role = $user->getRole()->name;
        
        $user->posts->each(function($post) {
            $post->contractTypes = $post->contractTypes->pluck('name');
            $post->unsetRelation('contractTypes');
        });
            
        return $this->showOne( $user );
    }

    public function getPublishers(Request $request, $type)
    {
        //$this->authorize('fetch', User::class);

        $users = User::role($type)
                    ->has('posts')
                    ->select(
                        'users.id',
                        'users.username',
                        'users.email',
                        'users.created_at',
                        'users.city',
                        'users.status',
                        DB::raw('
                            CASE
                                WHEN roles.id = 1 THEN users.profession
                                ELSE users.company_name
                            END prof_or_comp
                        '),
                        'roles.name as role'
                    )
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->orderBy('users.created_at', 'desc');
        
        if( $request->filled('input') )
            $users->where(function($query) use( $request, $type ) {
                $query->where('users.username', 'LIKE', "%{$request->input}%")
                    ->orWhere('email', 'LIKE', "%{$request->input}%" )
                    ->orWhere('city', 'LIKE', "%{$request->input}%" );

                if( $type == 'user' )
                    $query->orWhere('profession', 'LIKE', "%{$request->input}%" );
                else if( $type == 'company' )
                    $query->orWhere('company_name', 'LIKE', "%{$request->input}%" );
            });

        $users->where(function( $query ) use( $request ) {
            if( $request->filled('status') && in_array( 'ACTIVE', $request['status'] ) )
                $query->where('users.status', 'ACTIVE');
    
            if( $request->filled('status') && in_array( 'BLOCKED', $request['status'] ) )
                $query->orWhere('users.status', 'BLOCKED');
        });
        
        if( $request->filled('cities') )
            $users->whereIn('city', $request->query('cities') );

        $paginatedResults = Collect($users->paginate());
        $paginatedResults['data'] = Collect($paginatedResults['data'])->map(function($item) {
            $postsCount = Post::where('user_id', $item['id'])->count();
            $item['posts_count'] = $postsCount;

            return $item;
        })->toArray();
        
        return $this->showAll($paginatedResults);
    }

    public function countTotal($type)
    {
        //$this->authorize('fetch', User::class);

        $count = User::role($type)->count();

        return $this->success( $count );
    }

    public function block(BlockUserRequest $request, User $user, $fromReports = false) {
        //$this->authorize( 'update', $user );
        
        $motive = $request->validated()['motive'];
        
        DB::beginTransaction();
        try {
            
            $user->status = 'BLOCKED';
            $user->block_reason = $motive;
            $user->update();

            if($fromReports) {
                $user->reports_related()->update([ 'status' => 'ACCEPTED' ]);
                $user->reports_made()->update([ 'status' => 'REJECTED' ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 400);
        }

        return $this->success( $user );
    }

    public function unblock(User $user) {
        //$this->authorize( 'update', $user );
        
        DB::beginTransaction();
        try {

            
            $user->status = 'ACTIVE';
            $user->block_reason = NULL;
            $user->update();
            
            $user->petition()->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->success( $user );
    }

    public function destroy(User $user, $fromReports = false) {
        //$this->authorize( 'destroy', $user );
        
        DB::beginTransaction();
        try {
            
            
            $user->reports_related()->update([ 'status' => 'ACCEPTED' ]);
            $user->reports_made()->delete();

            $user->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 400);
        }

        return $this->success( true );
    }   

}
