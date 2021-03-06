<?php

    namespace Bid\Tools;

    use Bid\Exceptions\moveFileException;
    use Illuminate\Database\Capsule\Manager;
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Reader\Csv;

    function getDataOfArchive($filename, $ex = "csv")
    {
        try {
            if ($ex == "csv") {
                $reader = new Csv();
                $reader->setDelimiter(";");
                $reader->setEnclosure('');
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
            $reader->setReadDataOnly(true);

            $spreadsheet = $reader->load($filename);

            $whorsheet = $spreadsheet->getActiveSheet();
		
            return (object) ["highestRow" => count($whorsheet->toArray()), "whorsheet" => $whorsheet->toArray()];
        } catch (\Exception $exception)
        {
            return $exception->getMessage();
        }
    }

    function getHighestDataRow($worksheet)
    {
        return count(array_filter(array_map("array_filter", $worksheet->toArray())));
    }

    function moveUploadFile(\Slim\Http\UploadedFile $uploadedFile)
    {
        try {

            $extension = \pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
            $bansename = \bin2hex(\rand(10000000, 99999999));
            $filename =  \sprintf('%s.%0.8s', $bansename, $extension);
            $filename = date("d-m-y") . "_" . $filename;
            $uploadedFile->moveTo(TEMP . $filename);
            return TEMP . $filename;
        } catch ( \Exception $e) {
            die ($e->getMessage() . " " . $e->getCode());
        }
    };


    function sort_by_orden ($array)
    {
        $v = [];
        for ($i = 0; $i < count($array); $i++)
        {
            if (isset($v[$array[$i]->ano_mes])) {
                $v[$array[$i]->ano_mes] = (int) $v[$array[$i]->ano_mes] +  $array[$i]->Total;
            } else {
                $v[$array[$i]->ano_mes] =(int) $array[$i]->Total;
            }
        }
        ksort($v);
        return $v;
    }

    function getMothSpanish($date) {


        $months_i = [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ];

        $months_e = [
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ];

        return str_replace($months_i, $months_e, $date);

    }

    function truncateTable(string $table)
    {
		
        $pdo = Manager::connection()->getPdo();
        if(!$pdo->query("TRUNCATE TABLE {$table}")) {
            return false;
        }
        return true;
    }
