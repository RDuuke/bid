<?php

namespace Bid\Models;


class TeleasisCallPathology extends Model
{
    protected $table = 'teleasis_llamadas_patologia';

    protected $fillable = [
        "patalogia", "num_llamadas_saludable"
    ];

    public $timestamps = false;
}