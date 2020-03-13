<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OfflineBallot extends Model
{
    protected $table = 'offline_ballots';
    protected $visible = [
        'id', 'order', 'invalid', 'votes', 'created_at', 'updated_at'
    ];
    protected $casts = [
        'selected' => 'boolean',
    ];
    public function votes()
    {
        return $this->hasMany('App\Model\OfflineVote','ballot_id');
    }

}
