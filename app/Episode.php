<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    public function links()
    {
        return $this->hasMany('App\Link');
    }

    public function films()
    {
        return $this->belongsTo('App\Film');
    }
}
