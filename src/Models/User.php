<?php

namespace Bid\Models;


class User extends Model
{
    protected $table;

    protected $fillable = [
      "username", "password", "name", "last_name", "institution"
    ];

    public $timestamps = false;
    
    function getFullNameAttribute()
    {
        return $this->name . " " . $this->last_name;
    }
}