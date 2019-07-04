<?php

$container = $app->getContainer();
$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container["settings"]["db"], "db");
$capsule->addConnection($container["settings"]["db_extension"], "db_extension");
$capsule->addConnection($container["settings"]["db_abiertas_moodle"], "db_abiertas_moodle");
$capsule->addConnection($container["settings"]["db_abiertas_gestion"], "db_abiertas_gestion");
$capsule->addConnection($container["settings"]["db_telemedicina"], "db_telemedicina");
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container["db"] = function () use ($capsule) {
  return $capsule;
};

$container["view"] = function ($container) {
  $view =   new \Slim\Views\Twig(VIEW, [
      "cache" => false
  ]);
  $view->addExtension(new \Slim\Views\TwigExtension(
      $container->router,
      $container->request->getUri()
  ));

    $view->addExtension(new Knlv\Slim\Views\TwigMessages(
        new Slim\Flash\Messages()
    ));

      $view->getEnvironment()->addGlobal("types", \Bid\Tools\Tools::$type);
      $view->getEnvironment()->addGlobal("flash", $container->flash);
      //$view->getEnvironment()->addGlobal("auth", $_SESSION['user_bid']);
      $view->getEnvironment()->addGlobal("administrador", \Bid\Tools\Tools::Administrador);
      $view->getEnvironment()->addGlobal("teleasistencia", \Bid\Tools\Tools::Teleasistencia);
      $view->getEnvironment()->addGlobal("telemedicina", \Bid\Tools\Tools::Telemedicina);
      $view->getEnvironment()->addGlobal("teleducacion", \Bid\Tools\Tools::Teleducacion);
      $view->getEnvironment()->addGlobal("visualizador", \Bid\Tools\Tools::Visualizador);
      $view->getEnvironment()->addGlobal("user", $_SESSION["user_bid"]);

      return $view;
};

$container["auth"] = function () {
    return new Bid\Auth\Auth();
};

$container["flash"] = function ()
{
    return new Slim\Flash\Messages();
};

$container["HomeController"] = function ($container) {
    return new \Bid\Controllers\HomeController($container);
};

$container["UserController"] = function ($container) {
    return new \Bid\Controllers\UserController($container);
};

$container["DocumentController"] = function ($container) {
    return new \Bid\Controllers\DocumentController($container);
};

$container["TelemedicinaController"] = function ($container) {
    return new \Bid\Controllers\TelemedicinaController($container);
};

$container["TeleducacionController"] = function ($container) {
    return new \Bid\Controllers\TeleducacionController($container);
};

$container["TeleasistenciaController"] = function ($container) {
    return new \Bid\Controllers\TeleasistenciaController($container);
};