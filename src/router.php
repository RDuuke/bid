<?php

$app->get("/", "HomeController:index")->setName("home");
$app->post("/sing", "HomeController:sing")->setName("sing");
$app->get("/logout", "HomeController:logout")->setName("logout");
$app->group("/panel", function () {
    $this->get("", "HomeController:home")->setName("panel");
    $this->get("/administrator", "HomeController:administrator")->setName("panel.administrator");

    $this->get("/user/list", "UserController:home")->setName("panel.user.list");
    $this->get("/user/edit/{id}", "UserController:edit")->setName("panel.user.edit");
    $this->get("/user/delete/{id}", "UserController:delete")->setName("panel.user.delete");
    $this->get("/user", "UserController:create")->setName("panel.user.create");
    $this->post("/user", "UserController:store")->setName("panel.user.store");
    $this->post("/user/update/{id}", "UserController:update")->setName("panel.user.update");

    $this->post("/file/courses", "DocumentController:upLoadExtensionCourses")->setName("file.courses");
    $this->post("/file/interactions", "DocumentController:uploadInteractions")->setName("file.interactions");
    $this->post("/file/algorithms", "DocumentController:uploadAlgorithms")->setName("file.algorithms");
    $this->post("/file/patients", "DocumentController:uploadPatients")->setName("file.patients");
    $this->post("/file/call/management", "DocumentController:uploadCallManagement")->setName("file.call.management");
    $this->post("/file/call/pathology", "DocumentController:uploadCallPathology")->setName("file.call.pathology");

    $this->group("/telemedicina", function () {
        $this->get("/index", "TelemedicinaController:index")->setName("telemedicina.index");
        $this->group("/indicador", function (){
           $this->get("/1", "TelemedicinaController:indicador1")->setName("telemedicina.indicador.1");
           $this->get("/2", "TelemedicinaController:indicador2")->setName("telemedicina.indicador.2");
           $this->get("/3", "TelemedicinaController:indicador3")->setName("telemedicina.indicador.3");
           $this->get("/4", "TelemedicinaController:indicador4")->setName("telemedicina.indicador.4");
        });
    });
    $this->group("/teleducacion", function (){
        $this->get("/index", "TeleducacionController:index")->setName("teleducacion.index");
        $this->group("/indicador", function (){
           $this->get("/1", "TeleducacionController:indicador1")->setName("teleducacion.indicador.1");
           $this->get("/2", "TeleducacionController:indicador2")->setName("teleducacion.indicador.2");
           $this->get("/3", "TeleducacionController:indicador3")->setName("teleducacion.indicador.3");
        });
    });

    $this->group("/teleasistencia", function (){
        $this->get("/index", "TeleasistenciaController:index")->setName("teleasistencia.index");

        $this->group("/indicador", function (){
            $this->get("/1", "TeleasistenciaController:indicador1")->setName("teleasistencia.indicador.1");
            $this->get("/2", "TeleasistenciaController:indicador2")->setName("teleasistencia.indicador.2");
            $this->get("/3", "TeleasistenciaController:indicador3")->setName("teleasistencia.indicador.3");
        });

    });

})->add(new \Bid\Middlewares\AuthMiddleware($container));

$app->get("/capacitados", function ($request, $response) use ($container) {
    /**
     * --Número de profesionales de la salud capacitados

    -   --Número de estudiantes capacitados con cursos MOOC (AULAS ABIERTAS)
     */
    $count = \Illuminate\Database\Capsule\Manager::connection("db_abiertas_moodle")
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
        ->orderByRaw("mdl_course.id DESC, mdl_user.lastname, mdl_user.firstname, mdl_grade_items.itemname")->first();

    dump($count->total);
});
$app->get("/extension", function ($request, $response) use ($container) {
    /**
     * --Numero de profesionales de la salud capacitados por la oferta de extensión
     */
    $count = \Illuminate\Database\Capsule\Manager::connection("db_extension")
        ->table("certificado")
        ->select(\Illuminate\Database\Capsule\Manager::raw("count(*) as total"))
        ->leftJoin("oferta", "oferta.ofertaId", "=", "certificado.ofertaId")
        ->leftJoin("curso", "curso.cursoId", "=", "oferta.cursoId")
        ->whereIn("curso.codigoCurso", ["102003"])->get();

    dump($count[0]->total);
});

$app->get("/telemedicina", function ($request, $response) {
    $count = \Illuminate\Database\Capsule\Manager::connection("db_telemedicina")
        ->table("notaclinica")
        ->take(10)
        ->get();
    dump($count);
    /*

    $dbconn = pg_connect("host=104.154.151.200 port=5432 dbname=LivingLabTransaccional user=UserBDAnalitics password=123456 options='--client_encoding=UTF8'")
        or die ("No se pudo conectar: " . pg_last_error());

*/
});
$app->get("/test", function ($request, $response) {
  return $this->view->render($response, "form.twig");
});

$app->post("/test", "DocumentController:uploadInteractions")->setName("test");