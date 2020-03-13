<?php

namespace App\Util\IdentityProvider;
use App\Util\Exception\AppException;
use Carbon\Carbon;

class LocalIdentityProvider
{
    protected $db;
    protected $validation;

    public function __construct($db, $validation)
    {
        $this->db = $db;
        $this->validation = $validation;
    }

    public function makeIdentifiers($options)
    {
        $v = $this->validation->fromSchema([
            'email' => [
                'type' => 'string',
                'format' => 'email',
            ],
            'password' => [
                'type' => 'string',
                'minLength' => 1,
                'maxLength' => 250,
            ],
        ]);
        $v->assert($options);
        return [
            'email' => $options['email'],
            'password' => $options['password'],
        ];
    }

    public function retrieveUser($data)
    {
        $user = $this->db->query('App:User')->where('email', $data['email'])->first();
        if (isset($user) && password_verify($data['password'], $user->password)) {
            return $user;
        } else {
            return null;
        }
    }

    public function makeRegistrationToken($data)
    {
        return null;
    }

    public function createPendingUser($data)
    {
        $v = $this->validation->fromSchema([
            'type' => 'object',
            'properties' => [
                'identifier' => [
                    'type' => 'string',
                    'format' => 'email',
                ],
            ],
            'required' => ['identifier'],
            'additionalProperties' => false,
        ]);
        $v->assert($data);
        $user = $this->db->query('App:User')
            ->where('email', $data['identifier'])
            ->first();
        if (isset($user)) {
            throw new AppException('Ya existe un usuario registrado con este email');
        }
        $pending = $this->db->query('App:PendingUser')->firstOrNew([
            'provider' => 'local',
            'identifier' => $data['identifier'],
        ]);
        $pending->token = bin2hex(random_bytes(10));
        $pending->save();
        return $pending;
    }

    public function registerUser($data, $pending)
    {
        $roles = ['user'];
        $anotherUser = $this->db->query('App:User')->where(
            'dni', $data['dni']
        )->first();
        if(isset($anotherUser)){
            throw new AppException('Ya existe un usuario con el mismo DNI. Por favor, revise el DNI con el que se está registrando.');            
        }
        $citz = $this->db->query('App:Citizen')->where(
            'dni', $data['dni']
        )->first();
        // $subjectAlreadyAssigned = $this->db->query('App:Subject')->where(
        //     'citizen_id', $citz->id
        // )->first();
        // if(isset($anotherUser)){
        //     throw new AppException('Ya existe un usuario con el mismo DNI. Por favor, revise el DNI con el que se está registrando.');
        // }
        $neig = $this->db->query('App:Neighbourhood')
            ->findOrFail($data['neighbourhood_id']);
        $subj = $this->db->new('App:Subject');
        $subj->display_name = $data['names'] . ' ' . $data['surnames'];
        $subj->img_type = 0;
        $subj->img_hash = md5($pending->identifier);
        $subj->type = 'User';
        $subj->neighbourhood()->associate($neig);
        if (isset($citz)) {
            $subj->citizen()->associate($citz);
            $roles[] = 'verified';
        }
        $subj->save();
        $user = $this->db->new('App:User');
        $user->email = $pending->identifier;
        $user->password = $data['password'];
        $user->names = $data['names'];
        $user->dni = $data['dni'];
        $user->surnames = $data['surnames'];
        $user->birthday = Carbon::parse($data['birthday']);
        $user->subject()->associate($subj);
        $user->save();
        $pending->delete();
        $subj->roles()->sync($roles);
        return $user;
    }
}
