<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\Report\StoreRequest;
use App\Models\Motive;
use App\Models\Post;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
        private $classModel = [
                "user" => User::class,
                "post" => Post::class,
                "review" => Review::class,
        ];

        public function store(StoreRequest $request, $entity)
        {
                $validated = $request->safe()->except('reportable_id');
                $user = Auth::user();
                $model = $this->classModel[$entity]::find( $request->validated()['reportable_id'] );

                if( !$model )
                        return $this->error('model-not-found', 404);

                DB::beginTransaction();
                try{
                        $alreadyReported = $user->reports_made()->where( 'reportable_type', $this->classModel[$entity] )
                                                                ->where( 'reportable_id', $request->validated()['reportable_id'] )
                                                                ->exists();
                        
                        if( $alreadyReported )
                                return $this->error("This {$entity} already has reported by you", 400);

                        $report = null;
                        $validated['creator_user_id'] = $user->id;

                        switch ($entity) {
                                case 'post':
                                        $validated['reported_user_id'] = $model->user->id;
                                        
                                        $report = $model->reports_received()
                                                        ->create($validated);
                                        
                                        // $user->postulations()->where('post_id', $model->id)->delete();
                                        $user->postulations()->join('posts', function ($join) use ($model) {
                                                                                $join->on('postulations.post_id', '=', 'posts.id')
                                                                                        ->where('posts.user_id', $model->user->id);
                                                                        })->delete();
                                        break;
                                case 'review':
                                        $validated['reported_user_id'] = $model->user()->id;

                                        $report = $model->report_received()
                                                        ->create($validated);
                                        break;
                                case 'user':
                                        $validated['reported_user_id'] = $model->id;

                                        $report = $model->reports_received()
                                                        ->create($validated);

                                        $user->postulations()->join('posts', function ($join) use ($model) {
                                                                                $join->on('postulations.post_id', '=', 'posts.id')
                                                                                        ->where('posts.user_id', $model->id);
                                                                        })->delete();                                        
                                        break;
                                default:                                        
                                        break;
                        }                        
                        
                        DB::commit();
                        return $this->success( $report );                        
                }catch (\Exception $th) {
                        DB::rollback();
                        return $this->error($th->getMessage(), 400);
                }
        }

        public function getMotives($entity)
        {       
                try{
                        $motives = Motive::activos()
                                        ->select('id', 'name')
                                        ->where('type','reported')
                                        ->where('entity', $entity)
                                        ->get();

                        return $this->showAll( $motives );
                }
                catch (\Exception $th) {
                        return $this->error($th->getMessage(), 401);
                }
        }
        
}
