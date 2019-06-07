<?php

namespace Bid\Controllers;


class Controller
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __invoke($request, $response, $args)
    {
        return $response;
    }

    public function __get($name)
    {
        if ($this->container->get($name)) {
            return $this->container->get($name);
        }
        return null;
    }
}