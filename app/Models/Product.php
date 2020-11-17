<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model {

    use HasFactory;

    protected $guarded = [];
    protected $with='options';

    public function addOption( Option $option )
    {
        return $this->options()->attach( $option );
    }

    public function options()
    {
        return $this->belongsToMany( Option::class )->withTimestamps();
    }

    public function removeOption( Option $option )
    {
        return $this->options()->detach( $option );
    }
}
