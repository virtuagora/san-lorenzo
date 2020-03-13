<?php

// misc

// PagesAction

// SessionAction
$app->get('/logout', 'sessionAction:logout')->setName('logout');
$app->get('/login', 'sessionAction:showLogin')->setName('showLogin');
$app->post('/login', 'sessionAction:formLocalLogin')->setName('runLogin');

// UserAction
$app->group('/user', function () {
    $this->get('', 'userAction:get')->setName('getUsers'); // JSON get
    $this->get('/{usr}', 'userAction:getOne')->setName('getUser'); // JSON get
    $this->post('/signup', 'userAction:post')->setName('runNewUser');
    $this->post('/pending', 'userAction:newPendingUser')->setName('runNewPendingUser'); // JSON get
    $this->get('/signup/complete', 'userAction:showCompleteSignUp')->setName('showCompleteSignUp');
    $this->post('/restore-password/request', 'userAction:runRequestPasswordReset')->setName('runResetPassword'); // User sends its email for password reset
    $this->get('/restore-password/{usr}/{tkn}', 'userAction:showCompletePasswordRestore')->setName('showCompletePasswordRestore');
    $this->post('/restore-password/{usr}', 'userAction:runPasswordRestore')->setName('runRestorePassword'); // User sends its new password
    $this->post('/{usr}/password', 'userAction:postPassword')->setName('runUpdatePassword');

});

// VoteAction
$app->get('/votar', 'votingWebAction:showPrivateVoting')->setName('showVoting');
$app->get('/urna', 'votingWebAction:showPublicVoting')->setName('showUrna');
$app->post('/votar-privado', 'votingWebAction:runPrivateVoting')->setName('runPrivateVoting');
$app->post('/votar-publico', 'votingWebAction:runPublicVoting')->setName('runPublicVoting');

// PagesAction
$app->get('/', 'pagesAction:showHome')->setName('showHome');
$app->get('/terminos', 'pagesAction:showTerminos')->setName('showTerminos');
$app->get('/catalogo', 'pagesAction:showCatalogo')->setName('showCatalogo');
$app->get('/agenda', 'pagesAction:showAgenda')->setName('showAgenda');
$app->get('/consultas', 'pagesAction:showFAQ')->setName('showFAQ');
$app->get('/seleccionados', 'pagesAction:showSeleccionados')->setName('showSeleccionados');
$app->get('/datos', 'pagesAction:showDatos')->setName('showDatos');
$app->get('/sellos', 'pagesAction:showSellos')->setName('showSellos');
$app->get('/resultados-ppjoven', 'pagesAction:showResultsPPJoven')->setName('showResultsPPJoven');

$app->get('/sellos/{aud}/recibo', 'auditWebAction:downloadRecibo')->setName('showReciboSello');
$app->get('/sellos/{aud}/dataset', 'auditWebAction:downloadDataset')->setName('showDatasetSello');

// MiscAction
// $app->get('/install[/{extra}]', 'miscAction:runInstall');
$app->get('/update', 'miscAction:runUpdate');
$app->get('/ping', 'miscAction:getLoggedPing')->setName('getLoggedPing');

// ProjectWebAction
$app->group('/proyectos', function () {
    $this->get('/{pro}', 'projectWebAction:showOne')->setName('showProject');
    $this->get('/{pro}/presupuesto', 'projectWebAction:showOneBudget')->setName('showProjectBudget');
    $this->get('/{pro}/bitacora', 'projectWebAction:showOneJournal')->setName('showProjectJournal');
    $this->get('/{pro}/imagen', 'projectWebAction:showPicture')->setName('showProjectPicture');
    $this->post('/{pro}/imagen', 'projectWebAction:runUploadPicture')->setName('runUploadImage');
    $this->post('/{pro}/archivo', 'projectWebAction:runUploadDocument')->setName('runUploadDocument');
    $this->get('/{pro}/archivo/{doc}', 'projectWebAction:showDocument')->setName('showDocument');
    $this->post('/{pro}/archivo/{doc}/delete', 'projectWebAction:deleteDocument')->setName('deleteDocument');
});

