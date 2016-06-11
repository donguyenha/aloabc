<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['link', 'episode_id', 'profile'];
    public function episodes()
    {
        return $this->belongsTo('App\Episode');
    }
}
