<?php

namespace App\Service;

use ArrayAccess;
use ReflectionClass;
use Slim\Container;
use App\Util\Exception\SystemException;

class ResourcesService implements ArrayAccess
{
    protected $container;
    protected $resources;

    public function __construct(Container $container)
    {
        $this->resources = [];
        $this->container = $container;
    }

    // TODO revisar si no se hace nada con este método
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }

    public function offsetGet($r) {
        if (isset($this->resources[$r])) {
            return $this->resources[$r];
        }
        $className = 'App\\Resource\\'.ucfirst($r).'Resource';
        if (!class_exists($className)) {
            throw new SystemException('Resource not found');
        }
        $reflection = new ReflectionClass($className);
        $resource = $reflection->newInstance($this->container);
        $this->resources[$r] = $resource;
        return $resource;
    }
}
