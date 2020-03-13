<?php

namespace App\Action;

abstract class AbstractAction
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }
}
