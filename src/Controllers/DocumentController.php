<?php

namespace Bid\Controllers;


use Bid\Models\ExtensionCourses;
use Bid\Models\TeleasisAlgorithms;
use Bid\Models\TeleasisCallManagement;
use Bid\Models\TeleasisCallPathology;
use Bid\Models\TeleasisPatients;
use Bid\Models\TeleduInterracion;
use function Bid\Tools\getDataOfArchive;
use function Bid\Tools\moveUploadFile;
use Slim\Http\Response;
use Slim\Http\Request;
use Twig\Util\TemplateDirIterator;

class DocumentController extends Controller
{
    protected $creators = [];
    protected $errors = [];

    function uploadInteractions(Request $request, Response $response)
    {
        $uploadFiles = $request->getUploadedFiles();
        $archive = $uploadFiles['archive'];
        if ($archive->getError() == UPLOAD_ERR_OK) {
            $filename = moveUploadFile($archive);
             if (is_string($filename)) {
                 $data = getDataOfArchive($filename);
                 for ($row = 1; $row < $data->highestRow; $row ++) {
                     $value = [
                         "youtube" => $data->whorsheet[$row][0],
                         "saludando" => $data->whorsheet[$row][1],
                         "perlas_clinicas" => $data->whorsheet[$row][2],
                         "atulado" => $data->whorsheet[$row][3],
                         "fb_live" => $data->whorsheet[$row][4]
                     ];

                     if (TeleduInterracion::create($value)) {
                         array_push($this->creators, $value);
                     } else {
                         array_push($this->creators, $value);
                     }
                 }
                 return $this->view->render($response, "administrator/home.twig", [
                     "data" => [
                         "errors" => [
                             "total" => count($this->errors),
                             "register" => $this->errors
                         ],
                         "creators" => [
                             "total" => count($this->creators),
                             "register" => $this->creators
                         ],
                     ],
                     "code" => 200,
                     "message" => "Upload archive csv.",
                     "title_table" => "Interacciones en plataforma de Teleducación"
                 ]);
             }
        }
    }

    function upLoadExtensionCourses (Request $request, Response $response)
    {
        $data = $this->File($request);

        for($i = 1; $i < count($data); $i ++) {
            if (ExtensionCourses::updateOrCreate(["codigo" => $data[$i][0]], ["codigo" => $data[$i][0], "nombre" => $data[$i][1]])) {
                array_push($this->creators, $data[$i]);
            } else {
                array_push($this->errors, $data[$i]);
            }
        }
        return $this->view->render($response, "administrator/home.twig", [
            "data" => [
                "errors" => [
                    "total" => count($this->errors),
                    "register" => $this->errors
                ],
                "creators" => [
                    "total" => count($this->creators),
                    "register" => $this->creators
                ],
            ],
            "code" => 200,
            "message" => "Upload archive csv.",
            "title_table" => "Cargar archivo de cursos"
        ]);
    }

    function uploadAlgorithms(Request $request, Response $response)
    {
        $data = $this->File($request);

        for ($i = 1; $i < count($data); $i++) {
            if (TeleasisAlgorithms::updateOrCreate(["algoritmo" => $data[$i][0]],
                ["algotimo" => $data[$i][0], "fecha_creacion" => $data[$i][1]])) {
                array_push($this->creators, $data[$i]);
            } else {
                array_push($this->errors, $data[$i]);
            }
        }
        return $this->view->render($response, "administrator/home.twig", [
            "data" => [
                "errors" => [
                    "total" => count($this->errors),
                    "register" => $this->errors
                ],
                "creators" => [
                    "total" => count($this->creators),
                    "register" => $this->creators
                ],
            ],
            "code" => 200,
            "message" => "Upload archive csv.",
            "title_table" => "Algoritmos de teleasistencia"
        ]);
    }

    function uploadPatients(Request $request, Response $response)
    {
        $data = $this->File($request);

        for ($i = 1; $i < count($data); $i++) {
            if (TeleasisPatients::updateOrCreate(["patalogia" => $data[$i][0]],
                ["patalogia" => $data[$i][0], "num_pacientes" => $data[$i][1]])) {
                array_push($this->creators, $data[$i]);
            } else {
                array_push($this->errors, $data[$i]);
            }
        }
        return $this->view->render($response, "administrator/home.twig", [
            "data" => [
                "errors" => [
                    "total" => count($this->errors),
                    "register" => $this->errors
                ],
                "creators" => [
                    "total" => count($this->creators),
                    "register" => $this->creators
                ],
            ],
            "code" => 200,
            "message" => "Upload archive csv.",
            "title_table" => "Pacientes de teleasistencias"
        ]);
    }

    function uploadCallManagement(Request $request, Response $response)
    {
        $data = $this->File($request);

        for ($i = 1; $i < count($data); $i++) {
            if (TeleasisCallManagement::updateOrCreate(["periodo" => $data[$i][0]],
                [
                    "periodo" => $data[$i][0], "num_llamadas_gestionadas" => $data[$i][1],
                    "llamada_saludable" => $data[$i][2], "linea" => $data[$i][3],
                    "telepsicologia" => $data[$i][4]
                ])) {
                array_push($this->creators, $data[$i]);
            } else {
                array_push($this->errors, $data[$i]);
            }
        }
        return $this->view->render($response, "administrator/home.twig", [
            "data" => [
                "errors" => [
                    "total" => count($this->errors),
                    "register" => $this->errors
                ],
                "creators" => [
                    "total" => count($this->creators),
                    "register" => $this->creators
                ],
            ],
            "code" => 200,
            "message" => "Upload archive csv.",
            "title_table" => "Gestion llamadas de teleasistencias"
        ]);
    }

    function uploadCallPathology(Request $request, Response $response)
    {
        $data = $this->File($request);

        for ($i = 1; $i < count($data); $i++) {
            if (TeleasisCallPathology::updateOrCreate(["patalogia" => $data[$i][0]],
                ["patalogia" => $data[$i][0], "num_llamadas_saludable" => $data[$i][1]])) {
                array_push($this->creators, $data[$i]);
            } else {
                array_push($this->errors, $data[$i]);
            }
        }
        return $this->view->render($response, "administrator/home.twig", [
            "data" => [
                "errors" => [
                    "total" => count($this->errors),
                    "register" => $this->errors
                ],
                "creators" => [
                    "total" => count($this->creators),
                    "register" => $this->creators
                ],
            ],
            "code" => 200,
            "message" => "Upload archive csv.",
            "title_table" => "Llamadas patalogía de teleasistencia"
        ]);
    }

    function listingHighCostDiseases(Request $request, Response $response)
    {
        echo "data";
        dump($this->File($request));
        die;
    }
    protected  function File(Request $request)
    {
        $uploadFiles = $request->getUploadedFiles();
        $archive = $uploadFiles['archive'];
        if ($archive->getError() == UPLOAD_ERR_OK) {
            $filename = moveUploadFile($archive);
            if (is_string($filename)) {
                $ex = \pathinfo($archive->getClientFilename(), PATHINFO_EXTENSION);
                $data = getDataOfArchive($filename, $ex);
               return $data->whorsheet;
            }
        }
        return $archive->getError();
    }
}