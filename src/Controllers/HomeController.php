<?php

namespace Bid\Controllers;


use Bid\Models\ExtensionCourses;
use Bid\Models\User;
use Bid\Tools\Tools;
use Faker\Factory;
use Slim\Http\Response;
use Slim\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request, Response $response)
    {
        return $this->view->render($response, "index.twig");
    }


    public function sing(Request $request, Response $response)
    {
        if ($this->auth->attemp($request->getParam("username"),
            $request->getParam("password"))) {

            return $response->withRedirect($this->router->pathFor("panel"));
        }
        return $response->withRedirect($this->router->pathFor("home"));
    }

    public function logout(Request $request, Response $response)
    {
        if ($this->auth->logout()) {
            return $response->withRedirect($this->router->pathFor("home"));
        }
        return $response->withRedirect($this->router->pathFor("panel"));
    }

    public function home(Request $request, Response $response)
    {
        return $this->view->render($response, "panel.twig", [
            "title" => "Panel",
            "teleducacion" => Tools::teleducacion_indicador_1(),
            "teleasistencia" => Tools::teleasistencia_indicador_1(),
            "telemedicina" => Tools::telemedicina_indicador_1()
        ]);
    }

    function administrator(Request $request, Response $response)
    {
        return $this->view->render($response, "administrator/home.twig", [
            "title" => "Administrador"
        ]);
    }
    protected function faker()
    {
        $faker = Factory::create();
        $institution = ['teledu','telemed','teleasis'];
        for ($i = 0; $i < 10; $i++) {
            $user = [
                "username" => $faker->userName,
                "password" => password_hash("1990duqe", PASSWORD_DEFAULT),
                "name" => $faker->name,
                "last_name" => $faker->lastName,
                "institution" => $institution[rand(0, 2)]
            ];

            User::create($user);
        }
    }

}