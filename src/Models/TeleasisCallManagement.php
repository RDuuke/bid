<?php

namespace Bid\Models;


class TeleasisCallManagement extends Model
{
    protected $table = 'teleasis_gestion_llamadas';

    protected $fillable = [
        "periodo", "num_llamadas_gestionadas", "llamada_saludable", "linea", "telepsicologia"
    ];

    public $timestamps = false;
}