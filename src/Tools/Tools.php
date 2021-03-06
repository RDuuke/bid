<?php

namespace Bid\Tools;


use Bid\Models\ExtensionCourses;
use Bid\Models\ExtensionCoursesExterno;
use Bid\Models\TeleasisAlgorithms;
use Bid\Models\TeleasisCallManagement;
use Bid\Models\TeleasisPatients;
use Bid\Models\TeleduInterracion;
use Bid\Models\TelemeHighCostDisease;
use Illuminate\Database\Capsule\Manager;

class Tools
{
    const Teleducacion = 'teledu';
    const Telemedicina = 'telemed';
    const Teleasistencia = 'teleasis';
    const Administrador = 'admin';
    const Visualizador = 'visua';

    const read = "1";
    const write = "1";

    const meta_teleducacion_indicador_1 = 1000;
    const meta_teleducacion_indicador_2 = 15;
    const meta_teleducacion_indicador_3 = 30000;

    const meta_teleasistencia_indicador_1 = 8;
    const meta_teleasistencia_indicador_2 = 1500;
    const meta_teleasistencia_indicador_3 = 50000;

    const meta_telemedicina_indicador_1 = 120000;
    const meta_telemedicina_indicador_2 = 75;
    const meta_telemedicina_indicador_3 = 36000;

