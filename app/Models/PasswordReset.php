<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $primaryKey = 'email';

    public $timestamps = [ "created_at" ];
	public $incrementing = false;

    const UPDATED_AT = null;

    protected $fillable = [
	    'email',
	    'token',
	    'created_at',
	];
}
