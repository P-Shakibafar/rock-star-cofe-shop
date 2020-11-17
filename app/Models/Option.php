<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use function array_diff;

class Option extends Model {

    use HasFactory;

    protected $guarded = [];
    protected $with    = 'values';

    public function saveValues( array $optionValues )
    {
        $addedValues   = array_diff( $optionValues, $this->getFlattenValues() );
        $deletedValues = array_diff( $this->getFlattenValues(), $optionValues );
        $this->addValues( $addedValues );
        $this->removeValues( $deletedValues );
    }

    protected function getFlattenValues() : array
    {
        return Arr::flatten( $this->values()->get( 'value' )->toArray() );
    }

    public function values()
    {
        return $this->hasMany( OptionValue::class );
    }

    public function addValues( array $optionValues )
    {
        foreach( $optionValues as $value ) {
            $this->values()->create( ['value' => $value] );
        }
    }

    public function removeValues( array $optionValues )
    {
        foreach( $optionValues as $value ) {
            $this->values()->where( 'value', $value )->delete();
        }
    }
}
