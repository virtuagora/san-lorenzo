<?php

namespace App\Action;

use App\Util\ContainerClient;

class MiscAction extends ContainerClient
{
    public function runInstall($request, $response, $params)
    {
        $env = $this->settings['mode'] ?? 'pro';
        if ($env == 'pro') {
            return $response->withJSON([
                'mensaje' => 'La instalación ha fallado',
            ]);
        }
        $actions = new \App\Util\ActionsLoader($this->db);
        if (!isset($params['extra'])) {
            $installer = new \App\Util\Installer($this->db);
            $installer->down();
            $installer->up();
            $installer->populate();
            // $loader = new \App\Util\DistrictsLoader($this->db);
            // $loader->up();
            // $loader = new \App\Util\DemoLoader($this->db);
            // $loader->up();
        } else {
            $actions->down();
        }
        $actions->up();
        return $response->withJSON(['message' => 'Installation was a success!']);
    }

    public function runUpdate($request, $response, $params)
    {
        $env = $this->settings['mode'] ?? 'pro';
        if ($env == 'pro') {
            return $response->withJSON([
                'mensaje' => 'La instalación ha fallado',
            ]);
        }
        $updater = new \App\Util\Updater($this->db);
        $updater->up();
        return $response->withJSON(['message' => 'Update was a success!']);
    }

    public function getDistricts($request, $response, $params)
    {
        $regs = $this->db->query('App:District')->get();
        return $response->withJSON($regs->toArray());
    }

    public function getLoggedPing($request, $response, $params)
    {
        if ($request->getAttribute('subject')->getType() != 'Annonymous') {
            return $response->withJSON(['message' => 'Pong!']);
        }
        return $response->withJSON(['message' => 'Login']);
    }
    
    // public function getRegions($request, $response, $params)
    // {
    //     $regs = $this->db->query('App:Region')->get();
    //     return $response->withJSON($regs->toArray());
    // }

    // public function getDepartments($request, $response, $params)
    // {
    //     $regId = $this->helper->getSanitizedId('reg', $params);
    //     $deps = $this->db->query('App:Department')->where('region_id', $regId)->get();
    //     return $response->withJSON($deps->toArray());
    // }

    // public function getLocalities($request, $response, $params)
    // {
    //     $depId = $this->helper->getSanitizedId('dep', $params);
    //     $locs = $this->db->query('App:Locality')->where('department_id', $depId)->get();
    //     return $response->withJSON($locs->toArray());
    // }

    // public function getLocality($request, $response, $params)
    // {
    //     $locId = $this->helper->getSanitizedId('loc', $params);
    //     $locality = $this->db->query('App:Locality', ['department.region'])->findOrFail($locId);
    //     return $response->withJSON($locality->toArray());
    // }
}
