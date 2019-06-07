<?php

namespace Bid\Controllers;


use Bid\Models\ExtensionCourses;
use Bid\Models\TeleduInterracion;
use function Bid\Tools\getMothSpanish;
use Bid\Tools\Tools;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use Slim\Http\Request;
use Slim\Http\Response;

class TeleducacionController extends Controller
{
    function index (Request $request, Response $response)
    {
        $created = TeleduInterracion::first();

        return $this->view->render($response, "teleducacion/home.twig",
            [
                "title" => "Teleducación",
                "indicador1" => Tools::teleducacion_indicador_1(),
                "indicador2" => Tools::teleducacion_indicador_2(),
                "indicador3" => Tools::teleducacion_indicador_3(),
                "created_at" => $created->created_at
            ]
        );
    }

    function indicador2(Request $request, Response $response)
    {
        $fecha = "2018-04-30 00:000:00";
        $valor1 = Manager::connection("db_abiertas_gestion")->table("curso")
            ->where("fecha", ">", $fecha)
            ->where("estado", 1)
            ->select("*")
            ->get()->toArray();
        $valor2 = ExtensionCourses::all()->toArray();
        $courses = array_merge($valor1, $valor2);

        return $this->view->render($response, "teleducacion/more_info/indicador2.twig",
            [
                "title" => "Teleducación indicador 2",
                "courses" => $courses
            ]
        );
    }

    function indicador1(Request $request, Response $response)
    {
        $data = Tools::teleducacion_indicador_more_1();
        $month = [];
        foreach (array_keys($data) as $key) {
            $carbon = new Carbon($key, "America/Bogota");
            array_push($month, getMothSpanish($carbon->format("F Y")));
        }
        $values = array_values($data);
        $courses = Tools::teleducacion_indicador_more_1_more();
        return $this->view->render($response, "teleducacion/more_info/indicador1.twig", [
            "title" => "Teleducación indicador 3",
            "months" => $month,
            "values" => $values,
            "courses" => $courses
        ]);
    }

    function indicador3(Request $request, Response $response)
    {
        $data = TeleduInterracion::first()->toArray();
        $labels = array_keys($data);
        $labels = Tools::replaceValues($labels);
        $values = array_values($data);
        return $this->view->render($response, "teleducacion/more_info/indicador3.twig", [
            "title" => "Teleducación indicador 3",
            "origen" => $labels,
            "values" => $values
        ]);
    }
    

}