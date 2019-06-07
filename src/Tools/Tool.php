<?php

namespace Bid\Tools;


class Tool
{
    const Teleducacion = 'teledu';
    const Telemedicina = 'telemed';
    const Teleasistencia = 'teleasis';
    const Administrador = 'admin';

    static public $type = [
            'teledu' => 'Teleducación',
            'telemed' => 'Telemedicina',
            'teleasis' => 'Teleasistencia',
            'admin' => 'Administrador'
        ];
}