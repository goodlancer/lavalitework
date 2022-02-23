<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FrontSection extends Model
{
    //
    protected $fillable = ['name', 'heading', 'body', 'image', 'icon'];
}
