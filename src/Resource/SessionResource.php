<?php
namespace App\Resource;

use App\Util\Exception\AppException;
use Carbon\Carbon;

class SessionResource
{
    protected $userProvider;
    protected $frontDesk;

    public function __construct($userProvider, $frontDesk)
    {
        $this->userProvider = $userProvider;
        $this->frontDesk = $frontDesk;
    }

    public function createOne($subject, $data)
    {
        $type = $data['type']; // TODO controlar mejor esto
        $result = $this->userProvider->getUser($data, $type);
        if (!isset($result['user'])) {
            // TODO enviar exception especial sobre login
            throw new AppException($result['message'], 400);
        }
        $user = $result['user'];
        if (isset($user->banExpiration)) {
            if (Carbon::now()->lt($user->banExpiration)) {
                // TODO enviar exception especial sobre login
                throw new AppException('Su cuenta se encuentra suspendida', 400);
            } else {
                $user->banExpiration = null;
                $user->save();
            }
        }
        $subject = $user->subject->toSubject();
        $session = $this->frontDesk->signIn($subject);
        return $session;
    }
}
