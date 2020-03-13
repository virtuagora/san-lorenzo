<?php

namespace App\Representation;

interface RepresentationInterface
{
    public function convert($resource, $response, $options);
    public function returnMessage($response, $options);
}
