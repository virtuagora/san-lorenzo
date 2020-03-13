<?php

namespace App\Action;

class SessionAction
{
    protected $session;
    protected $identity;
    protected $view;
    protected $db;
    
    public function __construct($session, $identity, $view, $db, $recaptchaKey)
    {
        $this->session = $session;
        $this->identity = $identity;
        $this->view = $view;
        $this->db = $db;
        $this->recaptchaKey = $recaptchaKey;
    }
    
    public function showLogin($request, $response, $params)
    {
        if ($request->getAttribute('subject')->getType() != 'Annonymous') {
            return $response->withRedirect('/');
        }
        return $this->view->render($response, 'sl/login.twig', [
            'googleKey' => $this->recaptchaKey
        ]);
    }
    
    public function logout($request, $response, $params)
    {
        $this->session->signOut();
        return $response->withRedirect('/');
    }
    
    public function formLocalLogin($request, $response, $params)
    {
        $result = $this->identity->signIn('local', $request->getParsedBody());
        if ($result['status'] == 'success') {
            $user = $result['user'];
            // $group = $user->groups->first();
            // if ($group) {
            //     $groupInfo = [
            //     'id' => $group->id,
            //     'relation' => $group->pivot->relation,
            //     'name' => $group->name,
            //     ];
            // } else {
            //     $groupInfo = [
            //     'id' => null,
            //     'relation' => null,
            //     'name' => null,
            //     ];
            // }
            $session = $this->session->signIn($user->subject->toDummy([
            'user_id' => $user->id,
            // 'group' => $groupInfo,
            ]));
            return $response->withRedirect('/');
        } else {
            return $this->view->render($response, 'sl/login.twig', [
            'error' => $result['status'],
            ]);
        }
    }
}