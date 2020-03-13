<?php

namespace App\Model;

use App\Util\DummySubject;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    public $timestamps = false;
    protected $table = 'subjects';
    protected $visible = [
        'id', 'display_name', 'img_type', 'img_hash', 'points'
    ];

    public function actor()
    {
        $entity = 'App\\Model\\'.$this->type;
        return $this->hasOne($entity);
    }

    // public function user(){
    //     return $this->hasOne('App\Model\User');
    // }

    public function citizen()
    {
        return $this->belongsTo('App\Model\Citizen');
    }

    public function neighbourhood()
    {
        return $this->belongsTo('App\Model\Neighbourhood');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Model\Role', 'subject_role');
    }

    public function setDisplayNameAttribute($value)
    {
        $this->attributes['display_name'] = $value;
        $this->attributes['trace'] = preg_replace('/[^[:alnum:]]/ui', '', $value);
    }

    public function toDummy($extras = null)
    {
        $attr = [
            'img_type' => $this->img_type,
            'img_hash' => $this->img_hash,
            'points' => $this->points,
            // 'citizen' => $this->citizen()
        ];
        if (isset($extras)) {
            $attr = array_merge($attr, $extras);
        }
        return new DummySubject(
            $this->type,
            $this->id,
            $this->display_name,
            $this->roles()->pluck('role_id')->all(),
            $attr
        );
    }
}