$app->group('/api', function () {
    $this->get('/distritos', 'miscAction:getDistricts');
    $this->get('/proyectos', 'projectApiAction:retrieveMany')->setName('retrieveProject');
    $this->post('/proyectos', 'projectApiAction:createOne')->setName('createProject');
    $this->post('/proyectos/{pro}', 'projectApiAction:updateOne')->setName('updateProject');
    $this->post('/proyectos/{pro}/feasibility', 'projectApiAction:updateFeasibility')->setName('updateProjectFeasibility');
    $this->post('/proyectos/{pro}/notes', 'projectApiAction:updateNotes')->setName('updateProjectNotes');
    $this->post('/proyectos/{pro}/author', 'projectApiAction:updateAuthor')->setName('updateProjectAuthor');
    $this->post('/proyectos/{pro}/selected', 'projectApiAction:updateSelected')->setName('updateProjectSelected');
    $this->get('/sellos-tiempo', 'auditWebAction:showSellos')->setName('getSellos');
    $this->post('/sellos-tiempo', 'auditWebAction:runSellar')->setName('runSellar');
    $this->post('/sellos-tiempo/{aud}', 'auditWebAction:runComprobar')->setName('runComprobarSellos');
    $this->group('/admin', function () {
        $this->get('/ciudadanos', 'adminAction:getCiudadanoPadron')->setName('getCiudadanoPadron');
        $this->get('/ciudadano', 'adminAction:getUsuarioCiudadano')->setName('getUsuarioCiudadano');
        $this->post('/ciudadanos/participar', 'votingWebAction:runOnSiteVoting')->setName('runParticipacionPadron');
        $this->post('/ciudadanos/nuevo', 'citizenAction:runAddCitizen')->setName('runAddCitizen');
        $this->get('/administradores', 'userAction:getAdmins')->setName('getAdmins');
        $this->get('/pending-users', 'userAction:getPendingUsers')->setName('getPendingUsers');
        $this->post('/pending-users/resend', 'userAction:runResendPending')->setName('runResendPending');
        $this->get('/pending-voters', 'userAction:getPendingVoters')->setName('getPendingVoters');
        $this->post('/pending-voters/send', 'userAction:runSendPendingVoter')->setName('runSendPendingVoter');
        $this->post('/administradores/nuevo', 'userAction:runNewAdmin')->setName('runNewAdmin');
        $this->get('/candidatos', 'userAction:getCandidatos')->setName('getCandidatos');
        $this->post('/option/{opt}', 'adminAction:postOption');
        $this->get('/ballots/offline', 'ballotAction:retrieveOfflineBallots')->setName('getOfflineBallots');
        $this->post('/ballots/offline/nuevo', 'ballotAction:runNewOfflineVote')->setName('runNewOfflineVote');
        $this->post('/ballots/offline/nulo', 'ballotAction:runNewInvalidOfflineVote')->setName('runNewInvalidOfflineVote');
        $this->delete('/ballots/offline/{bal}/delete', 'ballotAction:runDeleteOfflineVote')->setName('runDeleteOfflineVote'); 
    });
});

