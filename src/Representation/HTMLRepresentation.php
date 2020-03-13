<?php

namespace App\Representation;

class HTMLRepresentation implements RepresentationInterface
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function convert($resource, $response, $options)
    {
        if (isset($options['redirect'])) {
            return $response->withRedirect($options['redirect']); 
        } else {
            $template = isset($options['template'])?
                $options['template']:
                // TODO armar plantilla default
                'message.twig';
            return $this->view->render($response, $template, [
                'resource' => $resource,
            ]);
        }
    }

    public function returnMessage($response, $options)
    {
        $template = isset($options['template']) ?
            $options['template'] :
            // TODO armar plantilla default
            'message.twig';
        return $this->view->render($response, $template, [
            'message' => $options['message'],
        ]);
    }
}
