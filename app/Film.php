<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    protected $fillable = ['title_viet',
                        'title_english',
                        'description',
                        'category_id',
                        'thumbnail_id',
                        'thumbnail_id1',
                        'thumbnail_id2',
                        'play_time',
                        'year',
                        'country',
                        'type',
                        'crawler_at'];
    public function episodes()
    {
        return $this->hasMany('App\Episode');
    }

    public function category()
    {
        return $this->hasOne('App\Category');
    }

    public function actors()
    {
        return $this->belongsToMany('App\Actor');
    }

    public function directors()
    {
        return $this->belongsToMany('App\Director');
    }
}
