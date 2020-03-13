<?php

namespace App\Action;

use App\Util\Exception\UnauthorizedException;

// LEGACY
class VoteAction
{
    protected $options;
    protected $representation;
    protected $helper;
    protected $authorization;
    protected $db;
    protected $filesystem;
    protected $pagination;
    protected $view;
    protected $logger;
    
    public function __construct(
    $options, $representation, $helper, $authorization, $db, $filesystem, $pagination, $view, $logger
    ) {
        $this->options = $options;
        $this->representation = $representation;
        $this->helper = $helper;
        $this->authorization = $authorization;
        $this->db = $db;
        $this->filesystem = $filesystem;
        $this->pagination = $pagination;
        $this->view = $view;
        $this->logger = $logger;
    }
    
    public function showVoting($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'verified')) {
            throw new UnauthorizedException('No se encuentra autorizado para participar del presupuesto participativo');
        }
        $user = $this->helper->getUserFromSubject($subject);
        $citizen = $this->helper->getCitizenFromSubject($subject);
        if(!is_null($citizen->voted_at)){
            throw new UnauthorizedException('Ya ha participado del presupuesto participativo de San Lorenzo');
        }
        $start_date = $this->options->getOption('vote-launch')->value;
        $end_date = $this->options->getOption('vote-deadline')->value;
        $today = date('Y-m-d H:i:s');
        if($today <= $start_date){
            throw new UnauthorizedException('¡La votación no ha comenzado!');
        }
        if($today >= $end_date){
            throw new UnauthorizedException('¡La votación ha terminado!');
        }
        return $this->view->render($response, 'sl/vote/vote.twig', []);
    }
    
    public function runVoting($request, $response, $params)
    {
        $start_date = $this->options->getOption('vote-launch')->value;
        $end_date = $this->options->getOption('vote-deadline')->value;
        $today = date('Y-m-d H:i:s');
        if($today <= $start_date){
            throw new UnauthorizedException('¡La votación no ha comenzado!');
        }
        if($today >= $end_date){
            throw new UnauthorizedException('¡La votación ha terminado!');
        }
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'verified')) {
            throw new UnauthorizedException('No puede enviar tu voto, no se encuentra autorizado para participar del presupuesto participativo');
        }
        $user = $this->helper->getUserFromSubject($subject);
        $citizen = $this->helper->getCitizenFromSubject($subject);
        if(!is_null($citizen->voted_at)){
            throw new UnauthorizedException('No puede enviar tu voto, ya has participado del presupuesto participativo de San Lorenzo');
        }
        $comunitarios = $request->getParam('comunitarios');
        $institucionales = $request->getParam('institucionales');
        $comunitarios = array_filter(explode('&&', $comunitarios));
        $institucionales = array_filter(explode('&&', $institucionales));
        if(count($comunitarios) > 3){
            throw new UnauthorizedException('No se puede procesar tu voto, has enviado más de 3 proyectos en una categoría');
        }
        if(count($institucionales) > 3){
            throw new UnauthorizedException('No se puede procesar tu voto, has enviado más de 3 proyectos en una categoría');
        }
        $lastVote = $this->db->table('votes')->orderBy('id', 'desc')->first();
        $lastHash = null;
        if(!is_null($lastVote)){
            $lastHash = $lastVote->hash;
        }
        $this->db->getDatabaseManager()->transaction(function() use ($comunitarios, $institucionales, $citizen, $lastHash){
            foreach ((array)$comunitarios as $code) {
                $vote = $this->db->new('App:Vote');
                $vote->hash = $lastHash = hash('sha256', $code.$lastHash);
                $vote->project_id = $code;
                $vote->save();
            }
            foreach ((array)$institucionales as $code) {
                $vote = $this->db->new('App:Vote');
                $vote->hash = $lastHash = hash('sha256', $code.$lastHash);
                $vote->project_id = $code;
                $vote->save();  
            }
            $citizen->voted_at = date('Y-m-d H:i:s');
            $citizen->save();
        });
        return $this->view->render($response, 'sl/vote/success.twig', []);
    }
}