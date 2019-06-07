<?php

namespace Bid\Models;


class TeleasisAlgorithms extends Model
{
    protected $table = "teleasis_algoritmos";

    protected $fillable = [
        "algoritmo", "fecha_creacion"
    ];

    public $timestamps = false;
}