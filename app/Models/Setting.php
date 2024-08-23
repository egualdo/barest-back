<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Get the fields value.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fieldsValue(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode( $value, true ),
        );
    }
}