// UserPanelAction
$app->group('/panel', function () {
    // GET /panel
    $this->get('', 'userPanelAction:showOverview')->setName('showUserPanelOverview');
    $this->group('/proyectos', function () {
        $this->get('', 'userPanelAction:showProjects')->setName('showUserPanelProjects');
        $this->get('/crear', 'userPanelAction:showCreateProject')->setName('showUserPanelCreateProject');
        $this->get('/cambiar-password', 'userPanelAction:showChangePassword')->setName('showUserPanelChangePassword');
        $this->get('/{pro}/editar', 'userPanelAction:showEditProject')->setName('showUserPanelUpdateProject');
        $this->get('/{pro}/archivos', 'userPanelAction:showProjectFiles')->setName('showUserPanelProjectFiles');
        $this->get('/{pro}/imagen', 'userPanelAction:showProjectImage')->setName('showUserPanelProjectImagen');
        $this->get('/{pro}/bitacora', 'userPanelAction:showProjectJournal')->setName('showUserPanelProjectJournal');
    });
});
// AdminAction
$app->group('/admin', function () {
    // GET /admin
    $this->get('', 'adminAction:showOverview')->setName('showAdminOverview');
    $this->group('/proyectos', function () {
        $this->get('', 'adminAction:showProjects')->setName('showAdminProjects');
        $this->get('/crear', 'adminAction:showCreateProject')->setName('showAdminCreateProject');
        $this->get('/{pro}/editar', 'adminAction:showEditProject')->setName('showAdminUpdateProject');
        $this->get('/{pro}/usuario', 'adminAction:showEditProjectUser')->setName('showAdminUpdateProjectUser');
        $this->get('/{pro}/factibilidad', 'adminAction:showEditProjectFeasibility')->setName('showAdminUpdateProjectFeasibility');
        $this->get('/{pro}/bitacora', 'adminAction:showProjectJournal')->setName('showAdminProjectJournal');
        $this->get('/{pro}/imagen', 'adminAction:showProjectImage')->setName('showAdminProjectImagen');
        $this->get('/{pro}/archivos', 'adminAction:showProjectFiles')->setName('showAdminProjectFiles');
        $this->get('/sellados', 'adminAction:showTimestamp')->setName('showAdminSellado');
        $this->get('/boletas', 'adminAction:showBoletas')->setName('showBoletas');
        $this->get('/seleccionados', 'adminAction:showSeleccionados')->setName('showSeleccionadosAdmin');
        $this->get('/factibles', 'adminAction:showProjectsFeasible')->setName('showAdminProjectsFeasible');
        $this->get('/no-factibles', 'adminAction:showProjectsNotFeasible')->setName('showAdminProjectsNotFeasible');
        $this->get('/pendientes', 'adminAction:showRegistrosPendientes')->setName('showRegistrosPendientes');
        $this->get('/votantes-pendientes', 'adminAction:showVotersPendientes')->setName('showVotersPendientes');
        $this->post('/{pro}/seleccionar', 'projectAction:runSeleccionar')->setName('runSeleccionar');
        $this->post('/{pro}/quitar', 'projectAction:runQuitar')->setName('runQuitar');
    });
    $this->group('/padron', function () {
        $this->get('', 'adminAction:showCitizens')->setName('showAdminCitizens');
        $this->get('/nuevo', 'adminAction:showAddCitizen')->setName('showAddCitizen');
    });
        $this->get('/escrutinio', 'adminAction:showEscrutinio')->setName('showEscrutinio');
    $this->post('/escrutinio/run', 'ballotAction:runScrutiny')->setName('runScrutiny');
    $this->get('/estadisticas', 'adminAction:showStats')->setName('showAdminStats');
    $this->get('/roles', 'adminAction:showRoles')->setName('showRoles');
    $this->get('/opciones', 'adminAction:showOptions')->setName('showOptions');
});
// pantalla con botón login con facebook
// $app->get('/fb-login-js', function ($request, $response, $params) {
//     return $this->view->render($response, 'test/fb-login-js.twig', [
//     ]);
// });

