<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemplateItem extends Model
{
    use SoftDeletes;
    use TimestampMutatorAccessorTrait;

    protected $guarded = [];

    public function template()
    {
        return $this->belongsTo('App\TemplateItem');
    }
}
