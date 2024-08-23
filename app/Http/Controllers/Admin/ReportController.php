<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
class ReportController extends Controller
{
    public function getTotalReports(Request $request) {
        try {
            $query = Report::join('users as reported', 'reports.reported_user_id', '=', 'reported.id')
                            ->join('posts', 'reports.post_id', '=', 'posts.id')
                            ->join('model_has_roles', 'reported.id', '=', 'model_has_roles.model_id')
                            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                            ->where('reports.status', 'PENDANT')
                            ->whereNull('reported.deleted_at');
                            //->havingRaw('COUNT(reports.reported_user_id) > 1');
                            
            if( isset( $request['input'] ) && strlen( $request['input'] ) )
                $query->where(function($query) use( $request ) {
                    $query->where('posts.title', 'LIKE', "%{$request->input}%" )
                        ->orWhere('reported.username', 'LIKE', "%{$request->input}%" );
                });

            if( isset( $request['user_type'] ) && !in_array( 'admin', $request->input('user_type') ) ) {            
                $query->whereIn('roles.name', $request->input('user_type'));
            } else if ( !isset( $request['user_type'] ) )
                $query->where('roles.name', '!=', 'admin');

            return $this->showMessage($query->count());
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), 401);
        }
    }

    public function getTotalPostsReports(Request $request) {
        try {
            $query = Report::join('users as reported', 'reports.reported_user_id', '=', 'reported.id')
                            ->whereHasMorph(
                                'reportable',
                                Post::class,
                                function (Builder $query) use ($request) {
                                    $query->join('post_types', 'posts.type_post_id', '=', 'post_types.id');
                                    
                                    if( isset( $request['input'] ) && strlen( $request['input'] ) )
                                        $query->where(function($subQuery) use( $request ) {
                                            $subQuery->where('posts.title', 'LIKE', "%{$request->input}%" )
                                                    ->orWhere('reported.username', 'LIKE', "%{$request->input}%" );
                                        });
                                }
                            )
                            ->where('reports.status', 'PENDANT')
                            ->whereNull('reported.deleted_at');;
                            //->havingRaw('COUNT(reports.reported_user_id) > 1');                    
                
            if( isset( $request['post_type'] ) )
                $query->whereIn('post_types.id', $request->input('post_type'));

            return $this->showMessage($query->count());
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), 400);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPostsReports(Request $request)
    {
        $reports = Report::select(
                            'reports.id',
                            'reports.reportable_id',
                            'posts.title',
                            'reported.id as reported_id',
                            'reported.username as reported_name',
                            'post_types.name as post_type',
                            DB::raw('COUNT(reports.reportable_id) as report_count'),
                            'reports.created_at',
                            DB::raw('
                                CASE
                                    WHEN reports.motive_id IS NOT NULL THEN motives.name
                                    ELSE reports.other_motive
                                END motive
                            ')
                        )
                        //->join('users as creator', 'reports.user_id', '=', 'creator.id')
                        ->join('motives', 'reports.motive_id', '=', 'motives.id')
                        ->join('users as reported', 'reports.reported_user_id', '=', 'reported.id')
                        ->join('posts', function(JoinClause $join) {
                            $join->on('reports.reportable_id', '=', 'posts.id')
                                ->where('reports.reportable_type', 'App\\Models\\Post');
                        })
                        ->join('post_types', 'posts.type_post_id', '=', 'post_types.id')
                        ->where('reports.status', 'PENDANT')
                        ->whereNull('reported.deleted_at')
                        ->groupBy('reports.reportable_id')
                        //->havingRaw('COUNT(reports.reported_user_id) > 1')
                        ->orderBy('report_count', 'desc')
                        ->orderBy('reports.created_at', 'desc');
        
        if( isset( $request['input'] ) && strlen( $request['input'] ) )
            $reports->where(function($query) use( $request ) {
                        $query->where('posts.title', 'LIKE', "%{$request->input}%" )
                            ->orWhere('reported.username', 'LIKE', "%{$request->input}%" );
                    });

        if( isset( $request['post_type'] ) )
            $reports->whereIn('post_types.id', $request->input('post_type'));
        
        $reports = $reports->paginate();

        return $this->showAll( Collect( $reports ) );
    }

    public function getTotalReviewsReports(Request $request) {
        try {
            $query = Report::join('users as reported', 'reports.reported_user_id', '=', 'reported.id')                            
                            ->whereHasMorph(
                                'reportable',
                                Review::class,
                                function (Builder $query) use ($request) {
                                    if( isset( $request['input'] ) && strlen( $request['input'] ) )
                                        $query->where(function($subQuery) use( $request ) {
                                            $subQuery->where('reported.username', 'LIKE', "%{$request->input}%" );                                                  
                                        });
                                }
                            )
                            ->join('motives', 'reports.motive_id', '=', 'motives.id')                            
                            ->where('reports.status', 'PENDANT')
                            ->whereNull('reported.deleted_at');;
                            //->havingRaw('COUNT(reports.reported_user_id) > 1');

            if( $request->filled('motive_id') )
                $query->where('motives.id', $request->input('motive_id'));

            if( $request->filled('dateFrom') && !$request->filled('last_days') )
                $query->whereDate('reports.created_at', '>=', $request->dateFrom );
    
            if( $request->filled('dateTo') && !$request->filled('last_days') )
                $query->whereDate('reports.created_at', '<=', $request->dateTo );
            
            if( $request->filled('lastDays') )
                $query->whereDate('reports.created_at', '>=', Carbon::now()->subdays($request->lastDays) );

            return $this->showMessage($query->count());
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage(), 401);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getReviewsReports(Request $request)
    {
        $reports = Report::select(
                            'reports.id',
                            'reports.reportable_id',                            
                            'reported.id as reported_id',
                            'reported.username as reported_name',
                            DB::raw('COUNT(reports.reportable_id) as report_count'),                            
                            'reports.created_at',
                            DB::raw('
                                CASE
                                    WHEN reports.motive_id IS NOT NULL THEN motives.name
                                    ELSE reports.other_motive
                                END motive
                            ')
                        )
                        ->join('reviews', function(JoinClause $join) {
                            $join->on('reports.reportable_id', '=', 'reviews.id')
                                ->where('reports.reportable_type', 'App\\Models\\Review');
                        })                        
                        ->join('users as reported', 'reports.reported_user_id', '=', 'reported.id')
                        ->join('motives', 'reports.motive_id', '=', 'motives.id')
                        ->where('reports.status', 'PENDANT')
                        ->groupBy('reports.reported_user_id')
                        //->havingRaw('COUNT(reports.reported_user_id) > 1')
                        ->orderBy('report_count', 'desc')
                        ->orderBy('reports.created_at', 'desc');
        
        if( isset( $request['input'] ) && strlen( $request['input'] ) )
            $reports->where(function($query) use( $request ) {
                        $query->where('reported.username', 'LIKE', "%{$request->input}%" );                     
                    });

        if( $request->filled('motive_id') )
            $reports->where('motives.id', $request->input('motive_id'));
        
        if( $request->filled('dateFrom') && !$request->filled('last_days') )
            $reports->whereDate('reports.created_at', '>=', $request->dateFrom );

        if( $request->filled('dateTo') && !$request->filled('last_days') )
            $reports->whereDate('reports.created_at', '<=', $request->dateTo );
        
        if( $request->filled('lastDays') )
            $reports->whereDate('reports.created_at', '>=', Carbon::now()->subdays($request->lastDays) );
        
        $reports = $reports->paginate();

        return $this->showAll( Collect( $reports ) );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getReports(Request $request)
    {
        $reports = Report::select(
                            'reports.id',
                            'reports.post_id',
                            'posts.title',
                            'reported.id as reported_id',
                            'reported.username as reported_name',
                            DB::raw('
                                CASE
                                    WHEN roles.name = "user" THEN "Persona"
                                    ELSE "Empresa"
                                END role
                            '),
                            DB::raw('COUNT(reports.reported_user_id) as report_count'),
                            'reports.created_at'
                        )
                        //->join('users as creator', 'reports.user_id', '=', 'creator.id')
                        ->join('users as reported', 'reports.reported_user_id', '=', 'reported.id')
                        ->join('posts', 'reports.post_id', '=', 'posts.id')
                        ->join('model_has_roles', 'reported.id', '=', 'model_has_roles.model_id')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->where('reports.status', 'PENDANT')
                        ->whereNull('reported.deleted_at')
                        ->groupBy('reports.post_id')
                        //->havingRaw('COUNT(reports.reported_user_id) > 1')
                        ->orderBy('reports.created_at', 'desc');
        
        if( isset( $request['input'] ) && strlen( $request['input'] ) )
            $reports->where(function($query) use( $request ) {
                        $query->where('posts.title', 'LIKE', "%{$request->input}%" )
                            ->orWhere('reported.username', 'LIKE', "%{$request->input}%" );
                    });

        if( isset( $request['user_type'] ) && !in_array( 'admin', $request->input('user_type') ) ) {            
            $reports->whereIn('roles.name', $request->input('user_type'));
        } else if ( !isset( $request['user_type'] ) )
            $reports->where('roles.name', '!=', 'admin');

        $reports = $reports->paginate();

        return $this->showAll( Collect( $reports ) );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showUserReported(User $reported)
    {
        $reported->load([
            'reports_related' => function($query) {
                $query->where('reportable_type', 'App\\Models\\Review')
                    ->where('status', 'PENDANT');
            },
            'reports_related.creator:id,username,picture,company_name,profession,city',
            'reports_related.reportable',            
            'reports_related.motive:id,name',
        ])
        ->loadCount('reviews_received');

        // $reported->username = "$reported->name $reported->last_name";
        $reported->role = $reported->getRole()->name;
        $reported->rate = $reported->calculateRanking();

        $reported->reports_related = $reported->reports_related->map(function($report) {
            
            $report->motive = !is_null($report->motive)
                                ? $report->motive->name
                                : $report->other_motive;
            
            // $report->creator->username = $report->creator->name." ".$report->creator->last_name;
            $report->unsetRelation('motive');
            
            return $report;
        });        

        $reported->unsetRelation('reports_related');

        return $this->showOne( $reported );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showPublicationReported(Report $report)
    {
        $report->load([
            'creator:id,username,picture,company_name,profession,city',
            'reported:id,username,picture,company_name,profession,city',
            'reportable:id,type_post_id,title,description,amount,city,category_1_id,category_2_id,category_3_id,experience_id' => [                
                'categoryLevel1:id,name',
                'categoryLevel2:id,name',
                'categoryLevel3:id,name',
                'experience:id,name',
                'contractTypes:id,name',
                'condition:id,name',
                'images:id,imageable_id,path',
            ],
            'motive:id,name',
        ]);

        $report->post_reported = $report->reportable;
        $report->unsetRelation('reportable');
        
        $report->motive = !is_null($report->motive)
                            ? $report->motive->name
                            : $report->other_motive;
        
        $report->unsetRelation('motive');        
        $report->reported->role = $report->reported->getRole()->name;
        
        $reportsOnPost = $report->post_reported->reports_received()
                                            ->count();
        
        if( $reportsOnPost > 1 ) {
            $report->post_reported->load([
                                    'reports' => function($query) {
                                        $query->select('id','creator_user_id','reported_user_id','reportable_id','motive_id','created_at')
                                            ->where('status', 'PENDANT');
                                    },
                                    'reports.motive:id,name',
                                    'reports.creator:id,username,picture',
                                ]);            
        }
        
        $report->post_reported->reports = $report->post_reported->reports_received->where('creator_user_id', '<>', $report->creator_user_id)->values();
        $report->post_reported->unsetRelation('reports');
        
        return $this->showOne( $report );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function discard(Report $report) {
        //$this->authorize( 'update', $user );
        
        DB::beginTransaction();
        try {
            
            $report->status = 'REJECTED';
            $report->update();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), 400);
        }

        return $this->success('report discarded');
    }
    
}
