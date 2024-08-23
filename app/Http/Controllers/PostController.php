<?php

namespace App\Http\Controllers;

use App\Enums\StagePostulationEnum;
use App\Http\Requests\Landing\Image\UploadImage;

use App\Http\Requests\Landing\Post\Products\StoreRequest as StoreRequestProducts;
use App\Http\Requests\Landing\Post\Products\UpdateRequest as UpdateRequestProducts;
use App\Http\Requests\Landing\Post\Services\StoreRequest as StoreRequestServices;
use App\Http\Requests\Landing\Post\Services\UpdateRequest as UpdateRequestServices;
use App\Http\Requests\Landing\Post\Services\FilterGlobalRequest as FilterGlobalServices;
use App\Http\Requests\Landing\Post\Jobs\FilterGlobalRequest as FilterGlobalJobs;
use App\Http\Requests\Landing\Post\Products\FilterGlobalRequest as FilterGlobalProducts;

use App\Http\Requests\Landing\Post\Jobs\StoreRequest as StoreRequestJobs;
use App\Http\Requests\Landing\Post\Jobs\UpdateRequest as UpdateRequestJobs;
use App\Http\Requests\Landing\Post\FilterPostByPriorityRequest;
use App\Http\Requests\Landing\Post\FilterPostsByPriorityAndTypeRequest;
use App\Http\Requests\Landing\Post\AroundRequest;
use App\Models\Category;
use App\Models\Experience;
use App\Models\Post;
use App\Models\User;
use Intervention\Image\Facades\Image;
use App\Models\FavoritePostUser;
use App\Models\Idiom;
use App\Models\Image as ModelsImage;
use App\Models\Postulation;
use App\Models\Requirement;
use App\Models\Review;
use App\Notifications\DeletePost;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Tymon\JWTAuth\Facades\JWTAuth;
use Notification;
use Spatie\Permission\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

use function Aws\map;

class PostController extends Controller
{
  public function isFavorite($arrPost, $paginate=true){
    $userid= Auth::user()->id;
    $fav= FavoritePostUser::where('user_id',$userid)->get();
    if(!isset($arrPost->id)){
      foreach ($fav as  $value) {
        foreach ($arrPost as  $value2) {
          if($value->post_id == $value2->id){
            $value2->favorite=true;
          }
        }
      }
    }else{
      foreach ($fav as  $value) {
          if($value->post_id == $arrPost->id){
            $arrPost->favorite=true;
          }
      }
    }


    if($paginate){
      return $arrPost->paginate(15);
    }

    // dd($arrPost);
    return $arrPost;
  }

