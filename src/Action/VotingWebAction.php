<?php

namespace App\Action;

use App\Util\ContainerClient;
use App\Util\Utils;
use App\Util\Exception\AppException;
use App\Util\Exception\UnauthorizedException;
use Carbon\Carbon;

class VotingWebAction extends ContainerClient
{
    public function showPrivateVoting($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'verified')) {
            throw new AppException(
                'No se encuentra autorizado para participar del presupuesto participativo'
            );
        }
        $user = $this->helper->getUserFromSubject($subject);
        $citizen = $this->helper->getCitizenFromSubject($subject);
        if ($citizen->voted){
            throw new AppException(
                'Ya ha participado del presupuesto participativo de San Lorenzo'
            );
        }
        if (!$this->resources['ballot']->isVotingTime()) {
            throw new AppException(
                'No es el periodo de votación'
            );
        }
        return $this->view->render($response, 'sl/vote/private-vote.twig', []);
    }

    public function showPublicVoting($request, $response, $params)
    {
        if (!$this->resources['ballot']->isVotingTime()) {
            throw new AppException(
                'No es el periodo de votación'
            );
        }
        return $this->view->render($response, 'sl/vote/public-vote.twig', [
            //TODO give me google key from settings.php
            'googleKey' => $this->settings['recaptcha']['public_key'],
        ]);
    }

    public function runPrivateVoting($request, $response, $params)
    {
        if (!$this->resources['ballot']->isVotingTime()) {
            throw new AppException(
                'No es el periodo de votación'
            );
        }
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'verified')) {
            throw new AppException(
                'No puede enviar tu voto, no se encuentra autorizado para participar del presupuesto participativo'
            );
        }
        $user = $this->helper->getUserFromSubject($subject);
        $citizen = $this->helper->getCitizenFromSubject($subject);
        if ($citizen->voted){
            throw new AppException(
                'No puede enviar tu voto, ya has participado del presupuesto participativo de San Lorenzo'
            );
        }
        $comus = array_filter(explode('&&', $request->getParam('comunitarios')));
        $insts = array_filter(explode('&&', $request->getParam('institucionales')));
        $ballot = $this->db->newInstance('App:OnlineBallot');
        $result = $this->resources['ballot']->sendOnlineBallot($ballot, $comus, $insts);
        if ($result !== true) {
            throw new AppException($this->getErrorMessage($result));
        }
        $this->resources['ballot']->registerVoter($citizen, 'user');
        return $this->view->render($response, 'sl/vote/success.twig', []);
    }

    public function runOnSiteVoting($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $dni = $this->helper->getSanitizedStr('matricula', $request->getParsedBody());
        $type = $this->helper->getSanitizedStr('tipo', $request->getParsedBody(), 'paper');
        $citizen = $this->db->query('App:Citizen')->where('dni', $dni)->firstOrFail();
        $code = null;
        $this->resources['ballot']->registerVoter($citizen, $type);
        if (in_array($type, ['tablet', 'link'])) {
            $ballot = $this->db->new('App:OnlineBallot');
            $ballot->created_at = new Carbon();
            $ballot->code = $this->helper->randomStr(6);
            $ballot->save();
            $code = $ballot->code;
        }
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Participacion guardada',
            'codigo' => $code,
            'status' => 200,
        ]);
    }

    public function runPublicVoting($request, $response, $params)
    {
        if (!$this->resources['ballot']->isVotingTime()) {
            throw new AppException(
                'No es el periodo de votación'
            );
        }
        $schema = [
			'type' => 'object',
			'properties' => [
				'captcha' => [
					'type' => 'string',
				],
				'codigo' => [
					'type' => 'string',
					'minLength' => 4,
					'maxLength' => 12,
				],
				'comunitarios' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'minLength' => 1,
					    'maxLength' => 10,
                    ],
                    'maxItems' => 3,
				],
				'institucionales' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                        'minLength' => 1,
					    'maxLength' => 10,
                    ],
                    'maxItems' => 3,
				],
			],
			'required' => [
				'codigo',
				'comunitarios',
                'institucionales',
                'captcha',
			],
			'additionalProperties' => false,
        ];
        $data = $this->validation->prepareData($schema, $request->getParsedBody());
        $v = $this->validation->fromSchema($schema);
        $v->assert($data);
        $captchaResp = $this->recaptcha->verify($data['captcha']);
        if (!$captchaResp->isSuccess()) {
            return $this->representation->returnMessage($request, $response, [
                'message' => 'CAPTCHA inválido',
                'error' => 'invalidCaptcha',
                'status' => 400,
            ]);
        }
        $ballot = $this->resources['ballot']->retrieveBallotFromCode($data['codigo']);
        if (is_string($ballot)) {
            return $this->representation->returnMessage($request, $response, [
                'message' => $this->getErrorMessage($ballot),
                'error' => $ballot,
                'status' => 400,
            ]);
        }
        $result = $this->resources['ballot']->sendOnlineBallot(
            $ballot, $data['comunitarios'], $data['institucionales']
        );
        if ($result !== true) {
            return $this->representation->returnMessage($request, $response, [
                'message' => $this->getErrorMessage($result),
                'error' => $result,
                'status' => 400,
            ]);
        }
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Boleta digital enviada',
            'status' => 200,
        ]);
    }

    private function getErrorMessage($error) {
        $messages = [
            'comusExceeded' => 'No puede votar más de 3 proyectos comunitarios',
            'instsExceeded' => 'No puede votar más de 3 proyectos institucionales',
            'invalidComus' => 'Ingresó proyectos comunitarios inválidos',
            'invalidInsts' => 'Ingresó proyectos institucionales inválidos',
            'notSentCode' => 'No ha enviado el código de votación',
            'invalidCode' => 'Código de votación inválido',
            'usedCode' => 'El código de votación ya fue utilizado',
        ];
        return $messages[$error] ?? 'Error desconocido';
    }
}