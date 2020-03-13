<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;
    protected $table = 'categories';
    protected $visible = [
        'id', 'name', 'description',
    ];
}