  public function all(Request $request, $post_type_id = null)//publico y privado
  {
    $withFavorite = $request->user() ? true : false;
    $postsResults = [];
    try {
        $posts = Post::activos()
                    ->with([
                        'user',
                        'postType',
                        'categoryLevel1',
                        'categoryLevel2',
                        'categoryLevel3',
                        'images',
                        'experience',
                        'paymentModality',
                        'modalities',
                        'idioms',
                        'contractTypes',
                        'condition'
                    ])
                    ->orderBy('priority','desc')
                    ->orderBy('created_at', 'desc');

        if( !is_null( $post_type_id ) )
            $posts->where('type_post_id', $post_type_id);

        if($withFavorite)
            $postsResults = $this->isFavorite( $posts->get(), false );
        else
            $postsResults = $posts->paginate(15);

        return $this->success( $postsResults->paginate(15) );
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function storeProducts(StoreRequestProducts $request)
  {
    $validated = $request->safe()->except(['pictures']);
    $validated['type_post_id'] = 2;
    $user = Auth::user();

    DB::beginTransaction();
    try{
      $post = $user->posts()->create($validated);

      $arrImgNew = $request->validated()['pictures'];
      $files = [];

      foreach ($arrImgNew as $val) {
        $obj = (object) array('file' => $val['file'], 'cover' => isset( $val['cover'] ));
        array_push($files, $obj);
      }

      $storedImages = $this->storeAdImages( $post, $files, 960, true);
      $post->images()->createMany( $storedImages );
      $storedCover = $this->storeThumbnail( $post, $request->pictures[0]['file']);
      $post->picture = $storedCover;
      $post->update();

      DB::commit();
      return $this->success($post);
    }catch (\Exception $th) {
      DB::rollback();
      return $this->error($th->getMessage(), 401);
    }
  }

  public function storeServices(StoreRequestServices $request)
  {     $validated = $request->safe()->except(['cv', 'contract_types', 'questions']);
        $validated['type_post_id'] = 1;
        $user = Auth::user();

        DB::beginTransaction();
        try{
            $post = $user->posts()->create($validated);

            DB::commit();
            return $this->success($post->load(['modalities','paymentModality']) );
        }catch (\Exception $th) {
            DB::rollback();
            return $this->error($th->getMessage(), 500);
        }
  }

  public function storeJobs(StoreRequestJobs $request)
  {
    $validated = $request->safe()->except(['cv', 'contract_types', 'questions']);

    $validated['type_post_id'] = 3;

    $user = Auth::user();

    DB::beginTransaction();
    try{

      $post = $user->posts()->create($validated);

      if( $user->getRole()->id == 2 && $request->filled('questions') )
        $post->questions()->createMany( $request->validated()['questions'] );

      if( $request->hasFile('cv') ){

        $cv = $request->cv;

        $storedCv = [
                    "path" => $this->storeCv( $post, $cv, "posts"),
                    "size" => $request->file('cv')->getSize(),
                    "nameFile" => trim($request->file('cv')->getClientOriginalName())
                  ];

        $post->resume()->create( $storedCv );
      }

      if( isset($request->validated()['idioms']) ){
        $arr=[];

        foreach ($request->validated()['idioms'] as $value) {

          $idiom = Idiom::where('name','like',"%".$value['name']."%")->first();

          if(!$idiom){
            $idiom = Idiom::create([
                              "name"=>$value['name']
                            ]);
          }

          $arr[$idiom->id] = [ 'level' => $value['level'] ];
        }

        $post->idioms()->sync( $arr );
      }

      if( $request->filled('contract_types') ){
        $post->contractTypes()->sync($request->validated()['contract_types']); //cambiar desde dominyel
      }

      $returnTypeDefault = [ 'contractTypes', 'idioms', 'modalities', 'paymentModality' ];

      if( $user->getRole()->id == 2 ) array_push($returnTypeDefault, 'questions');

      DB::commit();
      return $this->success($post->load($returnTypeDefault));
    }catch (\Exception $th) {
      DB::rollback();
      return $this->error($th->getMessage(), 400);
    }
  }

  public function show(Request $request, Post $post)
  {
    $withFavorite = $request->user() ? true : false;

    try{

      $post->load([
        'user',
        'postType',
        'categoryLevel1',
        'categoryLevel2',
        'categoryLevel3',
        'images',
        'experience',
        'paymentModality',
        'modalities',
        'idioms:id,name',
        'contractTypes',
        'condition',
        'resume',
        'questions',
        'postulatedUsers',//hacer un array con los 3 tipos de stage y dentro los usuarios que tengan ese stage
        'postulations' => [
          'user'
        ]
      ]);

      $stages = Collect( StagePostulationEnum::toArray() )->map(fn() => []);

      $postulationStages = $stages->map(function($_, $stage_key) use( $post ) {
                                          return $post->postulations->filter(fn($postulation) => $postulation->stage_id == $stage_key)
                                                                    ->values()
                                                                    ->all();
                                        });

      $post->postulations_segmented = $postulationStages;

      $post->user->ranking = $post->user->calculateRanking();
      $post->user->reviews = Review::where('ranked_user_id',$post->user->id)->count();
      $post->user->role = $post->user->getRole()->name;

      if( auth()->check() ) {
        $post->user->already_reported = $post->user->reports_received()->where('creator_user_id', auth()->user()->id )->exists();
        $post->already_reported = $post->reports_received()->where('creator_user_id', auth()->user()->id )->exists();
      }

      $postResult = null;

      if( $withFavorite ){
        $postResult = $this->isFavorite($post, false);
      }else{
        $postResult = $post;
      }

      return $this->showOne($postResult);
    }catch (\Exception $th){
      return $this->error($th->getMessage(), 400);
    }
  }

  public function showJobOfferWithPostulationsDetails(Request $request, Post $post)
  {
    try{
      $post->load([
        'paymentModality:id,name,abbrev_name',
        'contractTypes:id,name',
        'postulations:id,user_id,post_id,stage_id,notes,created_at' => [
          'user:id,picture,username,profession,city,email,country_code,phone_number,description'
        ]
      ]);

      $stages = Collect( StagePostulationEnum::toArray() )->map(fn() => []);

      $postulationStages = $stages->map(function($_, $stage_key) use( $post ) {
                                          return $post->postulations->filter(fn($postulation) => $postulation->stage_id == $stage_key)
                                                                    ->values()
                                                                    ->all();
                                        });

      $post->postulations_segmented = $postulationStages;
      $post->unsetRelation('postulations');

      return $this->showOne($post);
    }catch (\Exception $th){
      return $this->error($th->getMessage(), 500);
    }
  }

  public function updateProducts(UpdateRequestProducts $request, Post $post)
  {
    $post_info = $request->safe()->except([ 'pictures' ]);// ,'contract_type';

    DB::beginTransaction();

    try {
      $post->update($post_info);

      if( $request->filled('pictures')){

        $pic = $request->pictures[0];

        if( isset($pic['id']) ){
          $imgFind = ModelsImage::find($pic['id']);
          $imgFind->cover = true;
          $imgFind->update();                                                // nueva portada desde la misma galeria existente
          $this->destroyStoredImage( $post->picture );                    // eliminamos en el s3 la portada vieja aparte

          $storedCover = $this->storeThumbnail($post, $imgFind->path);   //guardamos la nueva en s3
          $post->picture = $storedCover;                                   //actualizamos en el post la portada nueva
          $post->update();
        }

        if(isset($pic['cover'])){           //si la imagen no tiene id y si es portada, entonces se sube a la galeria y portada
          $storedCover = $this->storeThumbnail( $post, $pic['file']);     //guardamos la nueva en s3
          $post->picture= $storedCover;                                   //actualizamos en el post la portada nueva
          $post->update();
        }

        //Aqui cambiaria un poco la cosa, habria que generar un array nuevo a partir de request->picture
        //sacando la imagen que tenga id porque esta ya existe en la galeria, y si el array resultante esta
        //vacio sera porque solamente se cambio imagen de portada por lo que no hay que agregar nuevas imagenes a la galeria
        //si la imagen no tiene id y no es portada, entonces se sube a la galeria

        $arrImgNew = $request->pictures;
        $files = [];

        foreach ($arrImgNew as $value) {

          if( !isset($value['id']) ){

            $obj = (object) array('file' => $value['file'], 'cover' => isset( $value['cover'] ));
            array_push($files, $obj);
          }
        }

        if( count($files) ) {
          $storedImages = $this->storeAdImages( $post, $files,960,false);
          $post->images()->createMany( $storedImages );
        }
      }

      DB::commit();
      return $this->success( $post->load(['contractTypes','idioms','modalities','paymentModality']) );
    } catch (\Exception $exception) {
      DB::rollback();
      return $this->error($exception->getMessage(), 401);
    }
  }

  public function updateServices(UpdateRequestServices $request, Post $post)
  {
    $post_info = $request->safe()->except(['contract_types']);

    DB::beginTransaction();

    try {
      $post->update($post_info);


      DB::commit();
      return $this->success( $post->load([ 'contractTypes','idioms','modalities','paymentModality']) );
    } catch (\Exception $exception) {
      DB::rollback();
      return $this->error($exception->getMessage(), 401);
    }
  }

  public function updateJobs(UpdateRequestJobs $request, Post $post)
  {
    $post_info = $request->safe()->except([ 'cv', 'picture', 'contract_types' ,'questions' ]);
    $user = Auth::user();

    DB::beginTransaction();

    try {
      $post->update($post_info);

      if( $user->getRole()->id == 2 && $request->filled('questions') ){

        if(!isset($request->questions[0]) ){
          $post->questions()->whereIn('post_id', [$post->id] )->update(['old'=>1]);
          $post->questions()->whereIn('post_id', [$post->id] )->delete();
        }else{

          $exist_id=array_search(true, array_map(fn($valor)=>isset($valor['id']),$request->validated()['questions']));
          if(!$exist_id ){
            $post->questions()->whereIn('post_id', [$post->id] )->delete();

            foreach ($request->validated()['questions'] as $element) {
              $post->questions()->create( ['question'=>$element['question']] );
            }
          }else{
            foreach ($request->validated()['questions'] as $value) {

              if(isset($value['id'])){
                $questionFound=$post->questions()->where('id',$value['id'])->where('question','!=',$value['question'])->first();

                if($questionFound){
                  $value['id']=(int)$value['id'];
                  $params_to_save=['question'=>$value['question'],'old'=>1];
                  unset($value['question']);
                  $post->questions()->updateOrCreate( $value,$params_to_save );
                }
              }else{
                $post->questions()->create( ['question'=>$value['question']] );
              }
            }
          }
        }

      }


      if( $request->hasFile('cv') ){

        $curriculumVitae = $request->file('cv');

        $storedCvPath = null;

        if( is_null( $post->resume ) )
            $storedCvPath = $this->storeCv( $post, $curriculumVitae, "posts");
        else
            $storedCvPath = $this->updateCv( $post, $curriculumVitae );

        $resume = [
                  "path" => $storedCvPath,
                  "size" => $curriculumVitae->getSize(),
                  "nameFile" => trim($curriculumVitae->getClientOriginalName())
                ];

        if( is_null( $post->resume ) )
          $post->resume()->create( $resume );
        else
          $post->resume->update( $resume );
      }

      if( $request->filled('contract_types') ){
        $post->contractTypes()->sync( $request->safe()['contract_types'] );
      }

      if( $request->filled('idioms') ){
        $arr=[];

        foreach ($request->validated()['idioms'] as $value) {

          $idiom = Idiom::where('name','like',"%".$value["name"]."%")->first();

          if(!$idiom){
            $idiom = new Idiom();
            $idiom->name = $value["name"];
            $idiom->save();
          }

          $arr[$idiom->id] = [ 'level' => $value['level'] ];

        }

        $post->idioms()->sync( $arr );
      }

      if( $request->filled('picture')){

        $pic = $request->picture[0];

        if($pic['id']){                                                   //caso en que actualice la portada con las imagenes de la galeria existente
          $imgFind = ModelsImage::find($pic['id']);                         // nueva portada desde la misma galeria existente
          $this->destroyStoredImage( $post->picture );                    // eliminamos en el s3 la portada vieja aparte

          $storedCover = $this->storeThumbnail($post, $imgFind->path);   //guardamos la nueva en s3
          $post->picture = $storedCover;                                   //actualizamos en el post la portada nueva
          $post->update();
        }

        if(filter_var($pic['cover'], FILTER_VALIDATE_BOOLEAN)){           //si la imagen no tiene id y si es portada, entonces se sube a la galeria y portada
          $storedCover = $this->storeThumbnail( $post, $pic['file']);     //guardamos la nueva en s3
          $post->picture= $storedCover;                                   //actualizamos en el post la portada nueva
          $post->update();
        }

        //Aqui cambiaria un poco la cosa, habria que generar un array nuevo a partir de request->picture
        //sacando la imagen que tenga id porque esta ya existe en la galeria, y si el array resultante esta
        //vacio sera porque solamente se cambio imagen de portada por lo que no hay que agregar nuevas imagenes a la galeria
        //si la imagen no tiene id y no es portada, entonces se sube a la galeria

        if(!$pic['id'] ){
          $arrImgNew=$request->picture;

          foreach ($arrImgNew as $value) {

            if(isset($value['id']))
              unset($value);
          }

          if(count($arrImgNew)){
            $files=[];

            foreach ($arrImgNew as $val) {
               $obj = (object) array('file' => $val['file'],'cover' => isset($value['cover']));
              array_push($files,$obj);
            }

            $storedImages = $this->storeAdImages( $post, $files,960,false );
            $post->images()->createMany( $storedImages );
          }
        }
      }

        $returnTypeDefault = [ 'contractTypes', 'idioms', 'modalities', 'paymentModality', 'resume'];

      if( $user->getRole()->id == 2 ) array_push($returnTypeDefault, 'questions');

      DB::commit();
      return $this->success( $post->load($returnTypeDefault));
    } catch (\Exception $exception) {
      DB::rollback();
      return $this->error($exception->getMessage(), 400);
    }
  }

  public function destroy(Post $post)
  {
    try{
      $post->delete();

      return $this->success(true);
    }catch (\Exception $th) {
      return $this->error($th->getMessage(), 500);
    }
  }

  public function filterPostsByPriority(FilterPostByPriorityRequest $request)
  {
    $validatedData = $request->validated();

    $priority = $request->query('priority');

    try {
        $urgentPosts = Post::activos()
            ->with([
              'user',
              'postType',
              'categoryLevel1',
              'categoryLevel2',
              'categoryLevel3',
              'images',
              'experience',
              'paymentModality',
              'modalities',
              'idioms',
              'contractTypes',
              'condition'
            ])
            ->where('priority', $priority);

        if( $request->filled('keyword') )
            $urgentPosts->where('title', 'LIKE', '%'. $request->keyword . '%')
                ->orWhere('description', 'LIKE', '%'. $request->keyword .'%');

        if( $request->filled('category') )
            $urgentPosts->where('category_2_id',  $request->category );

        if( $request->filled('type_post') )
            $urgentPosts->where('type_post_id',  $request->type_post );

        if( $request->filled('user_id') )
            $urgentPosts->where('user_id',  $request->user_id );

        if (!empty($request->sort)) {
            $sort  = explode('-', $request->sort);
            $urgentPosts->orderBy($sort[0], $sort[1]);
        }

        $urgentPosts->orderBy('priority', 'desc')->get();

        $postsResults = $this->isFavorite( $urgentPosts );

        return $this->success( $postsResults );

    }catch (\Exception $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function getPostsByPriority(Request $request)
  {
    $request->validate([
      'priority' => 'required|numeric'
    ]);

    $priority = $request->query('priority');
    try {
      $posts = Post::activos()
                  ->with([
                      'user',
                      'postType',
                      'categoryLevel1',
                      'categoryLevel2',
                      'categoryLevel3',
                      'images',
                      'experience',
                      'paymentModality',
                      'modalities',
                      'idioms',
                      'contractTypes',
                      'condition'
                  ])
                  ->where('priority', $priority)
                  ->orderBy('created_at', 'desc')
                  ->get();

      $posts = $this->isFavorite( $posts );

      return $this->success( $posts );
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function filterGlobalServices(FilterGlobalRequestServices $request)//urgentes y no urgentes
  {
    $validatedData = $request->validated();

    try {
        $urgentPosts = Post::activos()
                      ->with([
                        'user',
                        'postType',
                        'categoryLevel1',
                        'categoryLevel2',
                        'categoryLevel3',
                        'images',
                        'experience',
                        'paymentModality',
                        'modalities' ,
                        'idioms',
                        'contractTypes',
                        'condition'
                      ])
                      ->where('type_post_id', 1)
                      ->orderBy('priority', 'DESC');

        if( $request->filled('title') )
            $urgentPosts->where('title', 'LIKE', '%'. $request->title . '%')
                    ->orWhere('description', 'LIKE', '%'. $request->title . '%');

        if( $request->filled('category_id') )
            $urgentPosts->where('category_2_id',  $request->category_id );

        if( $request->filled('experience_id') )
            $urgentPosts->where('experience_id',  $request->experience_id );

        if( $request->filled('contract_type_id') )
            $urgentPosts->whereHas('contractTypes', function($query) use ($request) {
                                                    return $query->where('id', $request->contract_type_id);
                                                });

        if( $request->filled('sort') ) {
            // sort= amount-desc / amount-desc / created_at-asc / created_at-desc / relevantes falta por filtrar
            $sort  = explode('-', $request->sort);
            $urgentPosts->orderBy($sort[0], $sort[1]);
        }

        $postsResults = $this->isFavorite( $urgentPosts->get() );

        return $this->showAll(Collect( $postsResults ));
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function filterGlobalProducts(FilterGlobalProducts $request)//urgentes y no urgentes cercanos al usuario
  {
    try {
        $urgentPosts = Post::activos()
            ->with([
                'user',
                'postType',
                'categoryLevel1',
                'categoryLevel2',
                'categoryLevel3',
                'images',
                'experience',
                'paymentModality',
                'modalities',
                'idioms',
                'contractTypes',
                'condition'
            ])
            ->where('type_post_id', 2)
            ->orderBy('priority', 'desc');

        if( $request->filled('title') )
            $urgentPosts->where('title', 'LIKE', '%'. $request->title . '%')
                        ->orWhere('description', 'LIKE', '%'. $request->title . '%');

        if( $request->filled('category_id') )
            $urgentPosts->where('category_2_id',  $request->category_id );

        if( $request->filled('sort') ) {
            // sort= amount-asc / amount-desc / created_at-asc / created_at-desc / relevantes falta por filtrar
            $sort  = explode('-', $request->sort);
            $urgentPosts->orderBy($sort[0], $sort[1]);
        }

        $anuncios = $this->isFavorite( $urgentPosts->get(), false );

        /* Coords. de prueba
        "lat":"41.3873974",
        "long":"2.168568", */
        //Algoritmo para el calculo del rango
        if( $request->filled('lat') && $request->filled('long') ) {
            $lat = $request->lat;//lo que viene del request o la posicion del usuario logueado
            $lon = $request->long;//lo que viene del request o la posicion del usuario logueado

            // Calculamos la distancia de cada anuncio a la ubicación del usuario(request)
            $anunciosConDistancia = array();
            $arrayRange = array();

            foreach ($anuncios as $anuncio) {
                $distancia = $this->calcularDistancia( $lat, (float)$anuncio->lat, $lon, (float)$anuncio->long );
                $anunciosConDistancia[] = array('anuncio' => $anuncio, 'distancia' => $distancia/1000);
            }

            if( $request->filled('range_b') && $request->filled('range_b') && $request->range_b > 0 ) {
                foreach( $anunciosConDistancia as $anuncio ) {
                    if( (float)$anuncio["distancia"] <= (float)$request->range_b )
                        array_push($arrayRange, $anuncio );
                }

                $anunciosOrdenados = Collect( $arrayRange )->sortBy('distancia')->pluck('anuncio');
            } else
                $anunciosOrdenados = Collect( $anunciosConDistancia )->sortBy('distancia')->pluck('anuncio');
        }else
            $anunciosOrdenados = $anuncios;

        return $this->showAll(Collect ( $anunciosOrdenados->paginate(20) ));

    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function filterGlobalJobs(FilterGlobalJobs $request)//urgentes y no urgentes
  {
    try {
        $urgentPosts = Post::activos()
                      ->with([
                        'user',
                        'postType',
                        'categoryLevel1',
                        'categoryLevel2',
                        'categoryLevel3',
                        'images',
                        'experience',
                        'paymentModality',
                        'modalities' ,
                        'idioms',
                        'contractTypes',
                        'condition'
                      ])
                      ->where('type_post_id', 3)
                      ->orderBy('priority', 'desc');

        if( $request->filled('title') )
            $urgentPosts->where('title', 'LIKE', '%'. $request->title . '%')->orWhere('description', 'LIKE', '%'. $request->title . '%');

        if( $request->filled('category_id') )
            $urgentPosts->where('category_2_id',  $request->category_id );

        if( $request->filled('user_id') )
            $urgentPosts->where('user_id',  $request->user_id );

        if( !empty($request->sort) ) {
            // sort= amount-desc / amount-desc / created_at-asc / created_at-desc / relevantes falta por filtrar
            $sort  = explode('-', $request->sort);
            $urgentPosts->orderBy($sort[0], $sort[1]);
        }

        $postsResults = $this->isFavorite( $urgentPosts->get() );

        return $this->showAll(Collect( $postsResults ));
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function filterPostsByPriorityAndType(FilterPostsByPriorityAndTypeRequest $request) //filterJobs cambiar en la ruta
  {
    $request->validated();
    $priority = $request->query('priority');
    $postTypeId = $request->query('type_post_id');

    try {
      $urgentPosts = Post::activos()
                      ->with([
                        'user',
                        'postType',
                        'categoryLevel1',
                        'categoryLevel2',
                        'categoryLevel3',
                        'images',
                        'experience',
                        'paymentModality',
                        'modalities' ,
                        'idioms',
                        'contractTypes',
                        'condition'
                      ])
                      ->where('priority', $priority)
                      ->where('type_post_id', $postTypeId)
                      ->orderBy('priority', 'desc');

      if( $request->filled('title') )
        $urgentPosts->where('title', 'LIKE', '%'. $request->title . '%')->orWhere('description', 'LIKE', '%'. $request->title .'%');

      if( $request->filled('category_id') )
        $urgentPosts->where('category_2_id',  $request->category_id );

      if( $request->filled('user_id') )
        $urgentPosts->where('user_id',  $request->user_id );

      if( !empty($request->sort) ){
        $sort  = explode('-', $request->sort);
        $urgentPosts->orderBy($sort[0], $sort[1]);
      }

      $postsResults = $this->isFavorite( $urgentPosts->get() );

      return $this->showAll(Collect( $postsResults ));
    } catch (\Throwable $th) {
      return $this->error($th->getMessage(), 500);
    }
  }

  public function getPostsFiltered(Request $request, $post_type_id)
  {
    $payment_modality_id = $request->query('payment_modality_id');
    $categoriesLvl1 = explode(',',$request->query('categories_lvl1'));
    $categoriesLvl2 = explode(',',$request->query('categories_lvl2'));
    $categoriesLvl3 = explode(',',$request->query('categories_lvl3'));
    $priceRangeStart = $request->query('price_range_start');
    $priceRangeEnd = $request->query('price_range_end');
    $contractTypeIds = $request->query('contract_type_ids');
    $lat = $request->query('lat');
    $long = $request->query('long');
    $radio = $request->query('radius') == null ? '5':$request->query('radius');
    $typeUser = $request->query('type_user');

    $withFavorite = false;

    if( $request->user() )
      $withFavorite = true;

    if( $post_type_id === 2 )
      $columnsToSelect = ['posts.*'];
    else
      $columnsToSelect = ['posts.*', 'users.picture', 'payment_modalities.abbrev_name as payment_modality'];

    $posts = Post::select($columnsToSelect)
                ->activos()
                ->join('users', 'posts.user_id', 'users.id')
                ->join('model_has_roles as mhr', function ($join) {
                                                  $join->on('users.id', '=', 'mhr.model_id')
                                                      ->where('mhr.model_type', '=', 'App\\Models\\User');
                  })
                ->join('payment_modalities', 'posts.payment_modality_id', 'payment_modalities.id')
                ->where('type_post_id', $post_type_id);

    if($request->filled('date_search')){
      $dateExplode = explode("_",$request->date_search);
      $days = (integer) $dateExplode[1];
      $posts->where('posts.created_at','>=',Carbon::now()->subdays($days));
    }

    if( $request->filled('payment_modality_id') )
      $posts->whereHas('paymentModality', function(Builder $query) use ($payment_modality_id) {
        $query->where('payment_modalities.id', $payment_modality_id);
      });

    if( $request->filled('title') ) {
      $posts->where(function($query) use ($request) {
        $query->where('title', 'LIKE', '%'. $request->title .'%')
              ->orWhere('posts.description', 'LIKE', '%'. $request->title .'%');
      });

      $posts->orderByRaw("CASE
                          WHEN title LIKE ? THEN 1  -- Coincidencia en el título
                          ELSE 2  -- Coincidencia en la descripción
                        END", [ "%{$request->title}%" ]);
    }

    if( $typeUser ) {
      $posts->where('mhr.role_id', $typeUser);
    }

    if( $request->filled('lat') && $request->filled('long') )
      $posts->buscarCercanos( $lat, $long, $radio );

    if( $request->filled('categories_lvl1') || $request->filled('categories_lvl2') || $request->filled('categories_lvl3') )
      $posts->where(function($query) use ($request, $categoriesLvl1, $categoriesLvl2, $categoriesLvl3) {

        if( $request->filled('categories_lvl1') )
          $query->whereIn('category_1_id', $categoriesLvl1 );

        if( $request->filled('categories_lvl2') )
          $query->orWhereIn('category_2_id', $categoriesLvl2 );

        if( $request->filled('categories_lvl3') )
          $query->orWhereIn('category_3_id', $categoriesLvl3 );

      });

    if($post_type_id==3){
      if( $request->filled('price_range_start') )
        $posts->where('amount','>=', $priceRangeStart );

      if( $request->filled('price_range_end') )
        $posts->where('amount_to', '<=' ,$priceRangeEnd );

      if( $request->filled('price_range_end') && $request->filled('price_range_start') )
        $posts->whereBetween('amount' ,[$priceRangeStart,$priceRangeEnd] )->whereBetween('amount_to' ,[$priceRangeStart,$priceRangeEnd] );

    }else{
      if( $request->filled('price_range_start') )
        $posts->where('amount','>=', $priceRangeStart );

      if( $request->filled('price_range_end') )
        $posts->where('amount', '<=' ,$priceRangeEnd );

      if( $request->filled('price_range_end') && $request->filled('price_range_start') )
        $posts->whereBetween('amount' ,[$priceRangeStart,$priceRangeEnd] );
    }

    if( $request->filled('experience_id') ) {

      $experience = Experience::find($request->experience_id);
      $experienceAll = Experience::all();

      $experiencesToFilter = [];

      switch ($typeUser) {
        case '1':
            $condition = $request->experience_id == 1 ? "=" : ">=";
            $posts->where('years_experience', $condition, $experience->minimum);
          break;
        case '2':
          Experience::experienceRangeToFilter($experiencesToFilter, $request->experience_id);

          $posts->whereIn('experience_id', $experiencesToFilter);
          break;
        default:
          Experience::experienceRangeToFilter($experiencesToFilter, $request->experience_id);
          $condition = $request->experience_id == 1 ? "=" : ">=";
          $posts->where('years_experience', $condition, $experience->minimum)
                ->orWhereIn('experience_id', $experiencesToFilter);
          break;
      }
    }

    if( $request->filled('contract_type_ids') )
      $posts->whereHas('contractTypes', function(Builder $query) use ($contractTypeIds) {
        $query->whereIn('contract_types.id', $contractTypeIds);
      });

    if( isset($publisherTypeIds) )
      $posts->whereIn('role_id', $publisherTypeIds );

    if( !empty($request->sort) ){
      // sort= amount-desc / amount-desc / created_at-asc / created_at-desc / relevantes falta por filtrar
      $sort = explode('-', $request->sort);
      $posts->orderBy($sort[0], $sort[1]);
    } else {
      $posts->orderBy('created_at', 'desc')
            ->orderBy('priority', 'desc');
    }

    if( $withFavorite )
      $postResults = $this->isFavorite($posts->get());
    else
      $postResults = $posts->paginate(15);

    return $this->showAll(Collect( $postResults ));
  }

  public function postsByPriority(Request $request, $post_type_id = null)
  {
    if( $post_type_id == 3 && auth()->check() ) {
      $roleUser = $request->user()->getRole()->id;
      $roleInverse =  Role::whereNotIn('id',[ $roleUser, 3 ])->first()->id ;
      $typeUser = (integer) $roleInverse;
    }else{
      $typeUser = (integer) $request->query('type_user');
    }

    $withFavorite = false;

    if( $request->user() )
      $withFavorite = true;

    try {

      $posts = Post::select('posts.*')
                  ->activos()
                  ->join('users', 'posts.user_id', 'users.id')
                  ->join('model_has_roles as mhr', function ($join) {
                      $join->on('users.id', '=', 'mhr.model_id')
                          ->where('mhr.model_type', '=', 'App\\Models\\User');
                  })
                  ->where('type_post_id', $post_type_id)
                  ->orderBy('created_at', 'desc')
                  ->orderBy('priority', 'desc');

      if( $typeUser )
        $posts->where('mhr.role_id', $typeUser);

      if( $withFavorite )
        $postsResults = $this->isFavorite($posts->get());
      else
        $postsResults = $posts->paginate(15);

      return $this->showall(Collect( $postsResults ));
    } catch (\Exception $th) {
      return $this->error($th->getMessage(), 500);
    }
  }

  public function publicPostsUrgentJobs()
  {
    try {
        $posts = Post::activos()
                ->with([
                    'user',
                    'postType',
                    'categoryLevel1',
                    'categoryLevel2',
                    'categoryLevel3',
                    'images',
                    'experience',
                    'paymentModality',
                    'modalities',
                    'idioms',
                    'contractTypes',
                    'condition'
                ])
                ->where('type_post_id', 3)
                ->where('priority', 1)
                ->orderBy('created_at', 'desc');

        return $this->showAll(Collect( $posts->paginate(15) ));
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function postsUrgentJobs()
  {
    try {
        $posts = Post::activos()
                ->where('type_post_id',3)
                ->with([
                    'user',
                    'postType',
                    'categoryLevel1',
                    'categoryLevel2',
                    'categoryLevel3',
                    'images',
                    'experience',
                    'paymentModality',
                    'modalities',
                    'idioms',
                    'contractTypes',
                    'condition'
                ])
                ->where('priority', 1)
                ->orderBy('created_at', 'desc')
                ->get();

        $postsResults = $this->isFavorite($posts);

        return $this->showAll(Collect( $postsResults ));
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function postsNormalJobs()
  {
    try {
        $posts = Post::activos()
                ->with([
                    'user',
                    'postType',
                    'categoryLevel1',
                    'categoryLevel2',
                    'categoryLevel3',
                    'images',
                    'experience',
                    'paymentModality',
                    'modalities',
                    'idioms',
                    'contractTypes',
                    'condition'
                ])
                ->where('type_post_id', 3)
                ->where('priority', 0)
                ->orderBy('created_at', 'desc')->get();

        $postsResults = $this->isFavorite($posts);

        return $this->showAll(Collect( $postsResults ));
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function allJobs()
  {
    try {
        $posts = Post::activos()
                  ->with([
                    'user',
                    'postType',
                    'categoryLevel1',
                    'categoryLevel2',
                    'categoryLevel3',
                    'images',
                    'experience',
                    'paymentModality',
                    'modalities',
                    'idioms',
                    'contractTypes',
                    'condition'
                  ])
                  ->where('type_post_id', 3)
                  ->orderBy('priority','desc')
                  ->orderBy('created_at', 'desc')
                  ->get();

        $postsResults = $this->isFavorite($posts);

        return $this->showAll(Collect( $postsResults ));
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  public function filterByCategoryMarketplace(Request $request)//pendiente con este preguntar a german
  {
    $postsUrg = Post::activos()
                    ->with(['user',

                      'postType',
                      'categoryLevel1',
                      'categoryLevel2',
                      'categoryLevel3',
                      'images',
                      'experience',
                      'paymentModality',
                      'modalities' ,

                      'idioms',
                      'contractTypes',
                      'condition'
                    ])
                    ->where('type_post_id', 1);

    if( $request->filled('category') )
      $postsUrg->where('category_2_id',  $request->category );

    $postsUrg->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

    $postsResults = $this->isFavorite($postsUrg);

    return $this->showAll( $postsResults );
  }

  public function publicfilterByCategoryMarketplace(Request $request)//pendiente con este preguntar a german
  {
    $urgentPosts = Post::activos()
                      ->with([
                        'user',

                        'postType',
                        'categoryLevel1',
                        'categoryLevel2',
                        'categoryLevel3',
                        'images',
                        'experience',
                        'paymentModality',
                        'modalities' ,

                        'idioms',
                        'contractTypes',
                        'condition'
                      ])
                      ->where('type_post_id', 1)
                      ->orderBy('priority', 'desc')
                      ->orderBy('created_at', 'desc');

    if( $request->filled('category_id') )
      $urgentPosts->where('category_2_id',  $request->category_id );

    return $this->showAll(Collect( $urgentPosts->paginate(15) ));
  }

  //favoritos=================================================================================
  public function addFavorites(Request $request, $post_id) {
    try{
      $user = Auth::user();

      $user->favorites()->toggle(Array( $post_id ));

      return $this->success(true);
    }catch (\Exception $th) {
      return $this->error($th->getMessage(), 500);
    }
  }

  public function deleteFavoritesMassive(Request $request) {

    try{

      $user = Auth::user();

      if( !count($request->post_ids) )
        return $this->error('You dont selected a post to remove from your favorites', 422);

      $user->favorites()->detach($request->post_ids);

      return $this->success(true);
    }catch (\Exception $th) {
      return $this->error($th->getMessage(), 500);
    }
  }

  public function getFavoritesByAuth($post_type_id = null) {

    try{

      $favoritePosts = Auth::user()->favorites()
                                  ->activos()
                                  ->with([
                                    'user',
                                    'postType',
                                    'categoryLevel1',
                                    'categoryLevel2',
                                    'categoryLevel3',
                                    'images',
                                    'experience',
                                    'paymentModality',
                                    'modalities',
                                    'idioms',
                                    'contractTypes',
                                    'condition'
                                  ])
                                  ->orderBy('created_at', 'desc');

      if( !is_null($post_type_id) )
        $favoritePosts->where('type_post_id', $post_type_id);

      return $this->showAll(Collect( $favoritePosts->paginate(15) ));
    }catch (\Exception $th) {
      return $this->error($th->getMessage(), 401);
    }
  }

  public function getJobsToPostulationsManagement() {

    try{

      $posts = Auth::user()->jobsPublished()
                          ->with([
                            'user',
                            'postType',
                            'categoryLevel1',
                            'categoryLevel2',
                            'categoryLevel3',
                            'images',
                            'experience',
                            'paymentModality',
                            'modalities',
                            'idioms',
                            'contractTypes',
                            'condition',
                            'postulatedUsers',
                            'postulations' => [
                              'user'
                            ]
                          ])
                          ->orderBy('status', 'ASC')
                          ->orderBy('created_at', 'DESC');

      $jobsPublished = $posts->get();
      // corregido por un nombre mas descriptivo
      $stages = Collect( StagePostulationEnum::toArray() )->map(fn() => []);

      foreach($jobsPublished as $postFind) {

        $postulationStages = $stages->map(function($_, $stage_key) use( $postFind ) {
                                            return $postFind->postulations->filter(fn($postulation) => $postulation->stage_id == $stage_key)
                                                                          ->values()
                                                                          ->all();
                                          });

        $postFind->postulations_segmented = $postulationStages;
      }

      return $this->showAll(Collect( $jobsPublished->paginate(15) ));
    }catch (\Exception $th) {
      return $this->error($th->getMessage(), 500);
    }
  }

  //Este metodo se usa solamente para consultar empleos? si se llega a consultar productos se estaria ejecutando codigo que no tiene que ver con productos
  public function getPostsFromLoggedUser($post_type = null) {

    try{

      $type_posts_ids = [
        'services' => 1,
        'products' => 2,
        'jobs' => 3
      ];

      $posts = Auth::user()->posts()
                          ->with([
                            'user',
                            'postType',
                            'categoryLevel1',
                            'categoryLevel2',
                            'categoryLevel3',
                            'images',
                            'experience',
                            'paymentModality',
                            'modalities' ,
                            'idioms',
                            'contractTypes',
                            'condition',
                            'postulatedUsers',
                            'postulations' => [
                              'user'
                            ]
                          ])
                          ->orderBy('created_at', 'desc');

      if( !is_null($post_type) && array_key_exists($post_type, $type_posts_ids) ) {
        $posts->where('type_post_id', $type_posts_ids[$post_type]);
      }

      $postToPostulations = $posts->get();
      // corregido por un nombre mas descriptivo
      $stages = Collect( StagePostulationEnum::toArray() )->map(fn() => []);

      foreach($postToPostulations as $postFind) {

          $postulationStages = $stages->map(function($_, $stage_key) use( $postFind ) {
                                              return $postFind->postulations->filter(fn($postulation) => $postulation->stage_id == $stage_key)
                                                                            ->values()
                                                                            ->all();
                                          });

        $postFind->postulations_segmented = $postulationStages;
      }

      return $this->showAll(Collect( $postToPostulations->paginate(15) ));
    }catch (\Exception $th) {
      return $this->error($th->getMessage(), 400);
    }
  }

  public function deletePostsMassive(Request $request) {

    $request->validate([
      'post_ids' => [
        'required',
        'array',
        Rule::exists('posts', 'id')
      ],
      'post_ids.*' => 'numeric'
    ]);

    DB::beginTransaction();
    try{
      $fav = FavoritePostUser::with([ 'user', 'post' ])
                            ->whereIn('post_id', $request->post_ids)
                            ->get();

      foreach ($fav as $value) {
        $value->user->notify(new DeletePost($value->post->title));
        $value->delete();
      }

      Auth::user()->posts()->whereIn( 'id', $request->post_ids )->delete();

      DB::commit();
      return $this->success(true);
    }catch (\Exception $th) {
      DB::rollBack();
      return $this->error($th->getMessage(), 500);
    }
  }

  // public function sendNotification($users,$post) {

  //       $notification = [
  //           'title' => 'This post has been deleted',
  //           'content' => $post
  //       ];

  //       Notification::send($users, new NotificationUsers($notification));
  //       return $this->success(true);
  // }

  public function uploadImagePost(UploadImage $request, Post $post)
  {
    try {
      // la primera imagen se guarda como portada
      //se guarda 2 veces, una en la tabla de post (picture), la otra en la tabla images

      //tomar en cuenta si lo que viene en la primera posicion del array es distinto a lo que esta en la en la bd o s3
      $arrImgNew = $request->validated()['pictures'];
      $files = [];

      foreach ($arrImgNew as $val) {
        $obj = (object) array('file' => $val['file'], 'cover' => isset( $val['cover'] ));
        array_push($files, $obj);
      }
      if( $request->filled('pictures') ){
        $post->update([ 'picture' => $this->storeThumbnail( $post, $request->pictures[0]['file'] ) ]);
        $storedImages = $this->storeAdImages( $post, $files, 960, false );
        $post->images()->createMany( $storedImages );
      }

      return $this->success(true);
    } catch (\Exception $th) {
      return $this->error($th->getMessage(), 500);
    }
  }

  public function deleteImage(ModelsImage $image) {
    try {
      $image->delete();

      return $this->success(true);
    } catch (\Exception $th) {
      return $this->error($th->getMessage(), 500);
    }
  }
  // Consultar a Ely si este metodo esta implementado en alguna parte
  public function getNearbyPosts(AroundRequest $request) {

    $request->validated();
    $lat = $request->lat;
    $long = $request->long;

    try {
        $anuncios = Post::select()->get();
        // Calculamos la distancia de cada anuncio a la ubicación del usuario(request)
        $anunciosConDistancia = [];

        foreach ($anuncios as $anuncio) {
            $distancia = $this->calcularDistancia($lat, $anuncio->lat, $long, $anuncio->long);
            $anunciosConDistancia[] = ['anuncio' => $anuncio, 'distancia' => $distancia];
        }

        // Ordenar los anuncios por su distancia a la ubicación del usuario
        usort($anunciosConDistancia, function ($a, $b) {
            return $a['distancia'] <=> $b['distancia'];
        });

        if( $request->filled('range') ){
            $anunciosConDistancia = array_filter($anunciosConDistancia, function ($anuncio) use ($request) {
                $metros = $request->range * 1000;
                return $anuncio['distancia'] <= $metros;
            });
        }
        // Obtener solo los anuncios sin la información de distancia
        $anunciosOrdenados = array_column($anunciosConDistancia, 'anuncio');

        // Información de paginación
        $perPage = 20;
        $currentPage = $request->input('page', 1);
        $totalItems = count($anunciosOrdenados);
        $path = $request->url();
        $options = [
            'path' => $path,
            'query' => $request->query()
        ];

        // Crear una instancia de LengthAwarePaginator
        $paginatedAnuncios = new LengthAwarePaginator(
            $anunciosOrdenados,
            $totalItems,
            $perPage,
            $currentPage,
            $options
        );

        // Obtener la colección de anuncios paginados
        $collection = $paginatedAnuncios->forPage($currentPage, $perPage)->values();

        $paginationData = [
            'data' => $collection,
            'total' => $totalItems,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => $paginatedAnuncios->lastPage(),
            'next_page_url' => $paginatedAnuncios->nextPageUrl(),
            'prev_page_url' => $paginatedAnuncios->previousPageUrl(),
            'from' => $paginatedAnuncios->firstItem(),
            'to' => $paginatedAnuncios->lastItem()
        ];

        return $this->showAll(new Collection($paginationData));
    } catch (\Throwable $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

  function calcularDistancia($latitud1, $latitud2, $longitud1, $longitud2) {
    $R = 6371e3; // radio de la Tierra en metros
    $p1 = deg2rad($latitud1);//convertimos en radianes
    $p2 = deg2rad($latitud2);//convertimos en radianes

    $deltaP = deg2rad($latitud2 - $latitud1);
    $deltaLambda = deg2rad($longitud2 - $longitud1);

    $a = sin($deltaP/2) * sin($deltaP/2) +
        cos($p1) * cos($p2) *
        sin($deltaLambda/2) * sin($deltaLambda/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $d = $R * $c;

    return $d;
  }

  public function filterPostsByCarousel(Request $request, $post_type_id)
  {
    $withFavorite = false;

    if($request->user())
      $withFavorite = true;

    $posts = Post::activos()
                  ->select('posts.*')
                  ->join('users', 'posts.user_id', 'users.id')
                  ->join('post_types', 'posts.type_post_id', 'post_types.id')
                  // ->join('category_level_1 as ct1', 'posts.category_1_id', 'ct1.id')
                  ->join('category_level_2 as ct2', 'posts.category_2_id', 'ct2.id')
                  ->leftJoin('category_level_3 as ct3', 'posts.category_3_id', 'ct3.id')
                  ->where('type_post_id', $post_type_id)
                  ->orderBy('posts.created_at', 'desc')// por parametro
                  ->orderBy('posts.priority', 'desc');

    switch ($post_type_id) {
      case '1':
        $this->getServiceQuery($posts);
        break;
      case '3':
        $this->getJobQuery($posts);
        break;
      default:
        break;
    }

    if( $request->filled('category_id') )
      $posts->where('category_2_id', $request->category_id );


    if( $withFavorite )
      $postResults = $this->isFavorite($posts->get(), $withFavorite);
    else
      $postResults = $posts->get()->paginate(15);

    return $this->showAll(Collect( $postResults ));
  }

  function getServiceQuery(&$query) {
    $query->join('experiences', 'posts.experience_id', 'experiences.id')
          ->join('modalities', 'posts.modality_id', 'modalities.id')
          ->with([
            // 'requirements:id,name',
            'contractTypes:id,name'
          ]);
  }

  function getJobQuery(&$query) {
    $query
          ->join('experiences', 'posts.experience_id', 'experiences.id')
          ->join('modalities', 'posts.modality_id', 'modalities.id')
          ->with([
            // 'requirements:id,name',
            'contractTypes:id,name'
          ]);
  }

  public function getPostsFromIdUser(Request $request,User $user,$post_type = null) {

    $withFavorite = $request->user() ? true : false;

    try{

        $type_posts_ids = [
            'services' => 1,
            'products' => 2,
            'jobs' => 3
        ];

        $posts = $user->posts()
                    ->with([
                    'user',
                    'postType',
                    'categoryLevel1',
                    'categoryLevel2',
                    'categoryLevel3',
                    'images',
                    'experience',
                    'paymentModality',
                    'modalities' ,
                    'idioms',
                    'contractTypes',
                    'condition'
                    ])
                    // ->orderBy('priority', 'desc')
                    ->orderBy('created_at', 'desc');

    if( !is_null($post_type) && array_key_exists($post_type, $type_posts_ids) )
        $posts->where('type_post_id', $type_posts_ids[$post_type]);

    if( $withFavorite )
        $postResults = $this->isFavorite($posts->get(), $withFavorite);
    else
        $postResults = $posts->get()->paginate(15);

    return $this->showAll(Collect( $postResults ));

    }catch (\Exception $th) {
        return $this->error($th->getMessage(), 500);
    }
  }

}
