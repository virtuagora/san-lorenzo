<?php

namespace App\Util;

use App\Util\DummySubject;
use Twig_Extension;
use Twig_SimpleFunction;

class TwigExtension extends Twig_Extension
{
    private $router;
    private $request;
    private $uri;
    private $options;
    private $logger;
    private $helper;
    private $store;

    public function __construct($router, $request, $options, $logger, $helper, $store, $uri = null)
    {
        $this->router = $router;
        $this->request = $request;
        $this->options = $options;
        $this->logger = $logger;
        $this->uri = $uri;
        $this->helper = $helper;
        $this->store = $store;
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('options', [$this, 'options']),
            new Twig_SimpleFunction('asset', [$this, 'asset']),
            new Twig_SimpleFunction('path_for', [$this, 'pathFor']),
            new Twig_SimpleFunction('base_url', [$this, 'baseUrl']),
            new Twig_SimpleFunction('is_current_path', [$this, 'isCurrentPath']),
            new Twig_SimpleFunction('avatar_url', array($this, 'avatarUrlFunction')),
            new Twig_SimpleFunction('proposals_available', array($this, 'proposalsAvailable')),
            new Twig_SimpleFunction('proposals_ended', array($this, 'proposalsEnded')),
            new Twig_SimpleFunction('vote_available', array($this, 'voteAvailable')),
            new Twig_SimpleFunction('vote_ended', array($this, 'voteEnded')),
            new Twig_SimpleFunction('show_results', array($this, 'showResults')),
            new Twig_SimpleFunction('citizen', array($this, 'getCitizen')),
            new Twig_SimpleFunction('is_state', array($this, 'isState')),
            new Twig_SimpleFunction('get_state', array($this, 'getState')),
        ];
    }

    /*public function getGlobals()
    {
    return [
    'subject' => $this->request->getAttribute('subject')->toArray(),
    ];
    }*/

    public function options()
    {
        return $this->options->getAutoloaded();
    }

    public function asset($name)
    {
        return $this->baseUrl() . '/assets/' . $name;
    }

    // TODO analizar si agregar parametro $absolute
    public function pathFor($name, $data = [], $query = [])
    {
        return $this->router->pathFor($name, $data, $query);
    }

    public function baseUrl()
    {
        if (is_string($this->uri)) {
            return $this->uri;
        }
        if (method_exists($this->uri, 'getBaseUrl')) {
            return $this->uri->getBaseUrl();
        }

        return rtrim(str_ireplace('index.php', '', $this->request->getUri()->getBaseUrl()), '/');
    }

    public function isCurrentPath($name)
    {
        return $this->router->pathFor($name) === $this->uri->getPath();
    }

    public function setBaseUrl($baseUrl)
    {
        $this->uri = $baseUrl;
    }

    public function avatarUrlFunction($type, $hash, $size)
    {
        switch ($type) {
            case 0:
                return 'https://www.gravatar.com/avatar/' . $hash . '?d=mm&s=' . $size;
            case 1:
                return 'https://graph.facebook.com/' . $hash . '/picture?width=' . $size;
            default:
                return 'https://www.gravatar.com/avatar/' . $hash . '?d=mm&s=' . $size;
                // return Slim\Slim::getInstance()->request()->getRootUri().'/img/usuario/'.$hash.'/'.$size.'.png';
        }
    }

    public function getName()
    {
        return 'app';
    }

    public function voteAvailable()
    {
        $isAvailable = $this->options->getOption('vote-available')->value;
        if ($isAvailable) {
            $start_date = $this->options->getOption('vote-launch')->value;
            $end_date = $this->options->getOption('vote-deadline')->value;
            $today = date('Y-m-d H:i:s');
            $isDateBetweenRange = $this->check_in_range($start_date, $end_date, $today);
            return $isDateBetweenRange;
        }
        return false;
    }

    public function proposalsAvailable()
    {
        $isAvailable = $this->options->getOption('proposals-available')->value;
        if ($isAvailable) {
            $start_date = $this->options->getOption('proposals-launch')->value;
            $end_date = $this->options->getOption('proposals-deadline')->value;
            $today = date('Y-m-d H:i:s');
            $isDateBetweenRange = $this->check_in_range($start_date, $end_date, $today);
            return $isDateBetweenRange;
        }
        return false;
    }

    public function proposalsEnded()
    {
        $end_date = $this->options->getOption('proposal-deadline')->value;
        $today = date('Y-m-d H:i:s');
        return $today > $end_date ;
    }

    public function voteEnded()
    {
        $end_date = $this->options->getOption('vote-deadline')->value;
        $today = date('Y-m-d H:i:s');
        return $today > $end_date ;
    }

    public function getCitizen()
    {
        if (!is_null($this->store['subject'])) {
            $subject = new DummySubject(
                $this->store['subject']['type'],
                $this->store['subject']['id'],
                $this->store['subject']['name'],
                $this->store['subject']['roles'],
                $this->store['subject']['extra']
            );
            // $subject = $this->$request->getAttribute('subject');
            $citizen = $this->helper->getCitizenFromSubject($subject);
            return $citizen;
        }
        return null;
    }

    public function check_in_range($start_date, $end_date, $date)
    {
        // Convert to timestamp
        // $start_ts = strtotime($start_date);
        // $end_ts = strtotime($end_date);
        // $date_ts = strtotime($date);

        // Check that user date is between start & end
        return (($date >= $start_date) && ($date <= $end_date));
    }

    public function isState($state){
        return $this->options->getOption('current-state')->value == $state;
    }

    public function getState(){
        return $this->options->getOption('current-state')->value;
    }

    public function showResults(){
        return $this->options->getOption('current-state')->value == 'results';
    }
}
