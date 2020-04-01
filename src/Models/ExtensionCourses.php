<?php

namespace Bid\Models;


class ExtensionCourses extends Model
{
    protected $table = "cursos_extension";

    protected $fillable = [
      "codigo", "nombre", "fecha"
    ];

    public $timestamps = false;
}