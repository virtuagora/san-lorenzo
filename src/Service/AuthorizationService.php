<?php

namespace App\Service;

use App\Util\Exception\UnauthorizedException;

class AuthorizationService
{
    protected $db;
    protected $logger;

    public function __construct($db, $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
    }

    public function hasRoles($subject, $roles) {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        $subRoles = $subject->getRoles();
        return array_intersect($subRoles, $roles);
    }

    public function checkPermission($subject, $actName, $object = null, $proxy = null)
    {
        $action = $this->db->query('App:Action')->find($actName);
        if (is_null($action)) {
            return false;
        }
        // $this->logger->info(json_encode([$subject->toArray(), $action->allowed_roles]));
        $subRoles = $subject->getRoles();
        if (array_intersect($subRoles, $action->allowed_roles)) {
            return true;
        }
        if (isset($object)) {
            // $this->logger->info(json_encode([$object->getRelationsWith($subject), $action->allowed_relations]));
            $objRelations = $object->getRelationsWith($subject);
            return array_intersect($objRelations, $action->allowed_relations);
        }
        //TODO verificar proxies
        return false;
    }

    public function checkOrFail($subject, $action, $object = null, $proxy = null)
    {
        if (!$this->check($subject, $action, $object, $proxy)) {
            throw new UnauthorizedException();
        }
    }
}