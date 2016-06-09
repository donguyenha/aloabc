<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //  mass assignable attribute
    protected $fillable = ['name'];
    //  use table not same name with CategoryController
    protected $table = 'categories';
}
