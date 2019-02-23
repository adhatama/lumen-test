<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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

    public function getDueInDate()
    {
        $due = null;

        switch ($this->due_unit) {
        case 'minute':
            $due = Carbon::now();
            $due->addMinutes($this->due_interval); break;
        case 'hour':
            $due = Carbon::now();
            $due->addHours($this->due_interval); break;
        case 'day':
            $due = Carbon::now();
            $due->addDays($this->due_interval); break;
        case 'week':
            $due = Carbon::now();
            $due->addWeek($this->due_interval); break;
        case 'month':
            $due = Carbon::now();
            $due->addMonths($this->due_interval); break;
        }

        return $due;
    }
}