// hacer un post a esto con un campo "access" que tiene el access token
// si el usuario existe hay login exitoso
// si el usuario no existe en BD se muestra pantalla de confirmar registro
// $app->post('/fb-callback', function ($request, $response, $params) {
//     $result = $this->identity->signIn('facebook', $request->getParsedBody());
//     if ($result['status'] == 'success') {
//         $user = $result['user'];
//         $group = $user->groups->first();
//         $session = $this->session->signIn($user->subject->toDummy([
//         'user_id' => $user->id,
//         'group' => [
//         'id' => $group->id,
//         'relation' => $group->pivot->relation,
//         'name' => $group->name,
//         ],
//         ]));
//         return $response->withRedirect('/');
//     } elseif ($result['status'] == 'pending-user') {
//         return $this->view->render($response, 'base/facebook-registro.twig', [
//         'token' => $result['token'],
//         ]);
//     } else {
//         return $this->view->render($response, 'test/fb-login-link.twig', [
//         'link' => $result['status'],
//         ]);
//     }
// })->setName('fbCallbackGet');

// pantalla de confirmar registro, si se da ok el usuario entra identificado
// $app->post('/fb-register', function ($request, $response, $params) {
//     $data = $request->getParsedBody();
//     $user = $this->identity->registerUser([], $data['token']);
//     $group = $user->groups->first();
//     $session = $this->session->signIn($user->subject->toDummy([
//     'user_id' => $user->id,
//     'group' => [
//     'id' => $group->id,
//     'relation' => $group->pivot->relation,
//     'name' => $group->name,
//     ],
//     ]));
//     return $response->withRedirect('/');
// })->setName('fbRegister');

// pantalla que muestra si el usuario está identificado o no
// $app->get('/idle', function ($request, $response, $params) {
//     $sub = $request->getAttribute('subject');
//     return $this->view->render($response, 'test/simple.twig', [
//     'text' => $sub->getType(),
//     ]);
// });

///

// $app->get('/test', function ($request, $response, $params) {
//     $options = $this->db->query('App:Option')->get()->toArray();
//     return $this->view->render($response, 'test/simple.twig', [
//     'text' => $options,
//     ]);
//     //return $res->withJSON($this->session->get('user'));
// });

// views

// $app->get('/sign-up', function ($request, $response, $params) {
//     if ($request->getAttribute('subject')->getType() != 'Annonymous') {
//         return $response->withRedirect('/');
//     }
//     return $this->view->render($response, 'base/signUp.twig');
// })->setName('showSignUp');

// $app->get('/update-email/{usr}/{tkn}', 'userAction:updateEmail')->setName('runUpdUserEma');

// api

// $app->get('/category', 'miscAction:getCategories');

// $app->get('/region', 'miscAction:getRegions');
// $app->get('/region/{reg}/department', 'miscAction:getDepartments');
// $app->get('/department/{dep}/locality', 'miscAction:getLocalities');
// $app->get('/locality/{loc}', 'miscAction:getLocality');

// $app->get('/option/{opt}', 'adminAction:getOption');
// $app->post('/option/{opt}', 'adminAction:postOption');
// $app->post('/user/{usr}/verified-dni', 'adminAction:postVerifiedDni')->setName('runUpdUserDniVer');
// $app->put('/project/{pro}/notes', 'projectAction:putNotes')->setName('putProNot');
// $app->post('/user/role', 'userAction:postRole')->setName('runUpdUsrRol');
// $app->get('/admin/matriz/{opt}', 'adminAction:getMatriz')->setName('getMat');

// $app->post('/user/{usr}/profile', 'userAction:postProfile')->setName('runUpdUserPro');
// $app->post('/user/{usr}/public-profile', 'userAction:postPublicProfile')->setName('runUpdUserPub');
// $app->post('/user/{usr}/dni', 'userAction:postDni')->setName('runUpdUserDni');
// $app->get('/user/{usr}/dni', 'userAction:getDniFile')->setName('getUsrDni');
// $app->post('/user/{usr}/pending-email', 'userAction:postPendingEmail')->setName('runUpdUserPem');

