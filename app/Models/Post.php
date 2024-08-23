<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    use HasFactory,SoftDeletes;

     protected $fillable = [
        'title',
        'user_id',//dueno del post
        'picture',
        'description',
        'type_post_id',//tipo de post producto o servicio o jobs
        // 'stage_post_id',//etapa del post
        'payment_modality_id',//tipo de pago quincenal , mensual , por hora , etc...
        'amount',
        'category_1_id',
        'category_2_id',
        'category_3_id',//hoteles,bares,cafeterias, restaurantes etc...
        'experience_id',//0-1,1-3,+5 annos , etc...
        'years_experience',
        'contract_type',// indefinido, part-time, full-time, freelancer etc...
        'status',
        'lat',
        'long',
        'state',
        'city',//del maps
        'address',        
        'requeriments',
        'priority',
        'modality_id',
        'condition_product_id',
        'amount_to',
        'country',
        'benefits',
        'more_info',
        'custom_budget',
        'working_holidays',
        'delivery_price',
        'hour_price'
    ];
    
    protected $attributes = [
        'status' => "ACTIVE",
    ];

    protected static function newFactory(): PostFactory {
        return PostFactory::new();
    }

    public function scopeActivos($query) {
        return $query->whereIn('posts.status', [ 'ACTIVE', 'STOPPED' ]);
    }

    public function scopeBuscarCercanos($query, $latitudReferencia, $longitudReferencia, $radio) {
        return $query->whereRaw('ST_Distance_Sphere(
                            point(posts.long, posts.lat),
                            point(?, ?)
                        ) < ?', [
                            $longitudReferencia,
                            $latitudReferencia,
                            $radio * 1000 // convertir el radio en metros
                        ]);
    }

     public function postType()
    {
       return $this->belongsTo(PostType::class ,'type_post_id');//primero va la llave externa y luego va la llave foranea local
    }

    public function modalities()
    {
       return $this->hasOne(Modality::class ,'id','modality_id');//primero va la llave externa y luego va la llave foranea local
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // public function postStage()
    // {
    //     return $this->belongsTo(PostStage::class,'stage_post_id');
    // }

    //definir primero los niveles para la relacion con post creo que tendremos que colocar los 3 niveles de ids
    public function categoryLevel1()    
    {
        return $this->hasOne(GroupCategory::class ,'id','category_1_id');
    }

    public function categoryLevel2()    
    {
        return $this->hasOne(Category::class ,'id','category_2_id');
    }

    public function categoryLevel3()    
    {
        return $this->hasOne(SubCategory::class ,'id','category_3_id');
    }

    function experience() {
        return $this->belongsTo(Experience::class ,'experience_id');
    }

    function paymentModality() {
        return $this->belongsTo(PaymentModality::class ,'payment_modality_id');
    }

    function contractTypes() {
        return $this->belongsToMany(ContractType::class, 'post_contract_types');
    }

    public function images() {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function resume()
    {
        return $this->morphOne(Resume::class, 'resumeable');
    }

    // public function requirements()
    // {
    //     return $this->hasMany(Requirement::class,'post_id');
    // }

    public function idioms()
    {
        // return $this->hasMany(PostIdiom::class,'post_id');
        return $this->belongsToMany(Idiom::class, 'idiom_posts', 'post_id', 'idiom_id')
                    ->withPivot('level');
    }

    public function questions()
    {
        return $this->hasMany(QuestionPost::class,'post_id');
    }

    public function postulatedUsers()
    {
        return $this->belongsToMany(User::class, 'postulations') // postulations cambiar controlador de postulaciones-User-post
                    ->using(Postulation::class)
                    ->as('postulation');
    }

    public function postulations() {
        return $this->hasMany(Postulation::class,'post_id');
    }

    public function favorite() {
        return $this->belongsTo(FavoritePostUser::class,'post_id');
    }
     
    public function reviews_received()
    {
        return $this->hasMany(Review::class, 'post_id');
    }

    public function reports_received() {
        return $this->morphMany(Report::class, 'reportable');
    }
    
    public function getPictureAttribute($value) {
        if( is_null( $value ) || $value == "" )
            return NULL;

        return str_contains($value,'http') ? $value : Storage::disk('s3')->url( $value );
    }

    public function condition() {
        return $this->hasOne(ConditionProduct::class, 'id', 'condition_product_id');
    }

}
