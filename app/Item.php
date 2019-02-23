<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Item extends Model
{
    use SoftDeletes;
    use TimestampMutatorAccessorTrait;

    protected $guarded = [];

    protected $attributes = [
        'is_completed' => false,
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function checklist()
    {
        return $this->belongsTo('App\Checklist');
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
