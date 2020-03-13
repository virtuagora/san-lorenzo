<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuditTrail extends Model
{
    protected $table = 'audit_trails';
    protected $visible = [
        'id', 'state', 'description', 'extra',
    ];
    protected $fillable = [
        'state', 'description', 'extra',
    ];
    protected $casts = [
        'extra' => 'array',
    ];
}
