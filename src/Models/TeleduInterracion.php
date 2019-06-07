<?php


namespace Bid\Models;


class TeleduInterracion extends Model
{
    protected $table = "teledu_interacciones";

    protected $fillable = [
      "youtube", "saludando", "perlas_clinicas", "atulado", "fb_live"
    ];

    protected $hidden = ["id"];

    //protected $hidden = [ "created_at"];
    public $timestamps = false;
}