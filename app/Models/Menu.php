<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'parent_id',
        'title',
        'icon',
        'uri',
        'permissions',
        'priority',
        'link',
        'show'
    ];
}
