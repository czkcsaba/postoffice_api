<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $table = 'counties';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'population',
        'badge'
    ];

    public function cities()
    {
        return $this->hasMany(City::class, 'id_county');
    }
}