// $app->get('/group/{gro}', 'groupAction:getOne')->setName('getGroup');
// $app->get('/group/{gro}/user', 'groupAction:getUsuarios')->setName('getGroUsr');
// $app->post('/group', 'groupAction:post')->setName('runCreGro');
// $app->post('/group/{gro}', 'groupAction:patch')->setName('runUpdGro');
// $app->post('/group/{gro}/invitation', 'groupAction:postInvitation')->setName('runCreGroInv');
// $app->post('/group/{gro}/request', 'groupAction:postRequest')->setName('runCreGroReq');
// $app->post('/group/{gro}/letter', 'groupAction:postLetter')->setName('runUpdGroLet');
// $app->post('/group/{gro}/agreement', 'groupAction:postAgreement')->setName('runUpdGroAgr');
// $app->get('/group/{gro}/letter', 'groupAction:getLetterFile')->setName('getGroLet');
// $app->get('/group/{gro}/agreement', 'groupAction:getAgreementFile')->setName('getGroAgr');
// $app->put('/group/{gro}/second/{usr}', 'groupAction:putSecond')->setName('runCreGroSec');
// $app->delete('/group/{gro}/user/{usr}', 'groupAction:deleteUser')->setName('runDelGroUsr');
// $app->delete('/group/{gro}/second/{usr}', 'groupAction:deleteSecond')->setName('runDelGroSec');
// $app->delete('/group/{gro}', 'groupAction:delete')->setName('delGro');

// $app->post('/group/accept-inv/{inv}', 'groupAction:postUserFromInvitation')->setName('runCreGroUsrInv');
// $app->post('/group/accept-req/{inv}', 'groupAction:postUserFromRequest')->setName('runCreGroUsrReq');

// $app->get('/project', 'projectAction:get')->setName('lisPro');
// $app->get('/project-random', 'projectAction:get20Random')->setName('lisPro20Random');
// $app->get('/project/{pro}', 'projectAction:getOne')->setName('getPro');
// $app->get('/project/{pro}', 'projectAction:getOne')->setName('getPro');
// $app->post('/project', 'projectAction:post')->setName('runCrePro');
// $app->post('/project/{pro}', 'projectAction:patch')->setName('runUpdPro');
// // $app->delete('/project/{pro}', 'projectAction:delete')->setName('delPro');
// $app->post('/project/{pro}/vote', 'projectAction:postVote')->setName('runCreProVot');
// $app->post('/project/{pro}/comment', 'projectAction:postComment')->setName('runCreProCom');
// $app->post('/comment/{com}/reply', 'projectAction:postReply')->setName('runCreProRep');
// $app->post('/comment/{com}/vote', 'projectAction:postCommentVote')->setName('runCreComVot');
// $app->get('/project/{pro}/comment', 'projectAction:getComments')->setName('getProCom');

// $app->post('/project/{pro}/review', 'projectAction:postReview')->setName('updProRev');
// $app->post('/project/{pro}/coordin', 'projectAction:postCoordin')->setName('updProCor');
// $app->delete('/project/{pro}/coordin', 'projectAction:deleteCoordin')->setName('delProCor');

// $app->post('/project/{pro}/picture', 'projectAction:postPicture')->setName('runUpdProPic');

// guille

// $app->get('/testing', function ($req, $res, $arg) {
//     return $this->view->render($res, 'node/vote/showVote.twig', [
//     ]);
// });

// $app->group('/settings', function () {
//     $this->get('', function ($req, $res, $arg) {
//         return $this->view->render($res, 'base/appSettings.twig', [
//         ]);
//     });
//     $this->get('/[{path:.*}]', function ($req, $res, $arg) {
//         return $this->view->render($res, 'base/appSettings.twig', [
//         ]);
//     });
// });

// $app->group('/node/create', function () {
//     $this->get('', function ($req, $res, $arg) {
//         return $this->view->render($res, 'node/create.twig', [
//         ]);
//     });
//     $this->get('/[{path:.*}]', function ($req, $res, $arg) {
//         return $this->view->render($res, 'node/create.twig', [
//         ]);
//     });
// });

