<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = ['film_id', 'episode_no'];
    public function links()
    {
        return $this->hasMany('App\Link');
    }

    public function films()
    {
        return $this->belongsTo('App\Film');
    }
}
