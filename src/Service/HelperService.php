<?php

namespace App\Service;

use App\Util\DummySubject;

class HelperService
{
    private $db;
    private $logger;
    
    public function __construct($db, $router, $request, $logger)
    {
        $this->db = $db;
        $this->logger = $logger;
        $this->router = $router;
        $this->request = $request;
    }

    public function baseUrl($request = null)
    {
        if (is_null($request)) {
            $request = $this->request;
        }
        return $request->getUri()->getBaseUrl();
    }

    public function pathFor($name, $full = false, $params = [], $query = [])
    {
        if ($full) {
            return $this->baseUrl().$this->router->pathFor($name, $params, $query);
        } else {
            return $this->router->pathFor($name, $params, $query);
        }
    }

    public function getUserFromSubject(DummySubject $subject, $with = null)
    {
        return $this->db->query('App:User', $with)
            ->where('subject_id', $subject->getId())
            ->firstOrFail();
    }

    public function getCitizenFromSubject(DummySubject $subject, $with = null)
    {
        // $theRealSubject = $this->db->query('App:Subject', $with)
        //     ->where('id', $subject->getId())
        //     ->firstOrFail();
        // return $this->db->query('App:Citizen', $with)
        //     ->where('id', $theRealSubject->citizen_id)
        //     ->firstOrFail();
        $theRealSubject = $this->db->query('App:Subject', ['citizen'])
            ->where('id', $subject->getId())
            ->firstOrFail();
        return $theRealSubject->citizen;
    }

    public function getEntityFromId($model, $key, $params = null, $with = null)
    {
        $id = isset($params)? $this->getSanitizedId($key, $params): $key;
        return $this->db->query($model, $with)->findOrFail($id);
    }

    public function getSanitizedId($attr, $params)
    {
        $isDigit = ctype_digit($params[$attr] ?? 'x');
        return $isDigit ? $params[$attr] : -1;
    }

    public function getSanitizedStr($attr, $params, $default = null)
    {
        // TODO hacer validacion de verdad
        return $params[$attr] ?? $default;
    }

    public function generateTrace($str)
    {
        return preg_replace('/[^[:alnum:]]/ui', '', $str);
    }

    public function getDuplicatedFields($model, $instance, $fields)
    {
        $dupFields = [];
        $qry = $this->db->query($model);
        if ($instance->exists) {
            $qry = $qry->where('id', '!=', $instance->id);
        }
        $queryFields = array_intersect_key($instance->toArray(), array_flip($fields));
        $qry = $qry->where(function ($q) use ($queryFields) {
            $q->where($queryFields, null, 'or');
        });
        // $this->logger->info('');
        $dupli = $qry->first();
        if (isset($dupli)) {
            foreach($fields as $field) {
                if ($instance->getAttribute($field) == $dupli->getAttribute($field)) {
                    $dupFields[] = $field;
                }
            };
        }
        return $dupFields;
    }

    public function castFromJson($schema, $data, $deleteNulls = false)
    {
        foreach ($schema['properties'] as $prop => $rules) {
            if (array_key_exists($prop, $data)) {
                if (is_null($data[$prop])) {
                    if ($deleteNulls) {
                        if (array_key_exists('default', $rules)) {
                            $data[$prop] = $rules['default'];
                        } else {
                            unset($data[$prop]);
                        }
                    }
                } elseif (array_key_exists('type', $rules)) {
                    switch ($rules['type']) {
                        case 'integer':
                            $data[$prop] = (int) $data[$prop];
                            break;
                        case 'number':
                            $data[$prop] = (float) $data[$prop];
                            break;
                        case 'string':
                            $data[$prop] = (string) $data[$prop];
                            break;
                        case 'boolean':
                            $data[$prop] = (bool) $data[$prop];
                            break;
                    }
                }
            } elseif (array_key_exists('default', $rules)) {
                $data[$prop] = $rules['default'];
            }
        }
        return $data;
    }

    function randomStr($length, $keyspace = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ')
    {
        $str = '';
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keyspace[random_int(0, $max)];
        }
        return $str;
    }
}
