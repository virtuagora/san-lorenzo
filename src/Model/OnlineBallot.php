<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OnlineBallot extends Model
{
    public $timestamps = false;
    protected $table = 'online_ballots';
    protected $visible = [
        'id', 'code', 'created_at', 'sent',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
