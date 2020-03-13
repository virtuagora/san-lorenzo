<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendingUser extends Model
{
    protected $table = 'pending_users';
    protected $visible = [
        'id', 'provider', 'identifier', 'token', 'fields',
    ];
    protected $fillable = [
        'provider', 'identifier',
    ];
    protected $casts = [
        'fields' => 'array'
    ];
}
