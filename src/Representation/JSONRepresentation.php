<?php

namespace App\Representation;

class JSONRepresentation implements RepresentationInterface
{
    public function convert($resource, $response, $options)
    {
        return $response->withJSON($resource);
    }

    public function returnMessage($response, $options)
    {
        $data = array_diff_key($options, [
            'status' => '',
            'template' => '',
        ]);
        return $response->withJSON($data);
    }
}
