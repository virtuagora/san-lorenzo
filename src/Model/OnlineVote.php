<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OnlineVote extends Model
{
    public $timestamps = false;
    protected $table = 'online_votes';
    protected $visible = [
        'id', 'project_id', 'hash',
    ];
}
