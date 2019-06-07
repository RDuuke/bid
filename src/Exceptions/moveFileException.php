<?php

namespace Bid\Exceptions;


class moveFileException extends \Exception
{

    public function errorMessage()
    {
        $errorMsg = "No se pudo mover el archivo al directorio resource/temp; error en la linea {$this->getLine()} del archivo {$this->getFile()}:<b>{$this->getMessage()} ";

        return $errorMsg;

    }

}