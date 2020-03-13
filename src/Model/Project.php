<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    // public $incrementing = false;
    // public $keyType = 'string';
    protected $table = 'projects';
    protected $visible = [
        'id',
        'code',
        'name',
        'type',
        'slug',
        'objective',
        'description',
        'total_budget',
        'benefited_population',
        'community_contributions',
        'budget',
        'author_names',
        'author_surnames',
        'author_email',
        'author_dni',
        'author_phone',
        'organization_legal_entity',
        'organization_name',
        'organization_address',
        'picture_name',
        'youtube_id',
        'feasibility',
        'feasible',
        'selected',
        'district_id',
        'district',
        'likes'
    ];
    protected $fillable = [
        'code',
        'name',
        'type',
        'slug',
        'objective',
        'description',
        'total_budget',
        'benefited_population',
        'community_contributions',
        'budget',
        'author_names',
        'author_surnames',
        'author_email',
        'author_dni',
        'author_phone',
        'organization_legal_entity',
        'organization_name',
        'organization_address',
        'district_id',
    ];
    protected $with = [];
    protected $casts = [
        'deleted_at' => 'datetime',
        'budget' => 'array',
        'journal' => 'array',
        'selected' => 'boolean',
        'feasible' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo('App\Model\User', 'author_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Model\Category');
    }

    public function district()
    {
        return $this->belongsTo('App\Model\District');
    }

    public function documents()
    {
        return $this->hasMany('App\Model\Document');
    }

    public function getRelationsWith($subject)
    {
        $relations = [];
        $user = $this->author;
        if (isset($user) && $user->subject_id == $subject->getId()) {
            $relations[] = 'author';
        }
        return $relations;
    }
}
