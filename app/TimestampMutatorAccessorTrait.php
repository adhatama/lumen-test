<?php

namespace App;

use Carbon\Carbon;

trait TimestampMutatorAccessorTrait
{
    public function setCreatedAtAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value);
    }

    public function getCreatedAtAttribute()
    {
        return $this->attributes['created_at'] ? Carbon::parse($this->attributes['created_at'])->toIso8601String() : null;
    }

    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['updated_at'] = Carbon::parse($value);
    }

    public function getUpdatedAtAttribute()
    {
        return $this->attributes['updated_at'] ? Carbon::parse($this->attributes['updated_at'])->toIso8601String() : null;
    }
}
