<?php
/**
 * Created by PhpStorm.
 * User: RDuuke
 * Date: 6/05/2019
 * Time: 5:32 PM
 */

namespace Bid\Middlewares;


class Middleware
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
}