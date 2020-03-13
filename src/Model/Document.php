<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';
    protected $visible = [
        'id', 'name', 'filename', 'description', 'project'
    ];
    protected $fillable = [
        'name', 'filename', 'description',
    ];

    public function project()
    {
        return $this->belongsTo('App\Model\Project', 'project_id');
    }
}
