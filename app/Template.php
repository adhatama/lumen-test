<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;
    use TimestampMutatorAccessorTrait;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany('App\TemplateItem');
    }

    public function delete()
    {
        TemplateItem::where('template_id', $this->id)->delete();

        return parent::delete();
    }
}
