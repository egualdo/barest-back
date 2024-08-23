<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected function getDefaultGuardName(): string { return 'api'; }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'cif_nif',        
        'email',
        'picture',
        'description',
        'online',
        'phone_visible',
        'password',
        'resetPassword',
        'phone_number',
        'country_code',
        'verified',
        'profession',
        'province',
        'city',
        'zip_code',
        'address',
        'category',        
        'lat',
        'long',        
        'find_job',        
        'hide_address',
        'phone_number_whatsapp',
        'country_code_whatsapp',
        'phone_visible_whatsapp'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
   
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Interact with the user's password.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function password(): Attribute
    {
        return Attribute::make(set: fn( $value ) => bcrypt($value));
    }

    /**
     * Interact with the user's description.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
            set: fn ($value) => $value !== "" ? strtolower($value) : null,
        );
    }

    // public function nifValido($nif) {
	//     $nif = strtoupper($nif);
	//     $nifRegEx = '/^[0-9]{8}[A-Z]$/';
	//     $letras = "TRWAGMYFPDXBNJZSQVHLCKE";

	//     if (preg_match($nifRegEx, $nif)) return ($letras[(substr($nif, 0, 8) % 23)] == $nif[8]);
	//     else return false;
    // }
    
    public function posts() {
        return $this->hasMany(Post::class,'user_id', 'id');
    }

    public function jobsPublished(){
        return $this->posts()->where('type_post_id', 3);
    }

    public function invitedChats()
    {
        return $this->belongsTo(ChatRoom::class,'receiver_id');
    }

    public function adversitements() {
        return $this->hasMany(Adversitements::class);
    }
    
    public function createdChats()
    {
        return $this->hasMany(ChatRoom::class,'id','creator_id');
    }

    //=========================================================
    // user comments

    public function userCommented()
    {
        return $this->belongsTo(UserComment::class,'user_to_comment_id');
    }

    public function creatorComment()
    {
        return $this->hasMany(UserComment::class,'id','user_id');
    }

    //=========================================================
    public function postulations() {
        return $this->hasMany(Postulation::class, 'user_id');
    }

    public function postPostulated() {
        return $this->belongsToMany(Post::class, 'postulations')
                    ->using(Postulation::class)
                    ->as('postulation');
    }

    public function currentPostPostulation($post_id){
        return $this->postPostulated()->where('posts.id', $post_id)->first();
    }

    public function plans() {
    	return $this->belongsToMany(Plan::class, 'plan_user')
                    ->using(PlanUser::class)
                    ->as('suscription');
    }

    public function active_plan() {
        return $this->plans()
                    ->withPivot([ 'id', 'active', 'status', 'benefits_used', 'date_end_at' ])
                    ->wherePivot( 'active', 1 );
    }

    // public function cancelledPostulations() {
    //     return $this->hasMany(CancelledPostulation::class, 'user_id');
    // }

    public function favorites() {
        return $this->belongsToMany(Post::class, 'favorite_post_users');
    }

    public function reviews_given()
    {
        return $this->hasMany(Review::class, 'reviewer_user_id');
    }

    public function reviews_received()
    {
        return $this->hasMany(Review::class, 'ranked_user_id');
    }   

    public function getPictureAttribute($value) {
        if( is_null( $value ) || $value == "" )
            return NULL;

        return str_contains($value,'http') ? $value : Storage::disk('s3')->url( $value );
    }

    // public function getCvAttribute($value) {
    //     if( is_null( $value ) || $value == "" )
    //         return NULL;

    //     return str_contains($value,'http') ? $value : Storage::disk('s3')->url( $value );
    // }

    public function has_active_plan() {
        return $this->active_plan()->exists();
    }

    public function scopeActivos($query) {
        return $query->where('status', 'ACTIVE');
    }

    public function calculateRanking(){         
        $stringvar= $this->reviews_received()->avg('ranking');
        $floatvar =  floatval($stringvar);
        return $floatvar;
    }

    public function payments_made() {
        return $this->hasMany(PaymentHistory::class);
    }

    public function getRole() {
        return $this->roles()->first();
    }

    public function petition() {
        return $this->hasOne(Petition::class);
    }

    public function reports_related() {
        return $this->hasMany(Report::class, 'reported_user_id');
    }

    public function reports_received() {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function reports_made() {
        return $this->hasMany(Report::class, 'creator_user_id');
    }

    public function social_profiles() {
        return $this->hasMany(SocialProfile::class);
    }

    public function resume()
    {
        return $this->morphOne(Resume::class, 'resumeable');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
