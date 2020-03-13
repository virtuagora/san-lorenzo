<?php
namespace App\Service;

class OptionsService
{
    private $db;
    private $settings;

    public function __construct($db)
    {
        $this->db = $db;
        $this->settings = null;
    }

    public function getAutoloaded()
    {
        if (is_null($this->settings)) {
            $this->settings = $this->db->table('options')
                ->where('autoload', true)->pluck('value', 'key');
        }
        return $this->settings->toArray();
    }

    public function getOption($option)
    {
        return $this->db->query('App:Option')->where('key', $option)->firstOrFail();
    }

    public function getOptions($filters = null)
    {
        return $this->db->query('App:Option')->get();
    }

    public function incrementOption($option, $amount)
    {
        return $this->db->table('options')->where('key', $option)->increment('value', $amount);
    }
}
