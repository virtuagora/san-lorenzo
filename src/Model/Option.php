<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Option extends Model
{
    protected $table = 'options';
    protected $visible = ['id', 'key', 'value', 'type', 'group', 'autoload'];
    protected $casts = [
        'autoload' => 'boolean',
    ];

    public function getValueAttribute($value)
    {
        // return $this->castAttribute($this->type, $value);
        switch ($this->type) {
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'string':
                return (string) $value;
            case 'boolean':
                if($value === 'true' || $value === '1') {
                    return true;
                } elseif($value === 'false' || $value === '0') {
                    return false;
                }
                return (bool) $value;
            case 'object':
            case 'array':
                return json_decode($value, true);
            case 'date':
            case 'datetime':
                return Carbon::parse($value);
            default:
                return $value;
        }
    }
}
