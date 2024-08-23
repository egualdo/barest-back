<?php

namespace App\Http\Controllers;

use App\Http\Requests\Landing\Postulation\StoreRequest;
use App\Http\Requests\Landing\ChatRoom\StoreRequest as ChatRoomRequest;
use App\Http\Requests\Landing\Postulation\UpdateRequest as PostulationUpdateRequest;
use Illuminate\Http\Request;
use App\Models\Postulation;
use App\Models\Post;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostulationController extends Controller
{
        public function store(StoreRequest $request)
        {
                $user = Auth::user()->load('reports_made');

                $postulations = Postulation::where('user_id', $user->id)->
                                        where('post_id', $request->post_id)->
                                        withTrashed()->
                                        get();

                if( $postulations->filter(fn($postulation) => !$postulation->deleted_at )->count() )
                        return $this->error("Already have an active postulation", 400);

                if( $user->reports_made->filter(fn($report) => $report->reportable_type === 'App\\Models\\Post' && $report->reportable_id == $request->post_id )->count() )
                        return $this->error("report-active-on-this-post", 400);


                DB::beginTransaction();
                try{
                        $post = Post::find($request->post_id);

                        if( $post->questions()->count() && !$request->filled('postulation_answers') )
                                return $this->error('you dont have answers to this post', 422);

                        $user->postPostulated()->attach($request->post_id,[
                                                                "stage_id" => 'pendant',//cambiar en el modelo y migracion por ENUM [] revissar usermigration
                                                                "created_at" => now(),
                                                                "updated_at" => now(),
                                                        ]);

                        $postulation = Postulation::where('post_id', $post->id)->
                                                where('user_id', $user->id)->
                                                where('stage_id', 'pendant')->
                                                first();

                        if( $post->questions()->count() ) {
                                $postulation->answers()->createMany($request->postulation_answers);
                        }

                        if( $user->resume ) {
                                $curriculumVitae = $user->resume;

                                $resume = [
                                        "path" => $curriculumVitae->getRawOriginal()['path'],
                                        "size" => $curriculumVitae->size,
                                        "nameFile" => $curriculumVitae->nameFile
                                ];

                                $postulation->resume()->create( $resume );
                        }

                        if( $request->hasFile('cv') && !$user->resume ) {
                                $curriculumVitae = $request->cv;

                                $resume = [
                                        "path" => $this->storeCv( $postulation, $curriculumVitae, "postulations"),
                                        "size" => $curriculumVitae->getSize(),
                                        "nameFile" => trim($curriculumVitae->getClientOriginalName())
                                ];

                                $postulation->resume()->create( $resume );
                        }

                        DB::commit();
                        return $this->success([ "is_postulated" => true, "postulation_id" => $postulation->id ]);
                }catch (\Exception $th) {
                        DB::rollback();
                        return $this->error($th->getMessage(), 400);
                }
        }

        public function getByUserId(User $user)
        {
                try{

                        $postulations = $user->postulations()
                                                ->with([
                                                        'user',
                                                        'post.user',
                                                        'post.categoryLevel2.categoryLevel1',
                                                ])
                                                ->orderBy('created_at','desc')
                                                ->paginate(8);

                        return $this->showAll(Collect( $postulations ));

                }catch (\Exception $th){
                return $this->error($th->getMessage(), 401);
                }
        }

        public function getJobsPostulationsByAuthUser()
        {
                try{
                        $user = Auth::user();

                        $postulations = Postulation::with([
                                                        'post',
                                                        'post.user',
                                                        'post.paymentModality:id,abbrev_name',
                                                        'post.categoryLevel2.categoryLevel1',
                                                ])
                                                ->where('user_id', $user->id)
                                                ->whereHas('post', function(Builder $query) {
                                                        $query->where('type_post_id', 3 );
                                                })
                                                ->orderBy('created_at','desc')
                                                ->paginate(8);

                        if( !$postulations )
                                return $this->error('no-results', 404);

                        return $this->showAll(Collect( $postulations ));
                }catch (\Exception $th){
                        return $this->error($th->getMessage(), 500);
                }
        }

        public function update(PostulationUpdateRequest $request, Postulation $postulation)
        {
                $postulation_info = $request->safe()->except(['cv']);

                DB::beginTransaction();
                try{
                        $postulation->update( $postulation_info );

                        if( $request->hasFile('cv') ){
                                $curriculumVitae = $request->file('cv');

                                $resume = [
                                        "path" => $this->updateCv( $postulation, $curriculumVitae ),
                                        "size" => $curriculumVitae->getSize(),
                                        "nameFile" => trim($curriculumVitae->getClientOriginalName())
                                ];

                                $postulation->resume()->update( $resume );
                        }

                        DB::commit();
                        return $this->success($postulation);
                }catch (\Exception $th) {
                        DB::rollback();
                        return $this->error($th->getMessage(), 400);
                }
        }

        public function cancel(Postulation $postulation)// cambia el status de la postulacion a cancelado
        {
                try{
                        $postulation->delete();

                        return $this->success(true);
                }catch (\Exception $th) {
                        return $this->error($th->getMessage(), 400);
                }
        }

        public function finalizedPostulation(Postulation $postulation) // cambia el status de la postulacion a finalizado
        {
                try{
                        if( !$postulation )
                                return $this->error('there is no results', 404);

                        $postulation->update([ 'stage_id' => 'completed' ]);
                        $postulation->with([ 'post', 'user' ])->get();

                        return $this->success($postulation);
                }catch (\Exception $th) {
                        return $this->error($th->getMessage(), 400);
                }
        }

        public function destroy(Request $request)
        {
                DB::beginTransaction();
                try{
                        foreach ($request->postulation_ids as $value) {

                                $postulation = Postulation::where('user_id', $value["user_id"])->
                                                        where('post_id', $value["post_id"])->
                                                        first();

                                if(!$postulation){
                                        return $this->error('There are no results', 404);
                                }

                                $postulation->delete();
                        }

                        DB::commit();
                        return $this->success(true);
                }catch (\Exception $th) {
                        DB::rollback();
                        return $this->error($th->getMessage(), 400);
                }
        }

        public function verifyPostulationUser(Request $request)
        {
                try{
                        $postulation = Postulation::where('user_id', $request->user_id)->
                                                where('post_id', $request->post_id)->
                                                withTrashed()->
                                                get();

                        foreach ($postulation as $value) {
                                if( is_null($value->deleted_at) )
                                        return $this->showMessage([ "is_postulated" => true, "postulation_id" => $value->id]);
                        }

                        return $this->showMessage([ "is_postulated" => false, "postulation_id" => null ]);
                }catch (\Exception $th) {
                        return $this->error($th->getMessage(), 400);
                }
        }

        public function showPostulationsToMyPosts($idStage = null)
        {
            try{
                $arr = [];

                $myPosts = Post::with(['contractTypes'])->
                                where('user_id',Auth::user()->id)->
                                where('type_post_id',3)->get();
                                // post del usuario logueado y que sea servicios

                if($idStage !== null && $idStage !== ''){
                    foreach ($myPosts as  $mypost) {
                                //buscamos postulaciones correspondientes a ese post particular
                        $up = Postulation::where('post_id',$mypost->id)->
                                        where('stage_id',$idStage)->
                                        with(['user'])->
                                        orderBy('created_at','desc')->get();

                        $mypost->postulations = $up;
                        array_push($arr,$mypost);
                    }
                }else{
                    foreach ($myPosts as  $mypost) {
                        //buscamos postulaciones correspondientes a ese post particular
                        $up = Postulation::where('post_id',$mypost->id)->
                                        with(['user'])->
                                        orderBy('created_at','desc')->get();

                        $mypost->postulations=$up;
                        array_push($arr,$mypost);
                    }
                }

                if(count($arr)>0){
                    return $this->success(collect($arr)->paginate(15),200);
                }else{
                    return $this->error('There is no results',404);
                }
            }catch (\Exception $th){
                return $this->error($th->getMessage(),500);
            }
        }

        public function createChatRoom(ChatRoomRequest $request)
        {
                $data = $request->validated();
                $user = Auth::user();

                try {

                        $exists = ChatRoom::activos()
                                        ->where('creator_id', $user->id)
                                        ->where('receiver_id', $request->receiver_id)
                                        ->exists();

                        if($exists)
                                return $this->showError('You have a opened chat with this user', 400);


                        $existsPostulation = $user->postulations()
                                                ->where( 'postulations.post_id', $request->post_id )
                                                ->exists();

                        if( $existsPostulation )
                                return $this->error('You already applied to this post', 400);

                        $chatRoom = ChatRoom::create($data);

                        return $this->success( $chatRoom );

                } catch (\Exception $exception) {
                        return $this->error($exception->getMessage(), 401);
                }
        }

        public function getById(Postulation $postulation)
        {
                try{

                        $postulation->load([
                                                'user',
                                                'post' => [
                                                        'user:id,username,picture,description',
                                                        'categoryLevel1',
                                                        'categoryLevel2',
                                                        'categoryLevel3',
                                                        'questions',
                                                        'paymentModality',
                                                        'modalities',
                                                ],
                                                'answers',
                                                'resume'
                                        ]);

                        return $this->showOne( $postulation );
                }catch (\Exception $th){
                        return $this->error($th->getMessage(), 500);
                }
        }

        public function leaveNotes(Request $request,Postulation $postulation){


                try {
                        $user = $request->user();

                        if($user->id != $postulation->post->user_id)
                                return $this->error('you can not leave a note in this postulation', 400);

                        $validator = Validator::make($request->all(), [
                                                                        'notes' => 'required|string|max:255'
                                                                ]);

                        if($validator->fails()){
                                return $this->error($validator->errors(), 422);
                        }

                        $postulation->notes = $request->notes;
                        $postulation->update();

                        return $this->success($postulation);
                } catch (\Exception $th) {
                        return $this->error($th->getMessage(), 500);
                }
        }


        public function deleteNotes(Postulation $postulation) {

                try {
                        $user = Auth::user();

                        if( $user->id != $postulation->post->user_id )
                                return $this->error('you can not delete a note in this postulation', 400);

                        $postulation->notes = null;
                        $postulation->update();

                        return $this->success($postulation);
                } catch (\Exception $th) {
                        return $this->error($th->getMessage(), 500);
                }
        }

}
