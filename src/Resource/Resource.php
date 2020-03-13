<?php

namespace App\Resource;

use Slim\Container;

abstract class Resource
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function __get($key)
    {
        return $this->container->get($key);
    }
}