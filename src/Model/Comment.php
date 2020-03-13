<?php

// namespace App\Model;

// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

// class Comment extends Model
// {
//     use SoftDeletes;

//     protected $table = 'comments';
//     protected $visible = [
//         'id', 'content', 'votes', 'created_at', 'deleted_at', 'author', 'replies'
//     ];
//     protected $with = [
//         'author', 'replies'
//     ];
    
//     public function author()
//     {
//         return $this->belongsTo('App\Model\User', 'author_id');
//     }

//     public function project()
//     {
//         return $this->belongsTo('App\Model\Project', 'project_id');
//     }

//     public function parent()
//     {
//         return $this->belongsTo('App\Model\Comment', 'parent_id');
//     }

//     public function replies()
//     {
//         return $this->hasMany('App\Model\Comment', 'parent_id');
//     }

//     public function raters()
//     {
//         return $this->belongsToMany(
//             'App\Model\User', 'comment_votes', 'comment_id', 'user_id'
//         )->withPivot('value')->withTimestamps();
//     }
// }