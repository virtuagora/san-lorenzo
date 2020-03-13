<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StatisticalBallot extends Model
{
    protected $table = 'statistical_ballots';
    protected $visible = [
        'id', 'type', 'gender', 'age', 'created_at',
    ];
    protected $fillable = [
        'type', 'gender', 'age',
    ];
}
