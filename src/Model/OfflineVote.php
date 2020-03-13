<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OfflineVote extends Model
{
    public $timestamps = false;
    protected $table = 'offline_votes';
    protected $visible = [
        'id' ,'ballot', 'project'
    ];
    // protected $with = ['project'];
    public function project()
    {
        return $this->belongsTo('App\Model\Project', 'project_id');
    }
    
    public function ballot()
    {
        return $this->belongsTo('App\Model\OfflineBallot', 'ballot_id');
    }

}
