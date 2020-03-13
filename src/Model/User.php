<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $table = 'users';
    protected $dates = ['deleted_at', 'token_expiration'];
    protected $visible = [
        'id', 'names', 'surnames', 'ban_exp', 'created_at', 'subject',
        'bio', 'pivot',
    ];
    protected $with = ['subject'];
    protected $appends = ['pending_tasks'];

    public function subject()
    {
        return $this->belongsTo('App\Model\Subject');
    }

    // public function districts()
    // {
    //     return $this->belongsTo('App\Model\Disctrict');
    // }

    // public function groups()
    // {
    //     return $this->belongsToMany('App\Model\Group', 'user_group')->withPivot('relation', 'title');
    // }

    public function setNamesAttribute($value)
    {
        $this->attributes['names'] = $value;
        if (isset($this->attributes['surnames'])) {
            $fullname = $this->attributes['names'] . ' ' . $this->attributes['surnames'];
            $this->attributes['trace'] = mb_strtolower(trim($fullname));
        } else {
            $this->attributes['trace'] = mb_strtolower(trim($value));
        }
    }

    public function setSurnamesAttribute($value)
    {
        $this->attributes['surnames'] = $value;
        if (isset($this->attributes['names'])) {
            $fullname = $this->attributes['names'] . ' ' . $this->attributes['surnames'];
            $this->attributes['trace'] = mb_strtolower(trim($fullname));
        } else {
            $this->attributes['trace'] = mb_strtolower(trim($value));
        }
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }

    public function getRelationsWith($subject)
    {
        return ($this->subject_id == $subject->getId())? ['self']: [];
    }

    /*public function meta()
    {
        return $this->hasMany('App\Model\UserMeta');
    }*/
}
