<?php

namespace Bid\Controllers;


use Bid\Tools\Tools;
use Illuminate\Database\Capsule\Manager;
use Slim\Http\Request;
use Slim\Http\Response;

class TelemedicinaController extends Controller
{
    public function index (Request $request, Response $response)
    {
        return $this->view->render($response, "telemedicina/home.twig", [
            "title" => "Telemedicina",
            "indicador1" => Tools::telemedicina_indicador_1(),
            "indicador2" => Tools::telemedicina_indicador_2()
        ]);
    }

    public function indicador1 (Request $request, Response $response)
    {
        $values = Tools::telemedicina_indicador_1_more();
        dump($values);
        die;
        return $this->view->render($response, "telemedicina/more_info/indicador1.twig");
    }

    public function indicador2 (Request $request, Response $response)
    {
        return $this->view->render($response, "telemedicina/more_info/indicador2.twig");
    }

    public function indicador3 (Request $request, Response $response)
    {
        return $this->view->render($response, "telemedicina/more_info/indicador3.twig");
    }
}