<?php

namespace App\Action;

use App\Util\ContainerClient;
use App\Util\Utils;
use App\Util\Exception\AppException;
use App\Util\Exception\UnauthorizedException;
use Carbon\Carbon;

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

      public function printOne($request, $response, $params)
    {
        // $subject = $request->getAttribute('subject');
        // if (!$this->authorization->checkPermission($subject, 'user')) {
        //     throw new UnauthorizedException();
        // }
        $project = $this->helper->getEntityFromId(
            'App:Project', 'pro', $params, ['author']
        );

        $logo_pp = file_get_contents(__DIR__ . '/../../public/assets/img/pdf/pp-logo-email.png');
        $logo_sl = file_get_contents(__DIR__ . '/../../public/assets/img/pdf/logo-sl-email.png');
        
        // Encode the image string data into base64
        $data_logo_pp = base64_encode($logo_pp);
        $data_logo_sl = base64_encode($logo_sl);

        $dd = [
            'pageSize' => 'A4',
            'info' => [
                'title' => $project->name,
                'author' => 'Presupuesto Participativo del Municipio de San Lorenzo'
            ],
            'content' => [
                [

                    'columns' => [
                        [
                            'alignment' => 'left',
                            'image' => 'data:image/png;base64,'.$data_logo_sl,
                            'fit' => [110,110]
                        ],
                        [
                            'alignment' => 'center',
                            'image' => 'data:image/png;base64,'.$data_logo_pp,
                            'fit' => [40,40]
                        ],
                        [
                            'alignment' => 'right',
                            'qr' => $this->helper->pathFor('showProject', true, ['pro' => $project->id], []),
                            'fit' => 70
                        ]
                    ],
                ],
                [
                    'text' => 'Fecha impresión: ' . Carbon::parse(null),
                    'style' => 'small'
                ],
                [
                    'text' => 'Fecha creación: ' . Carbon::parse($project->created_at),
                    'style' => 'small'
                ],
                 [
                    'text' => 'Fecha última actualización: ' . Carbon::parse($project->updated_at),
                    'style' => 'small'
                ],
                [
                    'text' => $project->name,
                    'style' => 'theTitle'
                ]
            ],
        ];
        if($project->type == 'institucional'){
            array_push($dd['content'], [
                'text' => "Presenta: {$project->organization_name} ",
                'style' => 'theSubtitle'
            ]);
        } else {
            array_push($dd['content'], [
                'text' => "Presenta: {$project->author_surnames}, {$project->author_names}",
                'style' => 'theSubtitle'
            ]);
        }
        array_push($dd['content'],[
            'style' => 'tableExample',
            'table' => [
                'headerRows' => 1,
                'widths' => ['*','*','*','*','*'],
                'body' => [
                    [

                        ['text' => 'Edición', 'style' => 'tableHeader'],
                        ['text' => 'Código', 'style' => 'tableHeader'],
                        ['text' => 'Distrito', 'style' => 'tableHeader'],
                        ['text' => 'Categoría', 'style' => 'tableHeader'],
                        ['text' => 'Presupuesto', 'style' => 'tableHeader'],
                    ],
                    [
                        $project->edition,
                        isset($project->code) ? $project->code : 'No asignado',
                        $project->district->name,
                        $project->type,
                        // str_replace('.00','',$project->total_budget)
                        '$ ' . number_format($project->total_budget,2,',','.')
                    ]
                ]
            ]
        ]);
        if($project->type == 'institucional'){
            array_push($dd['content'],[
                'style' => 'tableExample',
                'table' => [
                    'headerRows' => 1,
                    'widths' => ['*','*'],
                    'body' => [
                        [

                            ['text' => 'Organización', 'style' => 'tableHeader'],
                            ['text' => 'Entidad Legal', 'style' => 'tableHeader']
                        ],
                        [
                            $project->organization_name,
                            $project->organization_legal_entity
                        ]
                    ]
                ]
            ]);
            array_push($dd['content'],[
                'style' => 'tableExample',
                'table' => [
                    'headerRows' => 1,
                    'widths' => ['*','*'],
                    'body' => [
                        [

                            ['text' => 'Dirección', 'style' => 'tableHeader'],
                            ['text' => 'Representante', 'style' => 'tableHeader']
                        ],
                        [
                            $project->organization_address,
                            "{$project->author_surnames}, {$project->author_names}"
                        ]
                    ]
                ]
            ]);
            array_push($dd['content'],[
                'style' => 'tableExample',
                'table' => [
                    'headerRows' => 1,
                    'widths' => ['*','*'],
                    'body' => [
                        [

                            ['text' => 'CUIT', 'style' => 'tableHeader'],
                            ['text' => 'Nro. Personeria Juridica', 'style' => 'tableHeader']
                        ],
                        [
                            $project->organization_cuit,
                            $project->organization_nro_personeria
                        ]
                    ]
                ]
            ]);
        }
        $auxFactibilidad = [
            'style' => 'tableExample',
            'table' => [
                'headerRows' => 1,
                'widths' => ['*'],
                'body' => [
                    [

                        ['text' => 'Factibilidad', 'style' => 'tableHeader'],
                    ]
                ]
            ]
        ];
        $appState = $this->options->getOption('current-state')->value;
        if(($appState == 'pre-vote' || $appState == 'vote' || $appState == 'pre-results') && $project->feasible){
            array_push($auxFactibilidad['table']['body'],["El proyecto ES FACTIBLE para participar de la votación del Presupuesto del año {$proyect->edition}"]);
        } elseif (($appState == 'pre-vote' || $appState == 'vote' || $appState == 'pre-results') && !$project->feasible) {
            array_push($auxFactibilidad['table']['body'],["El proyecto NO ES FACTIBLE para participar de la votación del Presupuesto del año {$proyect->edition}"]);
        } elseif ($appState == 'results' && !$project->feasible) {
            array_push($auxFactibilidad['table']['body'],["El proyecto NO FUE FACTIBLE para participar de la votación del Presupuesto del año {$proyect->edition}"]);
        } elseif ($appState == 'results' && $project->feasible && $project->selected == false) {
            array_push($auxFactibilidad['table']['body'],["El proyecto FUE FACTIBLE Y PARTICIPÓ de la votación del Presupuesto del año {$proyect->edition}"]);
        } elseif ($appState == 'results' && $project->feasible && $project->selected == true) {
            array_push($auxFactibilidad['table']['body'],["El proyecto FUE FACTIBLE Y FUE SELECCIONADO por el voto de los ciudadanos en el presupuesto participativo del año {$proyect->edition}"]);
        } else {
            array_push($auxFactibilidad['table']['body'],['Pendiente de revisión']);
        }
        array_push($dd['content'],$auxFactibilidad);
        array_push($dd['content'], [
            'text' => "El objetivo del proyecto",
            'style' => 'header'
        ]);
        array_push($dd['content'], [
            'text' => $project->objective,
            'style' => 'theText'
        ]);
        array_push($dd['content'], [
            'text' => "La problemática que aborda",
            'style' => 'header'
        ]);
        array_push($dd['content'], [
            'text' => $project->description,
            'style' => 'theText'
        ]);
        array_push($dd['content'], [
            'text' => "Poblacion beneficiada",
            'style' => 'header'
        ]);
        array_push($dd['content'], [
            'text' => $project->benefited_population,
            'style' => 'theText'
        ]);
        array_push($dd['content'], [
            'text' => "Aportes comunitarios",
            'style' => 'header'
        ]);
        array_push($dd['content'], [
            'text' => $project->community_contributions,
            'style' => 'theText'
        ]);
        array_push($dd['content'], [
            'text' => "Presupuesto",
            'style' => 'header'
        ]);
        $presupuesto =  [
                'style' => 'tableExample',
                'table' => [
                    'headerRows' => 1,
                    'widths' => ['*','auto'],
                    'body' => [
                        [

                            ['text' => 'Descripción', 'style' => 'tableHeader'],
                            ['text' => 'Monto', 'style' => 'tableHeader']
                        ]
                    ]
                ]
            ];
        foreach($project->budget as $item) {
            array_push($presupuesto['table']['body'],[
                $item['description'],
               '$ ' . number_format($item['amount'],2,',','.')
            ]);
        }
        array_push($dd['content'],$presupuesto);

        $styles = [
            'styles' => [
                'theTitle' => [
                    'fontSize' => 28,
                    'bold' => true,
                    'margin' => [0, 20, 0, 5]
                ],
                'theSubtitle' => [
                    'fontSize' => 18,
                    'bold' => true,
                    'margin' => [0, 0, 0, 30]
                ],
                'header' => [
                    'fontSize' => 16,
                    'bold' => true,
                    'margin' => [0, 20, 0, 10]
                ],
                'subheader' => [
                    'fontSize' => 14,
                    'bold' => true
                ],
                'tableExample' => [
                    'margin' => [0, 5, 0, 15]
                ],
                'tableHeader' => [
                    'bold' => true,
                    'fontSize' => 12,
                    'color' => 'black'
                ],
                'theText' => [
                    'fontSize' => 12,
                ],
                'quote' => [
                    'italics' => true
                ],
                'small' => [
                    'fontSize' => 8
                ]
            ]
        ];
        $dd = array_merge($dd, $styles);
        $filename = "{$project->name}. PPSL {$project->edition}";
        $filename = str_replace(" ","_",$filename);
        $filename = str_replace(".","",$filename);
        $filename = strtolower($filename);
        $filename = mb_ereg_replace("[^\w\s\d\-_~,;\[\]\(\).]", '', $filename);
        // Remove any runs of periods (thanks falstro!)
        $filename = mb_ereg_replace("[\.]{2,}", '', $filename);
        return $response->withJSON([
            'filename' => $filename,
            'documentDefinition' => $dd
        ]);
    }
}