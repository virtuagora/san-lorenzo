<?php

namespace App\Action;

use App\Util\Exception\UnauthorizedException;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Datetime;

class AdminAction
{
    protected $options;
    protected $representation;
    protected $helper;
    protected $authorization;
    protected $db;
    protected $filesystem;
    protected $pagination;
    protected $view;

    public function __construct(
        $options, $representation, $helper, $authorization, $db, $filesystem, $pagination, $view
    ) {
        $this->options = $options;
        $this->representation = $representation;
        $this->helper = $helper;
        $this->authorization = $authorization;
        $this->db = $db;
        $this->filesystem = $filesystem;
        $this->pagination = $pagination;
        $this->view = $view;
    }

    //
    public function showOverview($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        return $this->view->render($response, 'sl/admin/overview.twig', []);
    }

    public function showProjects($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyectos = $this->db->query('App:Project', ['district'])->get()->makeVisible(['author_id'])->toArray();
        
        return $this->view->render($response, 'sl/admin/projects.twig', [
            'proyectos' => $proyectos,
        ]);
    }
    public function showProjectsFeasible($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyectos = $this->db->query('App:Project', ['district'])
            ->where('feasible',true)->get()->makeVisible(['author_id'])->toArray();
        
        return $this->view->render($response, 'sl/admin/projects-feasible.twig', [
            'proyectos' => $proyectos,
        ]);
    }
    public function showProjectsNotFeasible($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyectos = $this->db->query('App:Project', ['district'])
            ->where('feasible',false)->get()->makeVisible(['author_id'])->toArray();
        
        return $this->view->render($response, 'sl/admin/projects-not-feasible.twig', [
            'proyectos' => $proyectos,
        ]);
    }
    public function showCreateProject($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        return $this->view->render($response, 'sl/admin/create-project.twig', []);
    }
    public function showEditProject($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyecto = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['author']
        );
        $proyecto->addVisible(['notes', 'author_phone', 'author_email', 'author_dni','author']);
        return $this->view->render($response, 'sl/admin/edit-project.twig', [
            'proyecto' => $proyecto->toArray(),
        ]);
    }
    public function showPrintProject($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyecto = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['author','district']
        );
        $proyecto->addVisible(['notes', 'author_phone', 'author_email', 'author_dni','author','created_at','updated_at']);
        return $this->view->render($response, 'sl/admin/print-project.twig', [
            'proyecto' => $proyecto->toArray(),
        ]);
    }
    public function showEditProjectUser($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyecto = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['author']
        );
        $proyecto->addVisible(['author']);
        return $this->view->render($response, 'sl/admin/edit-project-user.twig', [
            'proyecto' => $proyecto->toArray(),
        ]);
    }
    public function showProjectJournal($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyecto = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['author']
        );
        $userArr = array();
        foreach ($proyecto->journal as $key => $value) {
            $user = $this->helper->getEntityFromId(
            'App:User', $value['author_id'], null, ['subject']
            );
            $userArr[$value['author_id']] = $user->subject->toDummy()->toArray();
        }
        $proyecto->addVisible(['notes', 'author_phone', 'author_email', 'author_dni','author','journal']);
        return $this->view->render($response, 'sl/admin/edit-project-journal.twig', [
            'proyecto' => $proyecto->toArray(),
            'users' => $userArr
        ]);
    }
    public function showEditProjectFeasibility($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyecto = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['district']
        );
        return $this->view->render($response, 'sl/admin/edit-project-feasibility.twig', [
            'proyecto' => $proyecto->toArray(),
        ]);
    }
    public function showProjectFiles($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyecto = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['documents']
        );
        $proyecto->makeVisible(['documents']);
        return $this->view->render($response, 'sl/admin/edit-project-files.twig', [
            'proyecto' => $proyecto->toArray()
        ]);
    }
    
    public function showBoletas($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $paperBallotsCount = $this->db->query('App:StatisticalBallot')
            ->where('type', 'paper')
            ->count();

        // TODO revisar
        // el objetivo de esto es saber cuantas boletas papel debería haber?
        // $cantCiudadanos = $this->db->query('App:Citizen')
        //     ->whereNotNull('voted_at')
        //     ->count();
        // $cantVotosOnline = $this->db->table('users')
        //     ->join('subjects', 'users.subject_id', '=', 'subjects.id')
        //     ->join('citizens', 'subjects.citizen_id', '=', 'citizens.id')
        //     ->whereNotNull('subjects.citizen_id')
        //     ->whereNotNull('citizens.voted_at')
        //     ->whereDate('users.created_at', '<', 'citizens.voted_at')
        //     ->count();
        return $this->view->render($response, 'sl/admin/boletas.twig', [
            'cantVotos' => $paperBallotsCount // aca antes se restaba cantCiudadanos - cantVotosOnline
        ]);
    }
     public function showSeleccionados($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyectosSeleccionados = $this->db->query('App:Project', ['district'])->where('selected',true)->where('feasible',true)->get()->toArray();
        $proyectosNoSeleccionados = $this->db->query('App:Project', ['district'])->where('selected',false)->where('feasible',true)->get()->toArray();
        return $this->view->render($response, 'sl/admin/selected.twig', [
            'proyectosSeleccionados' => $proyectosSeleccionados,
            'proyectosNoSeleccionados' => $proyectosNoSeleccionados,
        ]);
        
    }

    // TODO actualizar
    public function showEscrutinio($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $lastParticipacion = $this->db->query('App:StatisticalBallot')->orderByDesc('created_at')->first();
        $lastOffline = $this->db->query('App:OfflineBallot')->orderByDesc('created_at')->first();
        $proyectos = $this->db->query('App:Project', ['district'])->where('feasible',true)->orderByDesc("likes")->get();
        return $this->view->render($response, 'sl/admin/escrutinio.twig', [
            'lastParticipacion' => $lastParticipacion->created_at,
            'lastOffline' => $lastOffline->created_at,
            'proyectos' => $proyectos
        ]);
    }
    public function showProjectImage($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $proyecto = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params
        );
        return $this->view->render($response, 'sl/admin/edit-project-image.twig', [
            'proyecto' => $proyecto->toArray(),
        ]);
    }
    public function showCitizens($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        return $this->view->render($response, 'sl/admin/sync.twig', []);
    }
    public function showAddCitizen($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        return $this->view->render($response, 'sl/admin/add-citizen.twig', []);
    }
    public function showStats($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $cantUsuarios = $this->db->query('App:User')
            ->count();
        $cantPendientes = $this->db->query('App:PendingUser')
            ->count();
        $cantVotosTotal = $this->db->query('App:StatisticalBallot')
            ->count();
        $cantVotosTablet = $this->db->query('App:StatisticalBallot')
            ->where('type', 'tablet')
            ->count();
        $cantVotosWeb = $this->db->query('App:StatisticalBallot')
            ->where('type', 'user')
            ->count();
        $cantVotosLink = $this->db->query('App:StatisticalBallot')
            ->where('type', 'link')
            ->count();
        $cantVotosPaper = $this->db->query('App:StatisticalBallot')
            ->where('type', 'paper')
            ->count();
        $votantesRegistradosVotaron = $this->db->table('users')
            ->join('subjects', 'users.subject_id', '=', 'subjects.id')
            ->join('citizens', 'subjects.citizen_id', '=', 'citizens.id')
            ->whereNotNull('subjects.citizen_id')
            ->where('citizens.voted', true)
            ->count();
        $cantCiudadanos = $this->db->query('App:Citizen')
            ->where('voted', true)
            ->count();
        $cantCiudadanosUsuarios = $this->db->query('App:Subject')
            ->whereNotNull('citizen_id')
            ->count();
        $votosTabletPerDay = $this->db->table('statistical_ballots')
            ->where('type', 'tablet')
            ->groupBy($this->db->getDatabaseManager()->raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as votes')
            ->get()
            ->keyBy('date');
        $votosWebPerDay = $this->db->table('statistical_ballots')
            ->where('type', 'user')
            ->groupBy($this->db->getDatabaseManager()->raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as votes')
            ->get()
            ->keyBy('date');
        $votosLinkPerDay = $this->db->table('statistical_ballots')
            ->where('type', 'link')
            ->groupBy($this->db->getDatabaseManager()->raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as votes')
            ->get()
            ->keyBy('date');
        $votosPaperPerDay = $this->db->table('statistical_ballots')
            ->where('type', 'paper')
            ->groupBy($this->db->getDatabaseManager()->raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as votes')
            ->get()
            ->keyBy('date');
        $votosTotalPerDay = $this->db->table('statistical_ballots')
            ->groupBy($this->db->getDatabaseManager()->raw('DATE(created_at)'))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as votes')
            ->get()
            ->keyBy('date');
        $votantesWebGenre = $this->db->table('statistical_ballots')
            ->where('type', 'user')
            ->groupBy('gender')
            ->selectRaw('gender as genero, COUNT(*) as votantes')
            ->get()
            ->keyBy('genero');
        $votantesTabletGenre = $this->db->table('statistical_ballots')
            ->where('type', 'tablet')
            ->groupBy('gender')
            ->selectRaw('gender as genero, COUNT(*) as votantes')
            ->get()
            ->keyBy('genero');
        $votantesLinkGenre = $this->db->table('statistical_ballots')
            ->where('type', 'link')
            ->groupBy('gender')
            ->selectRaw('gender as genero, COUNT(*) as votantes')
            ->get()
            ->keyBy('genero');
        $votantesPaperGenre = $this->db->table('statistical_ballots')
            ->where('type', 'paper')
            ->groupBy('gender')
            ->selectRaw('gender as genero, COUNT(*) as votantes')
            ->get()
            ->keyBy('genero');
        $votantesTotalGenre = $this->db->table('statistical_ballots')
            ->groupBy('gender')
            ->selectRaw('gender as genero, COUNT(*) as votantes')
            ->get()
            ->keyBy('genero');
        $votantesNeighbourhood = $this->db->table('users')
            ->join('subjects', 'users.subject_id', '=', 'subjects.id')
            ->join('citizens', 'subjects.citizen_id', '=', 'citizens.id')
            ->join('neighbourhoods', 'subjects.neighbourhood_id', '=', 'neighbourhoods.id')
            ->join('districts', 'neighbourhoods.district_id', '=', 'districts.id')
            ->whereNotNull('subjects.citizen_id')
            ->where('citizens.voted', true)
            ->groupBy('districts.id', 'neighbourhoods.id')
            ->selectRaw('districts.name as dname, neighbourhoods.name as nname, COUNT(*) as votantes')
            ->orderByDesc("votantes")
            ->get();
        $votantesDistricts = $this->db->table('users')
            ->join('subjects', 'users.subject_id', '=', 'subjects.id')
            ->join('citizens', 'subjects.citizen_id', '=', 'citizens.id')
            ->join('neighbourhoods', 'subjects.neighbourhood_id', '=', 'neighbourhoods.id')
            ->join('districts', 'neighbourhoods.district_id', '=', 'districts.id')
            ->whereNotNull('subjects.citizen_id')
            ->where('citizens.voted', true)
            ->groupBy('districts.id')
            ->selectRaw('districts.name as name, COUNT(*) as votantes')
            ->orderByDesc("votantes")
            ->get();
        $start_date = $this->options->getOption('vote-launch')->value;
        $end_date = $this->options->getOption('vote-deadline')->value;
        $today = new DateTime();
        if((new DateTime($end_date)) > $today){
            $period = new DatePeriod(
                new DateTime($start_date),
                new DateInterval('P1D'),
                new DateTime()
            );
        } else {
            $period = new DatePeriod(
                new DateTime($start_date),
                new DateInterval('P1D'),
                new DateTime($end_date)
            );
        }
        $arrayDates = array();
        foreach ($period as $key => $value) {
            $arrayDates[] = $value->format('Y-m-d');
        }
        return $this->view->render($response, 'sl/admin/stats.twig', [
            'cantUsuarios' => $cantUsuarios,
            'cantPendientes' => $cantPendientes,
            'cantVotosTotal' => $cantVotosTotal,
            'cantVotosTablet' => $cantVotosTablet,
            'cantVotosWeb' => $cantVotosWeb,
            'cantVotosLink' => $cantVotosLink,
            'cantVotosPaper' => $cantVotosPaper,
            'cantCiudadanos' => $cantCiudadanos,
            'cantCiudadanosUsuarios' => $cantCiudadanosUsuarios,
            'votosTabletPerDay' => $votosTabletPerDay->toArray(),
            'votosWebPerDay' => $votosWebPerDay->toArray(),
            'votosLinkPerDay' => $votosLinkPerDay->toArray(),
            'votosPaperPerDay' => $votosPaperPerDay->toArray(),
            'votosTotalPerDay' => $votosTotalPerDay->toArray(),
            'votantesWebGenre' => $votantesWebGenre,
            'votantesTabletGenre' => $votantesTabletGenre,
            'votantesLinkGenre' => $votantesLinkGenre,
            'votantesPaperGenre' => $votantesPaperGenre,
            'votantesTotalGenre' => $votantesTotalGenre,
            'votantesNeighbourhood' => $votantesNeighbourhood,
            'votantesDistricts' => $votantesDistricts,
            'votantesRegistradosVotaron' => $votantesRegistradosVotaron,
            'arrayDates' => $arrayDates,
            'arrayGenre' => array('M', 'F'),
        ]);
    }
    public function showRegistrosPendientes($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $cantUsuarios = $this->db->query('App:User')
            ->count();
        $cantPendientes = $this->db->query('App:PendingUser')
            ->count();
        $pendientesDate = $this->db->table('pending_users')
            ->groupBy($this->db->getDatabaseManager()->raw('DATE(pending_users.created_at)'))
            ->selectRaw('DATE(pending_users.created_at) as date, COUNT(*) as pendientes')
            ->get()
            ->keyBy('date');
        $start_date = $this->options->getOption('vote-launch')->value;
        $end_date = $this->options->getOption('vote-deadline')->value;
        $period = new DatePeriod(
            new DateTime($start_date),
            new DateInterval('P1D'),
            new DateTime($end_date)
        );
        $arrayDates = array();
        foreach ($period as $key => $value) {
            $arrayDates[] = $value->format('Y-m-d');
        }
        return $this->view->render($response, 'sl/admin/registrosPendientes.twig', [
            'cantUsuarios' => $cantUsuarios,
            'cantPendientes' => $cantPendientes,
            'pendientesDate' => $pendientesDate,
            'arrayDates' => $arrayDates,
        ]);
    }
    public function showVotersPendientes($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $cantCiudadanosUsuarios = $this->db->query('App:Subject')
            ->whereNotNull('citizen_id')
            ->count();
        $votantesRegistradosVotaron = $this->db->table('users')
            ->join('subjects', 'users.subject_id', '=', 'subjects.id')
            ->join('citizens', 'subjects.citizen_id', '=', 'citizens.id')
            ->whereNotNull('subjects.citizen_id')
            ->where('citizens.voted', true)
            ->count();
        return $this->view->render($response, 'sl/admin/votersPendientes.twig', [
            'cantCiudadanosUsuarios' => $cantCiudadanosUsuarios,
            'votantesRegistradosVotaron' => $votantesRegistradosVotaron,
        ]);
    }

    public function showRoles($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $administrators = $this->db->query('App:User')
            ->whereHas('subject.roles', function ($qry) {
                $qry->whereIn('role_id', ['admin']);
            })->get();
        $administrators->makeVisible(['email']);
        return $this->view->render($response, 'sl/admin/roles.twig', [
            'administradores' => $administrators->toArray(),
        ]);
    }
    public function showOptions($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $options = $this->options->getOptions()->toArray();
        return $this->view->render($response, 'sl/admin/options.twig', [
            'options' => $options,
        ]);
    }

    public function showTimestamp($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $trails = $this->db->query('App:AuditTrail')->get();
        return $this->view->render($response, 'sl/admin/timestamps.twig', [
            'trails' => $trails->toArray()
        ]);
    }

    // =================================================

    // public function getOptions($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if (!$this->authorization->checkPermission($subject, 'admin')) {
    //         throw new UnauthorizedException();
    //     }
    //     $options = $this->options->getOptions();
    //     return $response->withJSON($options->toArray());
    // }

    // public function getOption($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if (!$this->authorization->checkPermission($subject, 'admin')) {
    //         throw new UnauthorizedException();
    //     }
    //     $opt = $this->helper->getSanitizedStr('opt', $params);
    //     $option = $this->options->getOption($opt);
    //     return $response->withJSON($option->toArray());
    // }

    public function postOption($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $opt = $this->helper->getSanitizedStr('opt', $params);
        $val = $this->helper->getSanitizedStr('value', $request->getParsedBody());
        $option = $this->options->getOption($opt);
        $option->value = $val;
        $option->save();
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Opción [' . $opt . '] actualizada',
            'status' => 200,
        ]);
    }

    public function getCiudadanoPadron($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $options = $this->pagination->getParams($request, [
            'matricula' => [
                'type' => 'integer',
            ],
        ]);
        $ciudadanos = $this->db->query('App:Citizen', ['subject'])->where('dni', $options['matricula'])->get();
        return $response->withJSON($ciudadanos->toArray());
    }

    public function getUsuarioCiudadano($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $options = $this->pagination->getParams($request, [
            'matricula' => [
                'type' => 'integer',
            ],
        ]);
        $ciudadano = $this->db->query('App:Citizen', ['subject'])->where('dni', $options['matricula'])->firstOrFail();
        $user = $this->helper->getUserFromSubject($ciudadano->subject->toDummy());
        $user->makeVisible(['email']);
        $ciudadanoArr = $ciudadano->toArray();
        $ciudadanoArr['user'] = $user->toArray();
        unset($ciudadanoArr['user']['subject']);
        return $response->withJSON($ciudadanoArr);
    }

    // LEGACY
    public function runParticipacionPadron($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->checkPermission($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $matricula = $this->helper->getSanitizedStr('matricula', $request->getParsedBody());
        $ciudadano = $this->db->query('App:Citizen')->where('dni', $matricula)->firstOrFail();
        $ciudadano->voted = true;
        $ciudadano->save();
        $ballot = $this->db->new('App:OnlineBallot');
        $ballot->created_at = new Carbon();
        $ballot->code = $this->helper->randomStr(10);
        $ballot->save();
        return $this->representation->returnMessage($request, $response, [
            'message' => 'Participacion guardada',
            'codigo' => $ballot->code,
            'status' => 200,
        ]);
    }

    // public function downloadPropuestas($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if (!$this->authorization->checkPermission($subject, 'admin')) {
    //         throw new UnauthorizedException();
    //     }
    //     $writer = \Box\Spout\Writer\WriterFactory::create(\Box\Spout\Common\Type::XLSX);
    //     $path = $opt.'.xlsx';
    //     if ($this->filesystem->has($path)) {
    //         $updDate = Carbon::createFromTimestamp($this->filesystem->getTimestamp($path));
    //         $expLimit = new Carbon('+ 4 hours');
    //         if ($updDate->gt($expLimit)) {
    //             return $response
    //             ->withBody(new Stream($this->filesystem->readStream($path)))
    //             ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //         } else {
    //             $this->filesystem->delete($path);
    //         }
    //     }
    //     $this->filesystem->copy('sample.xlsx', $path);
    //     $tmpHandle = $this->filesystem->readStream($path);
    //     $metaDatas = stream_get_meta_data($tmpHandle);
    //     $tmpFilename = $metaDatas['uri'];
    //     $defStyle = (new \Box\Spout\Writer\Style\StyleBuilder())->setShouldWrapText()->build();
    //     $writer->openToFile($tmpFilename);
    //     $lala = $this->db->query('App:Project', [
    //         'author'
    //         ])->get();
    // }

    // public function getDniList($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     if (!$this->authorization->checkPermission($subject, 'retDni')) {
    //         throw new UnauthorizedException();
    //     }
    //     $options = $this->pagination->getParams($request, [
    //         's' => [
    //             'type' => 'string',
    //         ],
    //     ]);
    //     $query = $this->db->query('App:User')->where('verified_dni', false);
    //     if (isset($options['s'])) {
    //         $filter = $this->helper->generateTrace($options['s']);
    //         $query->where('trace', 'LIKE', "%$filter%");
    //     }
    //     $results = new Paginator($query, $options);
    //     $results->setUri($request->getUri());
    //     return $this->pagination->renderResponse($response, $results);
    // }

    // public function postVerifiedDni($request, $response, $params)
    // {
    //     $subject = $request->getAttribute('subject');
    //     $user = $this->helper->getEntityFromId('App:User', 'usr', $params);
    //     if (!$this->authorization->checkPermission($subject, 'coordin')) {
    //         throw new UnauthorizedException();
    //     }
    //     $user->verified_dni = true;
    //     $user->save();
    //     $group = $user->groups()->first();
    //     if (isset($group)) {
    //         $countMembers = $group->users()->count();
    //         $countVerified = $group->users()->where('verified_dni', true)->count();
    //         if ($countMembers >= 5 && $countMembers == $countVerified) {
    //             $group->verified_team = true;
    //             $group->save();
    //         }
    //     }
    //     return $this->representation->returnMessage($request, $response, [
    //     'message' => 'DNI verificado',
    //     'status' => 200,
    //     ]);
    // }
}
