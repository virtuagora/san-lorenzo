<?php
namespace App\Util;

class DummySubject
{
    protected $id;
    protected $type;
    protected $name;
    protected $roles;
    protected $extra;

    public function __construct($type, $id = null, $name = null, $roles = [], $extra = [])
    {
        $this->type = $type;
        $this->id = $id;
        $this->name = $name;
        $this->roles = $roles;
        $this->extra = $extra;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getExtra()
    {
        return $this->extra;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'roles' => $this->roles,
            'extra' => $this->extra,
        ];
    }

    public function getRelationsWith($subject)
    {
        return (isset($this->id) && $this->getId() == $subject->getId())? ['self']: [];
    }
}
