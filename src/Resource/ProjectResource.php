<?php

namespace App\Resource;

use App\Model\Project;
use App\Util\Exception\AppException;
use App\Util\Paginator;
use App\Util\Utils;
use App\Util\ContainerClient;
use Carbon\Carbon;

class ProjectResource extends ContainerClient
{
	public function retrieveSchema($options = [])
	{
		$schema = [
			'type' => 'object',
			'properties' => [
				'name' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 250,
				],
				'type' => [
					'type' => 'string',
					'enum' => ['institucional', 'comunitario'],
				],
				'slug' => [
					'type' => 'string',
					'pattern' => '^[a-z0-9]+(?:-[a-z0-9]+)*$',
					'maxLength' => 250,
				],
				'objective' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 2000,
				],
				'edition' => [
					'type' => 'number',
					'minimum' => 2010,
				],
				'description' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 2000,
				],
				'benefited_population' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 2000,
				],
				'community_contributions' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 2000,
				],
				'budget' => [
					'type' => 'array',
					'items' => [
						'type' => 'object',
						'properties' => [
							'description' => [
								'type' => 'string',
								'minLength' => 1,
								'maxLength' => 350,
							],
							'amount' => [
								'type' => 'number',
								'minimum' => 1,
							],
						],
						'required' => ['description', 'amount'],
						'additionalProperties' => false,
					],
				],
				'author_names' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 100,
				],
				'author_surnames' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 100,
				],
				'author_phone' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 100,
				],
				'author_dni' => [
					'type' => 'string',
					'minLength' => 2,
					'maxLength' => 20,
				],
				'author_email' => [
					'type' => 'string',
					'format' => 'email',
					'minLength' => 5,
					'maxLength' => 100,
				],
				'organization_name' => [
					'type' => 'string',
					'minLength' => 2,
					'maxLength' => 250,
				],
				'organization_legal_entity' => [
					'type' => 'string',
					'minLength' => 2,
					'maxLength' => 250,
				],
				'organization_address' => [
					'type' => 'string',
					'minLength' => 2,
					'maxLength' => 250,
				],
				'organization_cuit' => [
					'type' => 'string',
					'minLength' => 2,
					'maxLength' => 250,
				],
				'organization_nro_personeria' => [
					'type' => 'string',
					'minLength' => 2,
					'maxLength' => 250,
				],
				'monitoringStatus' => [
					'type' => 'string',
					'enum' => ['pendiente', 'en-ejecucion', 'no-ejecutado','ejecutado']
				],
        'monitoringComment' => [
					'type' => 'string',
					'minLength' => 2,
					'maxLength' => 2000,
				],
				'district_id' => [
					'type' => 'integer',
					'minimum' => 1,
				],
			],
			'required' => [
				'author_dni',
				'author_email',
				'author_names',
				'author_surnames',
				'author_phone',
				'benefited_population',
				'budget',
				'community_contributions',
				'description',
				'district_id',
				'name',
				'objective',
				'type',
			],
			'additionalProperties' => false,
		];
		if (isset($options['includeCode'])) {
			$schema['properties']['code'] = [
				'type' => 'string',
				'minLength' => 1,
				'maxLength' => 10,
			];
		}
		return $schema;
	}

	// LEGACY
	public function retrieveBySlug($slug)
	{
		return $this->db->query('App:Project')
			->where('slug', $slug)
			->firstOrFail();
	}

	public function retrieveOne($id)
	{
		return $this->db->query('App:Project')
			->where('id', $id)
			->firstOrFail();
	}

	public function retrieveMany($options, $random = false)
	{
		$query = $this->db->query('App:Project');
		if (isset($options['district'])) {
			$query->where('district_id', $options['district']);
		}
		if (isset($options['author'])) {
			$query->where('author_id', $options['author']);
		}
		if (isset($options['type'])) {
			$query->where('type', $options['type']);
		}
		if (isset($options['selected'])) {
			$query->where('selected', true);
			$query->where('feasible', true);
		} elseif (isset($options['feasible'])) {
			$query->where('feasible', $options['feasible']);
		} elseif (isset($options['proposals'])) {
			$query->whereNull('feasible');
			$query->where('selected', false);
		}
		if (isset($options['s'])) {
			$filter = $this->helper->generateTrace($options['s']);
			$query->where('trace', 'LIKE', "%$filter%");
		}
		if (isset($options['random']) && $options['random']) {
			$query->inRandomOrder();
		}
		$results = new Paginator($query, $options);
		return $results;
	}

	// LEGACY
	public function retrieveRandom($options = null)
	{
		// $query = $this->db->query('App:Project', ['group']);
		// $query->where('has_image', '=', "1");

		// $results = new Paginator($query, $options);
		$query = Project::inRandomOrder();
		if (isset($options['district'])) {
			$query->where('district_id', $options['district']);
		}
		if (isset($options['type'])) {
			$query->where('type', $options['type']);
		}
		if (isset($options['selected'])) {
			$query->where('selected', true);
		}
		if (isset($options['size'])) {
			$query->limit($options['size']);
		} else {
			$query->limit(10);
		}
		$results = $query->get();
		return $results;
	}

	// LEGACY
	public function retrieveComments($proId, $options)
	{
		$query = $this->db->query('App:Comment')->where('project_id', $proId);
		if (isset($options['usr'])) {
			$query->where('author_id', $options['usr']);
		}
		$results = new Paginator($query, $options);
		return $results;
	}

	public function createOne($subject, $data)
	{
		$project = $this->db->newInstance('App:Project');
		$user = $this->helper->getUserFromSubject($subject);
		if ($this->authorization->hasRoles($subject, 'admin')) {
			$schemaOpts = ['includeCode' => true];
		} else {
			$available = $this->options->getOption('proposals-available')->value;
			if (!$available) {
				throw new AppException('El formulario no se encuentra disponible');
			}
			$launch = $this->options->getOption('proposals-launch')->value;
			$deadline = $this->options->getOption('proposals-deadline')->value;
      $today = Carbon::now();
			if (!$today->between($launch, $deadline)) {
				throw new AppException('Fecha inválida');
			}
			$project->author()->associate($user);
			$schemaOpts = [];
		}
		$data['edition'] = $this->options->getOption('current-edition')->value;
		$this->fillProjectData($project, $data, $schemaOpts);
		// First version is not 
		//$this->updateJournal($user, $project);
		$project->save();
		return $project;
	}

	public function updateOne($subject, $project, $data)
	{
		$user = $this->helper->getUserFromSubject($subject);
		if ($this->authorization->hasRoles($subject, 'admin')) {
			$schemaOpts = ['includeCode' => true];
		} else {
			$available = $this->options->getOption('proposals-available')->value;
			if (!$available) {
				throw new AppException('El formulario no se encuentra disponible');
			}
			$launch = $this->options->getOption('proposals-launch')->value;
			$deadline = $this->options->getOption('proposals-deadline')->value;
      $today = Carbon::now();
			if (!$today->between($launch, $deadline)) {
				throw new AppException('Fecha inválida');
			}
			$schemaOpts = [];
		}
		if ($this->detectChanges($project, $data)) {
			$this->updateJournal($user, $project);
		}
		$this->fillProjectData($project, $data, $schemaOpts);
		$project->save();
		return $project;
	}

	private function fillProjectData($project, $data, $schemaOpt = [])
	{
		$schema = $this->retrieveSchema($schemaOpt);
		$v = $this->validation->fromSchema($schema);
		$v->assert($this->validation->prepareData($schema, $data, true));
		$distrito = $this->db->query('App:District')->findOrFail($data['district_id']);
		$duplicadosName = $this->helper->getDuplicatedFields(
			'App:Project', $project, ['name']
		);
		if (!empty($duplicadosName)) {
			throw new AppException('Ya existe un proyecto con el mismo nombre');
		}
		$totalBudget = 0;
		foreach ($data['budget'] as $item) {
			$totalBudget += $item['amount'];
		}
		if($data['type'] == 'comunitario'){
			$limit = $this->options->getOption('budget-comunitario-limit')->value;
		} else if($data['type'] == 'institucional') {
			$limit = $this->options->getOption('budget-institucional-limit')->value;
		} else {
			$limit = $this->options->getOption('budget-comunitario-limit')->value;
		}
		if ($totalBudget > $limit) {
			//tooMuchBudget
			throw new AppException('El proyecto supera el limite del presupuesto');
		}
		$project->fill($data);
		$project->trace = $this->helper->generateTrace($data['name']);
		$project->total_budget = $totalBudget;
		$project->public = true;
		return $project;
	}

	private function updateJournal($user, $project) {
		$fields = $this->getWatchedFields();
		$newPage = Utils::arrayWhiteList($project->toArray(), $fields);
		$newPage['author_id'] = $user->id;
		$journal = $project->journal ?? [];
		$journal[time()] = $newPage;
		$project->journal = $journal;
		return $project;
	}

	private function detectChanges($project, $data) {
		$watchedFields = $this->getWatchedFields();
		$newData = Utils::arrayWhiteList($project->toArray(), $watchedFields);
		$oldData = Utils::arrayWhiteList($data, $watchedFields);
		ksort($newData);
		ksort($oldData);
		$newTrace = md5(json_encode($newData));
		$oldTrace = md5(json_encode($oldData));
		return $oldTrace != $newTrace;
	}

	private function getWatchedFields() {
		return $watchedFields = [
			'edition',
			'benefited_population', 'budget', 'community_contributions',
			'description', 'name', 'objective', 'type'
		];
	}

	public function updatePicture($subject, $project, $imgFile)
	{
		if ($imgFile->getError() == UPLOAD_ERR_NO_FILE) {
			throw new AppException('No se envió archivo');
		} elseif ($imgFile->getError() !== UPLOAD_ERR_OK) {
			throw new AppException(
				'Hubo un error con el archivo recibido (' . $imgFile->getError() . ')'
			);
		}
		$fileMime = $imgFile->getClientMediaType();
		$allowedMimes = [
			'image/jpeg' => 'jpg',
			'image/pjpeg' => 'jpg',
			'image/png' => 'png',
			'image/gif' => 'gif',
		];
		if (!isset($allowedMimes[$fileMime])) {
			throw new AppException('Tipo de archivo inválido');
		}
		$imgStrm = $this->image->make($imgFile->getStream()->detach())
			->resize(1200, 800, function ($constraint) {
				$constraint->aspectRatio();
				$constraint->upsize();
			})->encode('jpg', 90);
		$imgName = $project->id . '.jpg';
		$this->filesystem->put('project/' . $imgName, $imgStrm);
		if (is_resource($imgStrm)) {
			fclose($imgStrm);
		}
		$project->picture_name = $imgName;
		$project->save();
	}

	public function updateFeasibility($subject, $project, $data)
	{
		$schema = [
			'type' => 'object',
			'properties' => [
				'code' => [
					'oneOf'=> [
						[
							'type' => 'string',
							'minLength' => 1,
							'maxLength' => 10,
						],
						[
							'type' => 'null'
						]
					]
				],
				'feasibility' => [
					'oneOf'=> [
						[
							'type' => 'string',
							'minLength' => 1,
							'maxLength' => 2000,
						],
						[
							'type' => 'null'
						]
					]
				],
				'feasible' => [
					'oneOf'=> [
						[
							'type' => 'boolean'
						],
						[
							'type' => 'null'
						]
					]
				],
			],
			'additionalProperties' => false,
		];
		$v = $this->validation->fromSchema($schema);
		$data = $this->validation->prepareData($schema['properties'], $data);
		$v->assert($data);
		// Mostrar si el codigo ya está en uso
		$project->code = $data['code'] ?? null;
		if( !is_null($data['code']) ){
			$duplicadosCode = $this->helper->getDuplicatedFields(
				'App:Project', $project, ['code']
			);
			if (!empty($duplicadosCode)) {
				throw new AppException('Ya existe un proyecto con el mismo código');
			}
		}
		// Completar los demas datos
		$project->feasibility = $data['feasibility'];
		$project->feasible = $data['feasible'] ?? null;
		$project->save();
		return $project;
	}

	public function updateSelected($subject, $project, $data)
	{
		$schema = [
			'type' => 'object',
			'properties' => [
				'selected' => [
					'type' => 'boolean',
				],
			],
			'additionalProperties' => false,
			'required' => ['selected'],
		];
		$v = $this->validation->fromSchema($schema);
		$data = $this->validation->prepareData($schema['properties'], $data);
		$v->assert($data);
		$project->selected = $data['selected'];
		$project->save();
		return $project;
	}

	public function updateNotes($subject, $project, $data)
	{
		$schema = [
			'type' => 'object',
			'properties' => [
				'notes' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 2000,
				],
			],
			'additionalProperties' => false,
			'required' => ['notes'],
		];
		$v = $this->validation->fromSchema($schema);
		$data = $this->validation->prepareData($schema['properties'], $data, true);
		$v->assert($data);
		$project->notes = $data['notes'];
		$project->save();
		return $project;
	}

	public function updateAuthor($subject, $project, $data)
	{
		$schema = [
			'type' => 'object',
			'properties' => [
				'author_id' => [
					'type' => 'integer',
					'minimum' => 1,
				]
			],
			'additionalProperties' => false,
		];
		$v = $this->validation->fromSchema($schema);
		$data = $this->validation->prepareData($schema['properties'], $data, true);
		$v->assert($data);
		if (isset($data['author_id'])) {
			$autor = $this->db->query('App:User')->findOrFail($data['author_id']);
			$project->author()->associate($autor);
		} else {
			$project->author()->dissociate();
		}
		$project->save();
		return $project;
	}

	public function retrieveDocuments($project)
	{
		return $project->documents;
	}

	public function createDocument($subject, $project, $data, $file)
    {
        if ($file->getError() === UPLOAD_ERR_INI_SIZE || $file->getError() === UPLOAD_ERR_FORM_SIZE) {
            throw new AppException('El archivo excede el límite de tamaño permitido');
        } elseif ($file->getError() !== UPLOAD_ERR_OK) {
            throw new AppException('Hubo un error con el archivo recibido. Código ' . $file->getError());
        }
        $fileMime = $file->getClientMediaType();
        $allowedMimes = [
            'application/pdf' => 'pdf',
            'invalid/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'image/jpeg' => 'jpg',
            'image/pjpeg' => 'jpg',
            'image/png' => 'png',
        ];
        if (!isset($allowedMimes[$fileMime])) {
            throw new AppException('Tipo de documento inválido');
		}
		$schema = [
			'type' => 'object',
			'properties' => [
				'name' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 250,
				],
				'description' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 2000,
				],
			],
			'additionalProperties' => false,
			'required' => ['name'],
		];
		$v = $this->validation->fromSchema($schema);
		$data = $this->validation->prepareData($schema['properties'], $data);
		$v->assert($data);
        $document = $this->db->newInstance('App:Document');
        $document->fill($data);
        $document->project()->associate($project);
        $document->save();
        $document->filename = 'document_'. $document->id . '.' . $allowedMimes[$fileMime];
        $document->save();
        $fileStrm = $file->getStream()->detach();
        $this->filesystem->putStream(
            'documents/' . $document->filename,
            $fileStrm
        );
        return $document;
	}
	
	public function getDocumentFile($document)
    {
        if (!isset($document->filename)) {
            throw new AppException('No posee documento cargado', 404);
        }
        $path = 'documents/' . $document->filename;
        if (!$this->filesystem->has($path)) {
            throw new AppException('El documento no se encuentra almacenado', 404);
        }
        $mime = $this->filesystem->getMimetype($path);
        $strm = $this->filesystem->readStream($path);
        return [
            'strm' => $strm,
            'mime' => $mime,
        ];
    }

    public function deleteDocument($subject, $document)
    {
        $document->delete();
    }

	// TODO
	// controlar logica ¿user puede borrar una vez que el project es factible?
	public function deleteOne($subject, $project)
	{
		$project->delete();
	}

	// public function vote($subject, $project)
	// {
	//     $user = $this->helper->getUserFromSubject($subject);
	//     $result = $project->voters()->toggle($user->id);
	//     $project->likes = $project->voters()->count();
	//     $project->save();
	//     $vote = empty($result['detached']);
	//     if ($vote) {
	//         $this->options->incrementOption('stat-votes', 1);
	//     } else {
	//         $this->options->incrementOption('stat-votes', -1);
	//     }
	//     return $vote;
	// }

	// LEGACY
	public function createComment($subject, $project, $data)
	{
		$user = $this->helper->getUserFromSubject($subject);
		$schema = [
			'type' => 'object',
			'properties' => [
				'content' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 500,
				],
			],
			'required' => ['content'],
			'additionalProperties' => false,
		];
		$v = $this->validation->fromSchema($schema);
		$v->assert($data);
		$comment = $this->db->new('App:Comment');
		$comment->author_id = $user->id;
		$comment->project_id = $project->id;
		$comment->content = $data['content']; // TODO escapar emojies
		$comment->save();
		$this->options->incrementOption('stat-comments', 1);
		return $comment;
	}

	// LEGACY
	public function createReply($subject, $comment, $data)
	{
		$user = $this->helper->getUserFromSubject($subject);
		$schema = [
			'type' => 'object',
			'properties' => [
				'content' => [
					'type' => 'string',
					'minLength' => 1,
					'maxLength' => 500,
				],
			],
			'required' => ['content'],
			'additionalProperties' => false,
		];
		$v = $this->validation->fromSchema($schema);
		$v->assert($data);
		if ($comment->parent != null) {
			$comment = $comment->parent;
		}
		$reply = $this->db->new('App:Comment');
		$reply->author_id = $user->id;
		$reply->parent_id = $comment->id;
		$reply->content = $data['content']; // TODO escapar emojies
		$reply->save();
		$this->options->incrementOption('stat-comments', 1);
		return $reply;
	}

	// LEGACY
	public function voteComment($subject, $comment, $data)
	{
		$user = $this->helper->getUserFromSubject($subject);
		$schema = [
			'type' => 'object',
			'properties' => [
				'value' => [
					'type' => 'integer',
					'enum' => [-1, 1],
				],
			],
			'required' => ['value'],
			'additionalProperties' => false,
		];
		$v = $this->validation->fromSchema($schema);
		$v->assert($data);
		if ($comment->raters()->where('user_id', $user->id)->exists()) {
			$comment->raters()->updateExistingPivot($user->id, ['value' => $data['value']]);
		} else {
			$comment->raters()->attach($user->id, ['value' => $data['value']]);
		}
		$comment->votes = $comment->raters->sum('pivot.value');
		$comment->save();
		return $comment->votes;
	}
}
