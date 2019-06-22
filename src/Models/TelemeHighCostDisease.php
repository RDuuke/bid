<?php

namespace Bid\Models;


class TelemeHighCostDisease extends Model
{
    protected $table = "telemed_enf_alto_costo";
    protected $fillable = [
    "idcie10", "codigo", "descripcion"
    ];

    public $timestamps = false;

}