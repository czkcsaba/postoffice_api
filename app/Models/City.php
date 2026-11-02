<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    public $timestamps = false;

    protected $fillable = [
        'zip_code',
        'city',
        'id_county',
        'population'
    ];

    public function county()
    {
        return $this->belongsTo(County::class, 'id_county');
    }
}