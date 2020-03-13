<?php

namespace App\Service;

use Psr\Http\Message\ServerRequestInterface as Request;
use App\Representation\RepresentationInterface;
use App\Util\Exception\SystemException;

class RepresentationService
{
    protected $representations;
    protected $knownContentTypes;

    public function __construct($representations)
    {
        $this->representations = $representations;
        $this->knownContentTypes = [
            'application/json' => 'json',
            'application/xml' => 'xml',
            'text/xml' => 'xml',
            'text/html' => 'html',
        ];
    }

    public function convert($resource, Request $request, $response, $options)
    {
        $repr = $this->getRepresentation($request, $response);
        return $repr->convert($resource, $response, $options);
    }

    public function returnMessage(Request $request, $response, $options)
    {
        $repr = $this->getRepresentation($request, $response);
        if (isset($options['status'])) {
            $response = $response->withStatus($options['status']);
        }
        return $repr->returnMessage($response, $options);
    }

    protected function determineContentType(Request $request)
    {
        $acceptHeader = $request->getHeaderLine('Accept');
        $mimeTypes = array_keys($this->knownContentTypes);
        $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $mimeTypes);
        if (count($selectedContentTypes)) {
            return current($selectedContentTypes);
        }
        if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
            $mediaType = 'application/' . $matches[1];
            if (in_array($mediaType, $mimeTypes)) {
                return $mediaType;
            }
        }
        return 'text/html';
    }

    protected function getRepresentation(Request $request, $response)
    {
        $type = $this->knownContentTypes[$this->determineContentType($request)];
        if (!isset($this->representations[$type])) {
            throw new SystemException('Representation not supported');
        }
        $repr = $this->representations[$type];
        if (!is_a($repr, RepresentationInterface::class)) {
            throw new SystemException('Invalid Representation');
        }
        return $repr;
    }
}
