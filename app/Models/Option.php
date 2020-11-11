<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Option extends Model {

    use HasFactory;

    protected $guarded = [];

    public function addValues( array $optionValues )
    {
        foreach( $optionValues as $value ) {
            $this->values()->create( ['value' => $value] );
        }
    }

    public function values()
    {
        return $this->hasMany( OptionValue::class );
    }
}
