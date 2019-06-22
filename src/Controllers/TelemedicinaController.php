<?php

namespace Bid\Controllers;


use function Bid\Tools\getMothSpanish;
use Bid\Tools\Tools;
use Carbon\Carbon;
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
            "indicador2" => Tools::telemedicina_indicador_2(),
            "indicador3" => Tools::telemedicina_indicador_3()
        ]);
    }

    public function indicador1 (Request $request, Response $response)
    {
        $data = Tools::telemedicina_indicador_1_more();
        $month = [];
        $values = [];
        for($i = 0; $i < count($data["atentions"]); $i++) {
            $carbon = new Carbon($data["atentions"][$i]->ano_mes, "America/Bogota");
            array_push($month, getMothSpanish($carbon->format("F Y")));
            array_push($values, $data["atentions"][$i]->total);
        }
        return $this->view->render($response, "telemedicina/more_info/indicador1.twig", [
            "title" => "Telemedicina indicador 1",
            "months" => $month,
            "values" => $values,
            "patologia" => $data["patologias"]
        ]);
    }

    public function indicador2 (Request $request, Response $response)
    {
        $municipios = Tools::telemedicina_indicador_2_more();
        return $this->view->render($response, "telemedicina/more_info/indicador2.twig", [
            "title" => "Telemedicina indicador 2",
            "municipios" => $municipios
        ]);
    }

    public function indicador3 (Request $request, Response $response)
    {
        $patologias = Tools::telemedicina_indicador_3_more();
        return $this->view->render($response, "telemedicina/more_info/indicador3.twig", [
            "title" => "Telemedicina indicador 3",
            "patologias" => $patologias
        ]);
    }
}