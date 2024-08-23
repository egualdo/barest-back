<?php

namespace App\Models;

use App\Traits\ImagesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Resume extends Model
{
    use HasFactory, ImagesTrait;

    protected $fillable = ['path','size','nameFile'];

    protected static function boot() {

    	parent::boot();
        
    	parent::deleting(function( $cv ) {
    		$cv->destroyStoredCv( $cv->path );
    	});
    }

    public function resumeable() {
    	return $this->morphTo();
    }

    public function getPathAttribute($value) {
        return !str_contains($value,'http')
                    ? Storage::disk('s3')->url( $value )
                    : $value;
    }
}

