<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/05/2019
 * Time: 2:01 PM
 */

$dbconn = pg_connect("host=104.154.151.200 dbname=LivingLabTransaccional user=UserBDAnalitics password=123456")
or die ("No se pudo conectar: " . pg_last_error());
