<?php

namespace App\Service;

use App\Utils\SujetoDummy;

class ErrorHandlerService
{
    protected $logger;
    protected $exceptions;
    
    public function __construct(array $exceptions, $logger = null, $representation)
    {
        $this->logger = $logger;
        $this->exceptions = $exceptions;
        $this->representation = $representation;
    }
    
    public function __invoke($request, $response, $exception)
    {
        if (isset($this->logger)) {
            $this->logger->info(
            $exception->getMessage().
            ' ['.$exception->getFile().' - '.$exception->getLine().']'
            );
            //$this->logger->info($request->getHeaderLine('Accept'));
            //$this->logger->info(json_encode($request->getAttributes()));
        }
        $errorData = [
            'message' => 'Error interno',
            'status' => 500,
        ];
        foreach ($this->exceptions as $trigger => $handler) {
            if ($exception instanceof $trigger) {
                $errorData = call_user_func($handler, $response, $exception);
                break;
            }
        }
        return $this->representation->returnMessage(
            $request,
            $response,
            array_merge($errorData, [
                'template' => 'error.twig',
                'log' =>  str_replace('"',"'",$exception->getMessage()),
                'file' =>  $exception->getFile(),
                'line' =>  $exception->getLine(),
            ])
        );
    }
}