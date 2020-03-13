<?php

namespace App\Action;

use App\Util\ContainerClient;
use App\Util\Utils;
use App\Util\Exception\AppException;
use App\Util\Exception\UnauthorizedException;

class ProjectApiAction extends ContainerClient
{
    public function retrieveMany($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $pagParams = $this->pagination->getParams($request, [
            'district' => [
                'type' => 'integer',
                'minimum' => 1,
            ],
            'type' => [
                'type' => 'string',
                'enum' => ['institucional', 'comunitario'],
            ],
            's' => [
                'type' => 'string',
            ],
            'random' => [
                'type' => 'boolean',
            ],
            'selected' => [
                'type' => 'boolean'
            ],
            'feasible' => [
                'type' => 'boolean'
            ],
            'proposals' => [
                'type' => 'boolean'
            ],
            'random' => [
                'type' => 'boolean'
            ],
            'author' => [
                'type' => 'integer',
                'minimum' => 1
            ]
        ]);
        $resultados = $this->resources['project']->retrieveMany($pagParams);
        $resultados->setUri($request->getUri());
        if ($this->authorization->hasRoles($subject, 'admin')) {
            $resultados->makeVisible(['author_email', 'author_dni', 'author_phone']);
        }
        return $this->pagination->renderResponse($response, $resultados);
    }

    public function createOne($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'createProject')) {
            throw new UnauthorizedException();
        }
        $project = $this->resources['project']->createOne($subject, $request->getParsedBody());
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Proyecto creado exitosamente',
            'status' => 200,
            'project' => $project->toArray(),
        ]);
    }

    public function updateOne($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        if (!$this->authorization->checkPermission($subject, 'updateProject', $project)) {
            throw new UnauthorizedException();
        }
        $data = $request->getParsedBody();
        $project = $this->resources['project']->updateOne($subject, $project, $data);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Información del proyecto actualizada exitosamente',
            'status' => 200,
            'project' => $project->toArray(),
        ]);
    }

    public function updateAuthor($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        if (!$this->authorization->checkPermission($subject, 'manageProject', $project)) {
            throw new UnauthorizedException();
        }
        $data = $request->getParsedBody();
        $this->resources['project']->updateAuthor($subject, $project, $data);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Autor del proyecto actualizado exitosamente',
            'status' => 200,
        ]);
    }

    public function updateNotes($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        if (!$this->authorization->checkPermission($subject, 'manageProject', $project)) {
            throw new UnauthorizedException();
        }
        $data = $request->getParsedBody();
        $this->resources['project']->updateNotes($subject, $project, $data);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Notas del proyecto actualizadas exitosamente',
            'status' => 200,
        ]);
    }

    public function updateFeasibility($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        if (!$this->authorization->checkPermission($subject, 'manageProject', $project)) {
            throw new UnauthorizedException();
        }
        $data = $request->getParsedBody();
        $this->resources['project']->updateFeasibility($subject, $project, $data);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Factibilidad del proyecto actualizada exitosamente',
            'status' => 200,
        ]);
    }

    public function updateSelected($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        if (!$this->authorization->checkPermission($subject, 'manageProject', $project)) {
            throw new UnauthorizedException();
        }
        $this->resources['project']->updateSelected($subject, $project, $data);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Selección del proyecto actualizada exitosamente',
            'status' => 200,
        ]);
    }

    public function deleteOne($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['group']
        );
        if (!$this->authorization->checkPermission($subject, 'updateProject', $project)) {
            throw new UnauthorizedException();
        }
        $this->resources['project']->deleteOne($subject, $project);
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Proyecto eliminado exitosamente',
            'status' => 200,
        ]);
    }
}