<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    public $incrementing = false;
    protected $table = 'actions';
    protected $visible = [
        'id', 'group', 'allowed_roles', 'allowed_relations', 'allowed_proxies',
    ];
    protected $casts = [
        'allowed_roles' => 'array',
        'allowed_relations' => 'array',
        'allowed_proxies' => 'array',
    ];
}