// $app->group('/usuario', function () {
//     $this->get('/{usr}', function($request, $response, $params){
//         $usuario = $this->helper->getEntityFromId(
//         'App:User', 'usr', $params, ['groups.project']
//         );
//         $usuario->addVisible(['groups']);
//         return $this->view->render($response, 'ingenia/user/showUser.twig', [
//         'user' => $usuario,
//         ]);
//     })->setName('showProfile');
// });

// $app->get('/project/{pro}/print', function ($request, $response, $params) {
//     $subject = $request->getAttribute('subject');
//     $proyecto = $this->helper->getEntityFromId(
//     'App:Project', 'pro', $params, ['group']
//     );
//     if (!$this->authorization->checkPermission($subject, 'coordin', $proyecto)) {
//         throw new UnauthorizedException();
//     }
//     // var_dump($proyecto->organization['locality_id']);
//     // $orgLoc = null;
//     // if( $proyecto->organization != null ){
//     //     $orgLoc = $this->db->query('App:Locality')->where('id', $proyecto->organization['locality_id'])->first();
//     // }
//     return $this->view->render($response, 'ingenia/project/printProject.twig', [
//     'project' => $proyecto,
//     // 'orgLoc' => $orgLoc
//     ]);
// })->setName('printPro');

// $app->group('/proyecto', function () {
//     $this->get('/{pro}', function($request, $response, $params) {
//         $subject = $request->getAttribute('subject');
//         $proyecto = $this->helper->getEntityFromId(
//         'App:Project', 'pro', $params, ['category']
//         );
//         if ($subject->getType() == 'User') {
//             $voted = !is_null(
//             $proyecto->voters()
//             ->where('user_id', $subject->getExtra()['user_id'])
//             ->first()
//             );
//         } else {
//             $voted = false;
//         }
//         $proyecto->addVisible(['category_id']);
//         // return $response->withJSON($proyecto->toArray());
//         return $this->view->render($response, 'ingenia/project/showProject.twig', [
//         'project' => $proyecto,
//         'voted' => $voted,
//         ]);
//     })->setName('showProject');
//     $this->get('/{pro}/[{path:.*}]', function($request, $response, $params){
//         $proyecto = $this->helper->getEntityFromId(
//         'App:Project', 'pro', $params
//         );
//         $proyecto->addVisible(['goals', 'schedule', 'budget','category_id']);
//         // return $response->withJSON($proyecto->toArray());
//         return $this->view->render($response, 'ingenia/project/showProject.twig', [
//         'project' => $proyecto,
//         ]);
//     });
// });

// $app->get('/proyecto/{pro}', function($request, $response, $params){
//     $proyecto = $this->helper->getEntityFromId(
//     'App:Project', 'pro', $params
//     );
//     $proyecto->addVisible(['goals', 'schedule', 'budget','category_id']);
//     // return $response->withJSON($proyecto->toArray());
//     return $this->view->render($response, 'ingenia/project/showProject.twig', [
//     'project' => $proyecto,
//     ]);
// })->setName('shwPro');

// $app->get('/testemail1', function ($req, $res, $arg) {
//     return $this->view->render($res, 'emails/completeRegister.twig', [
//     'url' => 'https://virtuagora.org'
//     ]);
// });

// $app->get('/testemail2', function ($req, $res, $arg) {
//     return $this->view->render($res, 'emails/sendInvitation.twig', [
//     'url' => 'https://virtuagora.org',
//     'team' => 'Nombre de un equipo genial',
//     'comment' => 'Hola! Me gustaria que seas parte de mi equipo ingenia! Besos!'
//     ]);
// });

// $app->get('/testing3', function ($req, $res, $arg) {
//     return $this->view->render($res, 'test3.twig', [
//     ]);
// });

// $app->post('/testing/comment', 'testingAction:commentNodeAction')->setName('commentNode');
