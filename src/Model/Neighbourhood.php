<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Neighbourhood extends Model
{
    public $timestamps = false;
    protected $table = 'neighbourhoods';
    protected $visible = [
        'id', 'name', 'district_id', 'district',
    ];

    public function district()
    {
        return $this->belongsTo('App\Model\District');
    }
}