    static public $type = [
            'teledu' => 'Teleducación',
            'telemed' => 'Telemedicina',
            'teleasis' => 'Teleasistencia',
            'admin' => 'Administrador',
            'visua' => 'Visualizador'
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
            ->whereIn("curso.codigoCurso", ['1110-2728','1145-2858','1109-3927','173502','180598','178717','181746','173658','182662','173640','187372','1421-5335','1421-5336','1421-5337'])->first();

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
	/*Temporalmente se quema el valor 1821 que corresponde a estudiantes certificados por curso COVID. Esta cifra no esta en la BD de Extension*/
    static function teleducacion_indicador_1()
    {
        $total = self::estudiantes() + self::profesionales() + 1821;

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
		/*
        $extension = Manager::connection("db_extension")
            ->table("certificado")
            ->leftJoin("oferta", "oferta.ofertaId", "=", "certificado.ofertaId")
            ->leftJoin("curso", "curso.cursoId", "=", "oferta.cursoId")
            ->select(Manager::raw("DATE_FORMAT(oferta.fechaCertificacion, \"%Y-%m\") AS \"ano_mes\", count(oferta.fechaCertificacion) AS Total"))
            ->whereIn("curso.codigoCurso", ['1110-2728','1145-2858','1109-3927','173502','180598','178717','181746','173658','182662','173640','187372','1421-5335','1421-5336','1421-5337'])
            ->where("oferta.fechaCertificacion", ">=", $fecha)
            ->groupBy("oferta.fechaCertificacion")
            ->orderBy("oferta.fechaCertificacion")
            ->get()->toArray();
			*/
			 $extension = \Illuminate\Database\Capsule\Manager::connection("db_extension")
            ->table("certificado")
            ->select(Manager::raw("DATE_FORMAT(oferta.fechaCertificacion, \"%Y-%m\") AS \"ano_mes\", count(oferta.fechaCertificacion) AS Total"))
            ->leftJoin("oferta", "oferta.ofertaId", "=", "certificado.ofertaId")
            ->leftJoin("curso", "curso.cursoId", "=", "oferta.cursoId")
            ->whereIn("curso.codigoCurso", ['1110-2728','1145-2858','1109-3927','173502','180598','178717','181746','173658','182662','173640','187372','1421-5335','1421-5336','1421-5337'])
			->where("oferta.fechaCertificacion", ">=", $fecha)
            ->groupBy("oferta.fechaCertificacion")
            ->orderBy("oferta.fechaCertificacion")
			->get()->toArray();

			$mooc = \Illuminate\Database\Capsule\Manager::connection("db_abiertas_moodle")
            ->table("mdl_course")
            ->select(Manager::raw("CONCAT(YEAR(FROM_UNIXTIME(mdl_grade_grades.timemodified)),'-',MONTH(FROM_UNIXTIME(mdl_grade_grades.timemodified))) AS ano_mes, count(mdl_grade_items.timemodified) AS Total"))
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
			->groupBy(Manager::raw("ano_mes"))
            ->get()->toArray();/*
        $mooc = Manager::connection("db_abiertas_moodle")
            ->table("mdl_course")
            ->join("mdl_context", "mdl_context.instanceid", "=", "mdl_course.id")
            ->join("mdl_role_assignments", "mdl_role_assignments.contextid", "=", "mdl_context.id")
            ->join("mdl_user", "mdl_user.id", "=", "mdl_role_assignments.userid")
            ->join("mdl_grade_grades", "mdl_grade_grades.userid", "=", "mdl_user.id")
            ->join("mdl_grade_items", "mdl_grade_items.id", "=", "mdl_grade_grades.itemid")
            ->join("mdl_course_categories", "mdl_course_categories.id", "=", "mdl_course.category")
            ->select(Manager::raw("CONCAT(YEAR(FROM_UNIXTIME(mdl_grade_grades.timemodified)),'-',MONTH(FROM_UNIXTIME(mdl_grade_grades.timemodified))) AS ano_mes, count(mdl_grade_items.timemodified) AS Total"))
            ->where("mdl_grade_items.courseid", "=", \Illuminate\Database\Capsule\Manager::raw("mdl_course.id"))
            ->where("mdl_grade_items.itemtype", "=",'course')
            ->where("mdl_role_assignments.roleid", 5)
            ->where("mdl_grade_grades.finalgrade", ">=", '3.0')
            ->where("mdl_grade_items.timemodified", ">=", $fecha)
            ->groupBy(Manager::raw("ano_mes"))
            ->get()->toArray();
			*/
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
            ->select(Manager::raw("CONCAT(curso.nombreCurso, \" \", curso.nombreAuxiliar) AS \"curso\", COUNT(certificado.ofertaId) AS profesionales_capacitados, CONCAT('Extensión') as origen"))
            ->whereIn("curso.codigoCurso", ['1110-2728','1145-2858','1109-3927','173502','180598','178717','181746','173658','182662','173640','187372','1421-5335','1421-5336','1421-5337'])
            ->where("oferta.fechaCertificacion", ">=", $fecha)
            ->groupBy("curso")->get()->toArray();	

        $mooc = Manager::connection("db_abiertas_moodle")
            ->table("mdl_course")
            ->join("mdl_context", "mdl_context.instanceid", "=", "mdl_course.id")
            ->join("mdl_role_assignments", "mdl_role_assignments.contextid", "=", "mdl_context.id")
            ->join("mdl_user", "mdl_user.id", "=", "mdl_role_assignments.userid")
            ->join("mdl_grade_grades", "mdl_grade_grades.userid", "=", "mdl_user.id")
            ->join("mdl_grade_items", "mdl_grade_items.id", "=", "mdl_grade_grades.itemid")
            ->join("mdl_course_categories", "mdl_course_categories.id", "=", "mdl_course.category")
            ->select(Manager::raw("mdl_course.id AS \"idcurso\", mdl_course.fullname AS \"curso\", COUNT(mdl_course.id) AS \"profesionales_capacitados\", CONCAT(\"MOOC - Aulas Abiertas\") as \"origen\" "))
            ->where("mdl_grade_items.courseid", "=", \Illuminate\Database\Capsule\Manager::raw("mdl_course.id"))
            ->where("mdl_grade_items.itemtype", "=",'course')
            ->where("mdl_role_assignments.roleid", 5)
            ->where("mdl_grade_grades.finalgrade", ">=", '3.0')
            ->where("mdl_grade_items.timemodified", ">=", $fecha)
            ->groupBy(Manager::raw("mdl_course.id"))->get()->toArray();

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

    static public function telemedicina_indicador_1()
    {
        $pdo = Manager::connection("db_telemedicina")->getPdo();
        $q = $pdo->prepare("Select ca.idcupanexo3 
        from (((((((((((((((visita as v join encuentro as e on v.idvisita = e.idvisita) join anexo3 as a on e.idencuentro = a.idanexo3) join cupanexo3 as ca on a.idanexo3 = ca.idanexo3)
        join paciente as p on v.idpaciente = p.idpaciente) join persona as p2 on p.idpersona = p2.idpersona) join notaclinica as nc on a.idnotaclinica = nc.idnotaclinica) 
        join tipoidentificacion as ti on ti.idtipoidentificacion = p2.idtipodocumento) join cie10 on nc.idcie10principal = cie10.idcie10) join sedeinstitucion as s on e.idsede = s.idsede)
        join ciudad as c on s.idmunicipio = c.idciudad) left join departamento as d2 on c.iddepartamento = d2.iddepartamento) join prestador as p3 on e.idprestador = p3.idprestador)
        left join cup on ca.idcup = cup.idcup) left join eps on eps.ideps = p2.ideps) left join motivodevolucioncupanexo3 as md on md.idmotivodevolucioncupanexo3 = ca.idmotivodevolucioncupanexo3)
        where ca.idestadocupanexo3 in ('1','2') and  nc.enviada  in ('true') and a.enviado in ('true')  and (not ca.idnotaclinicarespuesta is NULL)");

        $q->execute();


        return [
            "total" => $q->rowCount(),
            "porcentaje" => round(($q->rowCount() * 100)/self::meta_telemedicina_indicador_1),
            "meta" => self::meta_telemedicina_indicador_1
        ];
    }

    static public function telemedicina_indicador_1_more()
    {
        $attention = self::sqlTelemedicina("to_char(cupanexo3.fechacreacion, 'YYYY-MM') as ANO_MES, COUNT (*) as total", "ANO_MES", "ANO_MES");
        $patologias = self::sqlTelemedicina("cie10.codigo as COD, cie10.descripcion as patologia, count(*) as num_atenciones", "COD, patologia", "num_atenciones desc");
        return [
            "atentions" => $attention,
            "patologias" => $patologias
        ];

    }
    static public function telemedicina_indicador_2()
    {
        $municipios = self::sqlTelemedicina("COUNT (DISTINCT ciudad.nombre) as total");
        return [
            "total" => $municipios->total,
            "porcentaje" => round(($municipios->total * 100)/self::meta_telemedicina_indicador_2),
            "meta" => self::meta_telemedicina_indicador_2
        ];
    }

    static public function telemedicina_indicador_2_more()
    {
        $municipios = self::sqlTelemedicina("ciudad.nombre as municipio, COUNT (*) as total", "municipio", "total");

        return $municipios;
    }

    static public function telemedicina_indicador_3()
    {
        $fecha = "2018-04-30 00:00:00";

        $data = Manager::connection("db_telemedicina")
            ->table("cupanexo3")
            ->whereNotNull("cupanexo3.idnotaclinicarespuesta")
            ->select("cupanexo3.idnotaclinicarespuesta")->get();

        $ids = array_column(json_decode(json_encode($data->toArray()), true), "idnotaclinicarespuesta");


        $enfermedades = Manager::connection("db_telemedicina")
            ->table("notaclinica")
            ->join("cie10", "cie10.idcie10", "=", "notaclinica.idcie10principal")
            ->join("usuario", "usuario.idusuario", "=", "notaclinica.idusuarioactualizacion")
            ->join("cupanexo3", "cupanexo3.idnotaclinicarespuesta", "=", "notaclinica.idnotaclinica")
            ->join("anexo3", "anexo3.idanexo3", "=", "cupanexo3.idanexo3")
            ->join("encuentro", "encuentro.idencuentro", "=", "cupanexo3.idanexo3")
            ->join("visita", "visita.idvisita", "=", "encuentro.idvisita")
            ->join("paciente", "paciente.idpaciente", "=", "visita.idpaciente")
            ->join("persona", "persona.idpersona", "=", "paciente.idpersona")
            ->leftJoin("tipopertinencia", "tipopertinencia.idtipopertinencia", "=", "notaclinica.idtipopertinencia")
            ->leftJoin(Manager::raw("usuario as u2"), Manager::raw("u2.idusuario"), "=", "cupanexo3.idusuarioresidente")
            ->leftJoin("tipoidentificacion", "tipoidentificacion.idtipoidentificacion", "=", "persona.idtipodocumento")
            ->leftJoin("causanopertinencia", "causanopertinencia.idcausanopertinencia", "=", "notaclinica.idcausanopertinencia")
            ->join("sedeinstitucion", "sedeinstitucion.idsede", "=", "encuentro.idsede")
            ->join("ciudad", "ciudad.idciudad", "=", "sedeinstitucion.idmunicipio")
            ->leftJoin("tipodiagnostico", "tipodiagnostico.idtipodiagnostico", "=", "notaclinica.idtipodiagnostico")
            ->select(Manager::raw("count(*) as total"))
            ->whereIn(
                "notaclinica.idnotaclinica", $ids
            )
            ->whereIn(
                "cie10.codigo", TelemeHighCostDisease::all("codigo")->toArray()
            )
            ->where("notaclinica.fechacreacion", ">=", $fecha)
            ->first();

        return [
            "total" => $enfermedades->total,
            "porcentaje" => round(($enfermedades->total * 100)/self::meta_telemedicina_indicador_3),
            "meta" => self::meta_telemedicina_indicador_3
        ];

    }
    static public function telemedicina_indicador_3_more()
    {
        $fecha = "2018-04-30 00:00:00";

        $data = Manager::connection("db_telemedicina")
            ->table("cupanexo3")
            ->whereNotNull("cupanexo3.idnotaclinicarespuesta")
            ->select("cupanexo3.idnotaclinicarespuesta")->get();

        $ids = array_column(json_decode(json_encode($data->toArray()), true), "idnotaclinicarespuesta");


        $patients = Manager::connection("db_telemedicina")
            ->table("notaclinica")
            ->join("cie10", "cie10.idcie10", "=", "notaclinica.idcie10principal")
            ->join("usuario", "usuario.idusuario", "=", "notaclinica.idusuarioactualizacion")
            ->join("cupanexo3", "cupanexo3.idnotaclinicarespuesta", "=", "notaclinica.idnotaclinica")
            ->join("anexo3", "anexo3.idanexo3", "=", "cupanexo3.idanexo3")
            ->join("encuentro", "encuentro.idencuentro", "=", "cupanexo3.idanexo3")
            ->join("visita", "visita.idvisita", "=", "encuentro.idvisita")
            ->join("paciente", "paciente.idpaciente", "=", "visita.idpaciente")
            ->join("persona", "persona.idpersona", "=", "paciente.idpersona")
            ->leftJoin("tipopertinencia", "tipopertinencia.idtipopertinencia", "=", "notaclinica.idtipopertinencia")
            ->leftJoin(Manager::raw("usuario as u2"), Manager::raw("u2.idusuario"), "=", "cupanexo3.idusuarioresidente")
            ->leftJoin("tipoidentificacion", "tipoidentificacion.idtipoidentificacion", "=", "persona.idtipodocumento")
            ->leftJoin("causanopertinencia", "causanopertinencia.idcausanopertinencia", "=", "notaclinica.idcausanopertinencia")
            ->join("sedeinstitucion", "sedeinstitucion.idsede", "=", "encuentro.idsede")
            ->join("ciudad", "ciudad.idciudad", "=", "sedeinstitucion.idmunicipio")
            ->leftJoin("tipodiagnostico", "tipodiagnostico.idtipodiagnostico", "=", "notaclinica.idtipodiagnostico")
            ->select(Manager::raw("cie10.codigo as COD, cie10.descripcion as patologia, count(*) as num_atenciones"))
            ->whereIn(
                "notaclinica.idnotaclinica", $ids
            )
            ->whereIn(
                "cie10.codigo", TelemeHighCostDisease::all("codigo")->toArray()
            )
            ->where("notaclinica.fechacreacion", ">=", $fecha)
            ->groupBy(Manager::raw('COD, patologia'))
            ->orderByDesc(Manager::raw('num_atenciones'))
            ->get()->toArray();

        return $patients;
    }

    protected function sqlTelemedicina(String $select, String $groupby = null, String $orderby = null) {

        $fecha = "2018-04-30 00:00:00";
        $data = Manager::connection("db_telemedicina")
            ->table("cupanexo3")
            ->whereNotNull("cupanexo3.idnotaclinicarespuesta")
            ->select("cupanexo3.idnotaclinicarespuesta")->get();

        $ids = array_column(json_decode(json_encode($data->toArray()), true), "idnotaclinicarespuesta");
        if (is_null($groupby)) {
            $patients = Manager::connection("db_telemedicina")
                ->table("notaclinica")
                ->join("cie10", "cie10.idcie10", "=", "notaclinica.idcie10principal")
                ->join("usuario", "usuario.idusuario", "=", "notaclinica.idusuarioactualizacion")
                ->join("cupanexo3", "cupanexo3.idnotaclinicarespuesta", "=", "notaclinica.idnotaclinica")
                ->join("anexo3", "anexo3.idanexo3", "=", "cupanexo3.idanexo3")
                ->join("encuentro", "encuentro.idencuentro", "=", "cupanexo3.idanexo3")
                ->join("visita", "visita.idvisita", "=", "encuentro.idvisita")
                ->join("paciente", "paciente.idpaciente", "=", "visita.idpaciente")
                ->join("persona", "persona.idpersona", "=", "paciente.idpersona")
                ->leftJoin("tipopertinencia", "tipopertinencia.idtipopertinencia", "=", "notaclinica.idtipopertinencia")
                ->leftJoin(Manager::raw("usuario as u2"), Manager::raw("u2.idusuario"), "=", "cupanexo3.idusuarioresidente")
                ->leftJoin("tipoidentificacion", "tipoidentificacion.idtipoidentificacion", "=", "persona.idtipodocumento")
                ->leftJoin("causanopertinencia", "causanopertinencia.idcausanopertinencia", "=", "notaclinica.idcausanopertinencia")
                ->join("sedeinstitucion", "sedeinstitucion.idsede", "=", "encuentro.idsede")
                ->join("ciudad", "ciudad.idciudad", "=", "sedeinstitucion.idmunicipio")
                ->leftJoin("tipodiagnostico", "tipodiagnostico.idtipodiagnostico", "=", "notaclinica.idtipodiagnostico")
                ->whereIn(
                    "notaclinica.idnotaclinica", $ids
                )
                ->where("notaclinica.fechacreacion", ">=", $fecha)
                ->select(Manager::raw($select))
                ->first();
        } else {
            $patients = Manager::connection("db_telemedicina")
                ->table("notaclinica")
                ->join("cie10", "cie10.idcie10", "=", "notaclinica.idcie10principal")
                ->join("usuario", "usuario.idusuario", "=", "notaclinica.idusuarioactualizacion")
                ->join("cupanexo3", "cupanexo3.idnotaclinicarespuesta", "=", "notaclinica.idnotaclinica")
                ->join("anexo3", "anexo3.idanexo3", "=", "cupanexo3.idanexo3")
                ->join("encuentro", "encuentro.idencuentro", "=", "cupanexo3.idanexo3")
                ->join("visita", "visita.idvisita", "=", "encuentro.idvisita")
                ->join("paciente", "paciente.idpaciente", "=", "visita.idpaciente")
                ->join("persona", "persona.idpersona", "=", "paciente.idpersona")
                ->leftJoin("tipopertinencia", "tipopertinencia.idtipopertinencia", "=", "notaclinica.idtipopertinencia")
                ->leftJoin(Manager::raw("usuario as u2"), Manager::raw("u2.idusuario"), "=", "cupanexo3.idusuarioresidente")
                ->leftJoin("tipoidentificacion", "tipoidentificacion.idtipoidentificacion", "=", "persona.idtipodocumento")
                ->leftJoin("causanopertinencia", "causanopertinencia.idcausanopertinencia", "=", "notaclinica.idcausanopertinencia")
                ->join("sedeinstitucion", "sedeinstitucion.idsede", "=", "encuentro.idsede")
                ->join("ciudad", "ciudad.idciudad", "=", "sedeinstitucion.idmunicipio")
                ->leftJoin("tipodiagnostico", "tipodiagnostico.idtipodiagnostico", "=", "notaclinica.idtipodiagnostico")
                ->select(Manager::raw($select))
                ->whereIn(
                    "notaclinica.idnotaclinica", $ids
                )
                ->where("notaclinica.fechacreacion", ">=", $fecha)
                ->groupBy(Manager::raw($groupby))
                ->orderByRaw(Manager::raw($orderby))
                ->get()->toArray();
        }

        return $patients;
    }
}