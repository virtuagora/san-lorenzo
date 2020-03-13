<?php

namespace App\Action;

use App\Util\Exception\UnauthorizedException;
use App\Util\Exception\AppException;

class PagesAction
{
    protected $options;
    protected $session;
    protected $identity;
    protected $view;
    protected $db;

    public function __construct($options, $session, $identity, $view, $db)
    {
        $this->options = $options;
        $this->session = $session;
        $this->identity = $identity;
        $this->view = $view;
        $this->db = $db;
    }

    public function showHome($request, $response, $params)
    {
        return $this->view->render($response, 'index.twig', []);
    }
    public function showTerminos($request, $response, $params)
    {
        return $this->view->render($response, 'sl/pages/terms.twig', []);
    }
    public function showAgenda($request, $response, $params)
    {
        $calendar = $this->options->getOption('calendar')->toArray();
        return $this->view->render($response, 'sl/pages/agenda.twig', [
            'calendar' => $calendar,
        ]);
    }
    public function showFAQ($request, $response, $params)
    {
        return $this->view->render($response, 'sl/pages/faq.twig', [
        ]);
    }
    public function showCatalogo($request, $response, $params)
    {
        return $this->view->render($response, 'sl/pages/catalogo.twig', []);
    }

    public function showSeleccionados($request, $response, $params)
    {
        $start_date = $this->options->getOption('vote-launch')->value;
        $end_date = $this->options->getOption('vote-deadline')->value;
        $today = date('Y-m-d H:i:s');
        $appState = $this->options->getOption('current-state')->value == 'results';
        if (!$appState) {
            throw new AppException('¡Aun no es tiempo de ver los resultados!');
        }
        $proyectosSeleccionados = $this->db->query('App:Project', ['district'])->where('selected', true)->orderByDesc('likes')->get()->toArray();
        return $this->view->render($response, 'sl/pages/seleccionados.twig', [
            'proyectos' => $proyectosSeleccionados,
        ]);
    }
    public function showDatos($request, $response, $params)
    {
        $appState = $this->options->getOption('current-state')->value == 'results';
        $proyectos = array();
        if($appState){
            $proyectos = $this->db->query('App:Project', ['district'])->where('feasible',true)->orderByDesc("likes")->get();
        } 
        return $this->view->render($response, 'sl/pages/datos.twig', [
            'proyectos' => $proyectos,
        ]);
    }
    
    public function showSellos($request, $response, $params)
    {
        return $this->view->render($response, 'sl/pages/sellos.twig', [
            'sellos' => null,
        ]);
    }
    
    public function showResultsPPJoven($request, $response, $params)
    {
        return $this->view->render($response, 'sl/pages/ppjoven.twig');
    }

}
