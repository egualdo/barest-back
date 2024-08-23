<?php

namespace App\Models;

use App\Traits\ImagesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory, ImagesTrait;

    protected $fillable = ['path','cover'];

    protected static function boot() {

    	parent::boot();

        parent::creating(function( $image ) {
            if( $image->cover )
                $image->imageable->images()->update([ 'cover' => false ]);
    	});

        parent::updating(function( $image ) {
            if( $image->cover )
                $image->imageable->images()->update([ 'cover' => false ]);
    	});

    	parent::deleting(function( $image ) {
    		$image->destroyStoredImage( $image->path );
    	});
    }

    public function imageable() {
    	return $this->morphTo();
    }

    public function getPathAttribute($value) {
        return !str_contains($value,'http')
                    ? Storage::disk('s3')->url( $value )
                    : $value;
    }
}
