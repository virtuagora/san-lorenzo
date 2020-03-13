<?php

namespace App\Action;

use App\Util\Exception\UnauthorizedException;
use App\Util\Exception\AppException;

// use DateInterval;
// use DatePeriod;
// use Datetime;

class BallotAction
{
    protected $ballotResource;
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
        $ballotResource, $options, $representation, $helper,
        $authorization, $db, $filesystem, $pagination, $view, $logger
    ) {
        $this->ballotResource = $ballotResource;
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

    public function retrieveOfflineBallots($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $ballots = $this->ballotResource->retrieveOfflineBallots();
        return $response->withJSON($ballots->toArray());
    }

    public function runNewInvalidOfflineVote($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $order = $request->getParam('order');
        $ballot = $this->db->new('App:OfflineBallot');
        $ballot->order = $order;
        $ballot->invalid = false;
        $ballot->save();
        return $response->withJSON([
            'message' => 'Ballot deleted',
            'status' => 200,
        ]);
    }

    public function runNewOfflineVote($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $order = $request->getParam('order');
        $ballot = $this->db->query('App:OfflineBallot')
            ->where('order', $order)
            ->first();
        if(isset($ballot)){
            throw new AppException('Ya existe una boleta del mismo numero');            
        }
        $comunitarios = $request->getParam('comunitarios');
        $institucionales = $request->getParam('institucionales');
        $comunitarios = array_filter(explode('&&', $comunitarios));
        $institucionales = array_filter(explode('&&', $institucionales));
        if (count($comunitarios) > 3) {
            throw new UnauthorizedException('No se puede procesar el voto, el voto más de 3 proyectos en una categoría');
        }
        if (count($institucionales) > 3) {
            throw new UnauthorizedException('No se puede procesar el voto, el voto más de 3 proyectos en una categoría');
        }
        $this->db->getDatabaseManager()->transaction(function () use ($comunitarios, $institucionales, $order) {
            $ballot = $this->db->new('App:OfflineBallot');
            $ballot->order = $order;
            $ballot->save();
            foreach ((array) $comunitarios as $code) {
                $vote = $this->db->new('App:OfflineVote');
                $vote->project_id = $code;
                $vote->ballot()->associate($ballot);
                $vote->save();
            }
            foreach ((array) $institucionales as $code) {
                $vote = $this->db->new('App:OfflineVote');
                $vote->project_id = $code;
                $vote->ballot()->associate($ballot);
                $vote->save();
            }
        });
        return $response->withJSON([
            'message' => 'Ballot created',
        ]);
    }

    public function runDeleteOfflineVote($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $balId = $this->helper->getSanitizedId('bal', $params);
        $ballot = $this->db->query('App:OfflineBallot')
            ->where('id', $balId)
            ->firstOrFail();
        $ballot->delete();
        return $response->withJSON([
            'message' => 'Ballot deleted',
        ],204);
    }

    public function runScrutiny($request, $response, $params)
    {
        
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $appState = $this->options->getOption('current-state')->value == 'pre-results';
        if (!$appState) {
            throw new AppException('La plataforma no se encuentra en estado "Pre-resultados" para realizar el escrutinio');
        }
        $lastTimeDone = $this->options->getOption('last-scrutiny');
        $lastTimeDone->value = date('Y-m-d H:i:s');
        $lastTimeDone->save();
        $countOnline = $this->db->table('online_votes')
            ->groupBy('project_id')
            ->selectRaw('online_votes.project_id as project, COUNT(*) as cant')
            ->get()
            ->keyBy('project');
            // ->toArray();
        $countOffline = $this->db->table('offline_votes')
            ->groupBy('project_id')
            ->selectRaw('offline_votes.project_id as project, COUNT(*) as cant')
            ->get()
            ->keyBy('project');
            // ->toArray();
        $proyectos = $this->db->query('App:Project')->get();
        forEach($proyectos as $project){
            $cantidadOnline = 0;
            $cantidadOffline = 0;
            if(isset($countOnline[(string) $project->id])){
                $cantidadOnline = $countOnline[(string) $project->id]->cant;
            } 
            if(isset($countOffline[(string) $project->id])){
                $cantidadOffline = $countOffline[(string) $project->id]->cant;
            } 
            $project->likes = $cantidadOnline + $cantidadOffline;
            $project->save();
        }

        return $response->withRedirect($request->getHeaderLine('HTTP_REFERER'));
    }

}
