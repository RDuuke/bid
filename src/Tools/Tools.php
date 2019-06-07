<?php

namespace Bid\Tools;


use Bid\Models\ExtensionCourses;
use Bid\Models\TeleasisAlgorithms;
use Bid\Models\TeleasisCallManagement;
use Bid\Models\TeleasisPatients;
use Bid\Models\TeleduInterracion;
use Illuminate\Database\Capsule\Manager;

class Tools
{
    const Teleducacion = 'teledu';
    const Telemedicina = 'telemed';
    const Teleasistencia = 'teleasis';
    const Administrador = 'admin';

    const read = "1";
    const write = "1";

    const meta_teleducacion_indicador_1 = 1000;
    const meta_teleducacion_indicador_2 = 15;
    const meta_teleducacion_indicador_3 = 30000;

    const meta_teleasistencia_indicador_1 = 8;
    const meta_teleasistencia_indicador_2 = 1500;
    const meta_teleasistencia_indicador_3 = 50000;

    static public $type = [
            'teledu' => 'Teleducación',
            'telemed' => 'Telemedicina',
            'teleasis' => 'Teleasistencia',
            'admin' => 'Administrador'
    ];

    static function estudiantes()
    {
        $fecha = "2018-04-30 00:00:00";
        $estudiantes = \Illuminate\Database\Capsule\Manager::connection("db_abiertas_moodle")
            ->table("mdl_course")
            ->select(\Illuminate\Database\Capsule\Manager::raw("count(*) as total"))
            ->join("mdl_context", "mdl_context.instanceid", "=", "mdl_course.id")
            ->join("mdl_role_assignments", "mdl_role_assignments.contextid", "=", "mdl_context.id")
            ->join("mdl_user", "mdl_user.id", "=", "mdl_role_assignments.userid")
            ->join("mdl_grade_grades", "mdl_grade_grades.userid", "=", "mdl_user.id")
            ->join("mdl_grade_items", "mdl_grade_items.id", "=", "mdl_grade_grades.itemid")
            ->join("mdl_course_categories", "mdl_course_categories.id", "=", "mdl_course.category")
            ->where("mdl_grade_items.courseid", "=", \Illuminate\Database\Capsule\Manager::raw("mdl_course.id"))
            ->where("mdl_grade_items.itemtype", "=",'course')
            ->where("mdl_role_assignments.roleid", 5)
            ->where("mdl_grade_grades.finalgrade", ">=", '3.0')
            ->where("mdl_grade_items.timemodified", ">=", $fecha)
            ->orderByRaw("mdl_course.id DESC, mdl_user.lastname, mdl_user.firstname, mdl_grade_items.itemname")->first();

        return $estudiantes->total;
    }

    static function profesionales()
    {
        $profesional = \Illuminate\Database\Capsule\Manager::connection("db_extension")
            ->table("certificado")
            ->select(\Illuminate\Database\Capsule\Manager::raw("count(*) as total"))
            ->leftJoin("oferta", "oferta.ofertaId", "=", "certificado.ofertaId")
            ->leftJoin("curso", "curso.cursoId", "=", "oferta.cursoId")
            ->whereIn("curso.codigoCurso", ExtensionCourses::all("codigo")->toArray())->first();

        return $profesional->total;
    }


    static function cursos()
    {
        $fecha = "2018-04-30 00:000:00";
        $valor1 = Manager::connection("db_abiertas_gestion")->table("curso")
            ->where("fecha", ">", $fecha)
            ->where("estado", 1)
            ->select(Manager::raw("count(*) as total"))
            ->first();
        $valor2 = ExtensionCourses::all()->count();

        return $valor1->total + $valor2;
    }
    static function teleducacion_indicador_1()
    {
        $total = self::estudiantes() + self::profesionales();

        return [
            "total" => $total,
            "porcentaje" => round(($total * 100)/ self::meta_teleducacion_indicador_1),
            "meta" => self::meta_teleducacion_indicador_1
        ];
    }

    static function teleducacion_indicador_2()
    {
        $total = self::cursos();
        return [
            "total" => $total,
            "meta" => self::meta_teleducacion_indicador_2,
            "porcentaje" => round(($total * 100)/ self::meta_teleducacion_indicador_2)
        ];
    }

    static function teleducacion_indicador_3()
    {
        $valores = TeleduInterracion::all()->last();
        $total = $valores->youtube+$valores->saludando+$valores->perlas_clinicas+$valores->atulado+$valores->fb_live;
        return [
            "total" => $total,
            "meta" => self::meta_teleducacion_indicador_3,
            "porcentaje" => round(($total * 100)/self::meta_teleducacion_indicador_3)
        ];
    }

