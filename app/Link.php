<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    public function episodes()
    {
        return $this->belongsTo('App\Episode');
    }
}
