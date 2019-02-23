<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Checklist extends Model
{
    use SoftDeletes;
    use TimestampMutatorAccessorTrait;

    protected $guarded = [];

    protected $attributes = [
        'is_completed' => false,
    ];

    public function items()
    {
        return $this->hasMany('App\Item');
    }

    public function setDueAttribute($value)
    {
        $this->attributes['due'] = Carbon::parse($value);
    }

    public function getDueAttribute()
    {
        return $this->attributes['due'] ? Carbon::parse($this->attributes['due'])->toIso8601String() : null;
    }
}
