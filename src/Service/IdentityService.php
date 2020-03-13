<?php

namespace App\Service;

use App\Util\Exception\SystemException;
use App\Util\Exception\AppException;
use Carbon\Carbon;

class IdentityService
{
    private $providers;
    private $db;

    public function __construct($providers, $db)
    {
        $this->providers = $providers;
        $this->db = $db;
    }

    private function getProvider($provider)
    {
        if (!isset($this->providers[$provider])) {
            throw new SystemException('Identity provider not found');
        }
        return $this->providers[$provider];
    }

    public function signIn($provider, $options)
    {
        $idProv = $this->getProvider($provider);
        $identifiers = $idProv->makeIdentifiers($options);
        $user = $idProv->retrieveUser($identifiers);
        if (is_null($user)) {
            $token = $idProv->makeRegistrationToken($identifiers);
            if (is_null($token)) {
                return [
                    'status' => 'not-found',
                ];
            } else {
                return [
                    'status' => 'pending-user',
                    'token' => $token,
                ];
            }
        } else {
            if (isset($user->banExpiration)) {
                if (Carbon::now()->lt($user->banExpiration)) {
                    return [
                        'status' => 'banned',
                    ];
                } else {
                    $user->banExpiration = null;
                    $user->save();
                }
            }
        }
        return [
            'status' => 'success',
            'user' => $user,
        ];
    }

    public function createPendingUser($provider, $data)
    {
        $idProv = $this->getProvider($provider);
        return $idProv->createPendingUser($data);
    }

    public function registerUser($data, $token)
    {
        $pending = $this->db->query('App:PendingUser')
            ->where('token', $token)
            ->first();
        if (is_null($pending)) {
            throw new AppException('Invalid token', 400);
        }
        $idProv = $this->getProvider($pending->provider);
        return $idProv->registerUser($data, $pending);
    }
}
