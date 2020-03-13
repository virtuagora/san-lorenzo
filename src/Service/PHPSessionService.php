<?php

namespace App\Service;

use App\Util\DummySubject;
use Psr\Http\Message\ServerRequestInterface as Request;

class PHPSessionService
{
    private $store;

    public function __construct($store)
    {
        $this->store = $store;
    }

    public function authenticate(Request $request)
    {
        if ($this->store->exists('subject')) {
            return new DummySubject(
                $this->store['subject']['type'],
                $this->store['subject']['id'],
                $this->store['subject']['name'],
                $this->store['subject']['roles'],
                $this->store['subject']['extra']
            );
        } else {
            return new DummySubject('Annonymous');
        }
    }

    public function signIn(DummySubject $subject)
    {
        $this->store->set('subject', $subject->toArray());
        return [
            'type' => 'php-session',
            'subject' => $this->store->get('subject'),
        ];
    }

    public function signOut()
    {
        $this->store->delete('subject');
    }
}
