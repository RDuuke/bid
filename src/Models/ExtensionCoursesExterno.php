<?php
namespace Bid\Models;

class ExtensionCoursesExterno extends Model
{
    protected $table = "cursos";
    protected $connection = "db_extension";
    protected $fillable = [
        "cursoId"
    ];

    public $timestamps = false;
}