    static function teleducacion_indicador_more_1()
    {
        $fecha = "2018-04-30 00:00:00";
        $extension = Manager::connection("db_extension")
            ->table("certificado")
            ->leftJoin("oferta", "oferta.ofertaId", "=", "certificado.ofertaId")
            ->leftJoin("curso", "curso.cursoId", "=", "oferta.cursoId")
            ->select(Manager::raw("DATE_FORMAT(oferta.fechaCertificacion, \"%Y-%m\") AS \"ano_mes\", count(oferta.fechaCertificacion) AS Total"))
            ->whereIn("curso.codigoCurso", ExtensionCourses::all("codigo")->toArray())
            ->where("oferta.fechaCertificacion", ">=", $fecha)
            ->groupBy("oferta.fechaCertificacion")
            ->orderBy("oferta.fechaCertificacion")
            ->get()->toArray();

        $mooc = Manager::connection("db_abiertas_moodle")
            ->table("mdl_course")
            ->join("mdl_context", "mdl_context.instanceid", "=", "mdl_course.id")
            ->join("mdl_role_assignments", "mdl_role_assignments.contextid", "=", "mdl_context.id")
            ->join("mdl_user", "mdl_user.id", "=", "mdl_role_assignments.userid")
            ->join("mdl_grade_grades", "mdl_grade_grades.userid", "=", "mdl_user.id")
            ->join("mdl_grade_items", "mdl_grade_items.id", "=", "mdl_grade_grades.itemid")
            ->join("mdl_course_categories", "mdl_course_categories.id", "=", "mdl_course.category")
            ->select(Manager::raw("CONCAT(YEAR(FROM_UNIXTIME(mdl_grade_items.timemodified)), '-',DATE_FORMAT(FROM_UNIXTIME(mdl_grade_items.timemodified), \"%m\")) AS \"ano_mes\", count(mdl_grade_items.timemodified) AS Total"))
            ->where("mdl_grade_items.courseid", "=", \Illuminate\Database\Capsule\Manager::raw("mdl_course.id"))
            ->where("mdl_grade_items.itemtype", "=",'course')
            ->where("mdl_role_assignments.roleid", 5)
            ->where("mdl_grade_grades.finalgrade", ">=", '3.0')
            ->where("mdl_grade_items.timemodified", ">=", $fecha)
            ->groupBy(Manager::raw("MONTH(FROM_UNIXTIME(mdl_grade_items.timemodified)), YEAR(FROM_UNIXTIME(mdl_grade_items.timemodified))"))
            ->get()->toArray();
        $total = array_merge($extension, $mooc);
        $total = sort_by_orden($total);
        return $total;
    }

    static function teleducacion_indicador_more_1_more()
    {
        $fecha = "2018-04-30 00:00:00";
        $extension = Manager::connection("db_extension")
            ->table("certificado")
            ->leftJoin("oferta", "oferta.ofertaId", "=", "certificado.ofertaId")
            ->leftJoin("curso", "curso.cursoId", "=", "oferta.cursoId")
            ->select(Manager::raw("CONCAT(curso.nombreCurso, \" \", curso.nombreAuxiliar) AS \"curso\", COUNT(certificado.ofertaId) AS \"eae\", CONCAT(\"Extensión\") as \"origen\" "))
            ->whereIn("curso.codigoCurso", ExtensionCourses::all("codigo")->toArray())
            ->where("oferta.fechaCertificacion", ">=", $fecha)
            ->groupBy("oferta.fechaCertificacion")
            ->orderBy("oferta.fechaCertificacion")
            ->get()->toArray();

        $mooc = Manager::connection("db_abiertas_moodle")
            ->table("mdl_course")
            ->join("mdl_context", "mdl_context.instanceid", "=", "mdl_course.id")
            ->join("mdl_role_assignments", "mdl_role_assignments.contextid", "=", "mdl_context.id")
            ->join("mdl_user", "mdl_user.id", "=", "mdl_role_assignments.userid")
            ->join("mdl_grade_grades", "mdl_grade_grades.userid", "=", "mdl_user.id")
            ->join("mdl_grade_items", "mdl_grade_items.id", "=", "mdl_grade_grades.itemid")
            ->join("mdl_course_categories", "mdl_course_categories.id", "=", "mdl_course.category")
            ->select(Manager::raw("mdl_course.id AS \"idcurso\", mdl_course.fullname AS \"curso\", COUNT(mdl_course.id) AS \"eae\", CONCAT(\"MOOC - Aulas Abiertas\") as \"origen\" "))
            ->where("mdl_grade_items.courseid", "=", \Illuminate\Database\Capsule\Manager::raw("mdl_course.id"))
            ->where("mdl_grade_items.itemtype", "=",'course')
            ->where("mdl_role_assignments.roleid", 5)
            ->where("mdl_grade_grades.finalgrade", ">=", '3.0')
            ->where("mdl_grade_items.timemodified", ">=", $fecha)
            ->groupBy(Manager::raw("mdl_course.id"))
            ->get()->toArray();
        $total = array_merge($mooc, $extension);
        return $total;
    }

    static public function replaceValues($array)
    {
        $labels = [
            "Youtube", "Saludando", "Perlas clinicas", "Atulado", "Facebook live"
        ];
        $values = [
            "youtube", "saludando", "perlas_clinicas", "atulado", "fb_live"
        ];
        $array = str_replace($values, $labels, $array);

        unset($array[5]);
        return $array;
    }

    static public function teleasistencia_indicador_1()
    {
        $total = TeleasisAlgorithms::all()->count();

        return [
            "total" => $total,
            "porcentaje" => round(($total * 100)/ self::meta_teleasistencia_indicador_1),
            "meta" => self::meta_teleasistencia_indicador_1
        ];
    }

    static public function teleasistencia_indicador_2()
    {
        $total = TeleasisPatients::all("num_pacientes")->sum("num_pacientes");
        return [
            "total" => $total,
            "porcentaje" => round(($total * 100)/ self::meta_teleasistencia_indicador_2),
            "meta" => self::meta_teleasistencia_indicador_2
        ];
    }

    static public function teleasistencia_indicador_3()
    {
        $total = TeleasisCallManagement::all("num_llamadas_gestionadas")->sum("num_llamadas_gestionadas");

        return [
            "total" => $total,
            "porcentaje" => round(($total * 100)/ self::meta_teleasistencia_indicador_3),
            "meta" => self::meta_teleasistencia_indicador_3
        ];
    }
}