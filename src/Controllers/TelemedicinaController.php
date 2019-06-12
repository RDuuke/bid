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
        $id = Tools::telemedicina_indicador_1();
        dump($id->take(10));
        dump(json_decode(json_encode($id->take(10)->toArray()), true));

        die;
        return $this->view->render($response, "telemedicina/home.twig");
    }

    public function indicador1 (Request $request, Response $response)
    {
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