<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderItem extends Model {

    use HasFactory;

    protected $casts = [
        'options' => Json::class,
    ];

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo( Product::class );
    }
}
