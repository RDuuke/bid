<?php

namespace Bid\Controllers;


use Bid\Models\TeleasisAlgorithms;
use Bid\Models\TeleasisCallManagement;
use Bid\Models\TeleasisCallPathology;
use Bid\Models\TeleasisPatients;
use function Bid\Tools\getMothSpanish;
use Bid\Tools\Tools;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use PhpOffice\PhpSpreadsheet\Shared\TimeZone;
use Slim\Http\Response;
use Slim\Http\Request;

class TeleasistenciaController extends Controller
{
    function index (Request $request, Response $response)
    {
        return $this->view->render($response, "teleasistencia/home.twig", [
            "title" => "Teleasistencia",
            "indicador1" => Tools::teleasistencia_indicador_1(),
            "indicador2" => Tools::teleasistencia_indicador_2(),
            "indicador3" => Tools::teleasistencia_indicador_3()
        ]);
    }

    function indicador1 ( Request $request, Response $response)
    {
        return $this->view->render($response, "teleasistencia/more_info/indicador1.twig", [
            "title" => "Teleasistencia indicador 1",
            "algoritmos" => TeleasisAlgorithms::all()
        ]);
    }

    function indicador2 ( Request $request, Response $response)
    {
        return $this->view->render($response, "teleasistencia/more_info/indicador2.twig", [
            "title" => "Teleasistencia indicador 2",
            "patologias" => TeleasisPatients::all()
        ]);
    }

    function indicador3 (Request $request, Response $response)
    {
        $p = TeleasisCallPathology::all(["patalogia", "num_llamadas_saludable"])->toArray();
        dump($p);
        $pacientes = [];
        foreach ($p as $k => $value) {
            array_push($pacientes, [$value["patalogia"], $value["num_pacientes"]]);
        }

        dump($pacientes);
        die;
        $g = TeleasisCallManagement::all(["periodo", "num_llamadas_gestionadas"])->toArray();
        $call_g = [];
        foreach ($g as $k => $value) {
            $date = implode("-", array_reverse(explode("/", $value["periodo"])));

            $carbon = new Carbon($date, "America/Bogota");
            array_push($call_g, [getMothSpanish($carbon->format("F Y")), $value["num_llamadas_gestionadas"]]);
        }

        $o = TeleasisCallManagement::select(Manager::raw("SUM(llamada_saludable) as \"LLAMADA INTERACTIVA\", 
        SUM(linea) as \"LINEA 01800\", SUM(telepsicologia) as \"TELEPSICOLOGIA\""))->first()->toArray();
        $origen = [];
        foreach ($o as $k => $value) {
            array_push($origen, [ucfirst(strtolower($k)), $value]);
        }
        return $this->view->render($response, "teleasistencia/more_info/indicador3.twig", [
            "title" => "Teleasistencia indicador 3",
            "pacientes" => $pacientes,
            "gestionadas" => $call_g,
            "origen" => $origen
        ]);
    }
}