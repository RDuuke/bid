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
    $this->post("/file/list/high/diseases", "DocumentController:listingHighCostDiseases")->setName("file.list.high.diseases");
    $this->get("/file/download/{filename}/{ext}", "DocumentController:downloadFile")->setName("file.download");
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

