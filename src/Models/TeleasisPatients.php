<?php

namespace Bid\Models;


class TeleasisPatients extends Model
{
    protected $table = "teleasis_pacientes";

    protected $fillable = [
        "patalogia", "num_pacientes"
    ];

    public $timestamps = false;
}