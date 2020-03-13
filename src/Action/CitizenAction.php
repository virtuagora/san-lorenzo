<?php

namespace App\Action;

use App\Util\Exception\UnauthorizedException;

class CitizenAction
{
    protected $citizenResource;
    protected $representation;
    protected $helper;
    protected $authorization;
    protected $pagination;
    protected $view;
    protected $filesystem;

    public function __construct($citizenResource, $representation, $helper, $authorization, $pagination, $view, $filesystem)
    {
        $this->citizenResource = $citizenResource;
        $this->representation = $representation;
        $this->helper = $helper;
        $this->authorization = $authorization;
        $this->pagination = $pagination;
        $this->view = $view;
        $this->filesystem = $filesystem;
    }

    public function runAddCitizen($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $citizen = $this->citizenResource->createOne($subject, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => 'citizen created succefully',
            'status' => 200,
            'citizen' => $citizen->toArray()
        ]);
    }
}
