<?php

namespace App\Resource;
use Carbon\Carbon;

class BallotResource extends Resource
{

    public function retrieveOfflineBallots()
    {
        $results = $this->db->query('App:OfflineBallot', ['votes'])
            ->with(array('votes.project' => function ($query) {
                $query->select('id', 'code','type');
            }))->orderBy('order')->get();
        return $results;
    }

    public function sendOnlineBallot($ballot, $comus, $insts)
    {
        if (count($comus) > 3){
            return 'comusExceeded';
        }
        if (count($insts) > 3){
            return 'instsExceeded';
        }
        $comuPros = $this->db->query('App:Project')
            ->where('type', 'comunitario')
            ->whereIn('id', $comus)
            ->get();
        $instPros = $this->db->query('App:Project')
            ->where('type', 'institucional')
            ->whereIn('id', $insts)
            ->get();
        if (count($comus) != count($comuPros)) {
            return 'invalidComus';
        }
        if (count($insts) != count($instPros)) {
            return 'invalidInsts';
        }
        $projects = $comuPros->merge($instPros);
        $lastVote = $this->db->query('App:Project')->orderByDesc('id')->first();
        $lastHash = isset($lastVote) ? $lastVote->hash : '';
        foreach ($projects as $pro) {
            $vote = $this->db->newInstance('App:OnlineVote');
            $vote->project_id = $pro->id;
            $vote->hash = $this->getHash($vote, $lastHash);
            $vote->save();
            $lastHash = $vote->hash;
            //$this->logger->debug($pro->code . ' ' . $vote->id);
        }
        $ballot->sent = true;
        $ballot->save();
        return true;
    }

    public function registerVoter($citizen, $type)
    {
        $citizen->voted = true;
        $citizen->save();
        $sballot = $this->db->newInstance('App:StatisticalBallot', [
            'gender' => $citizen->genre,
            'age' => $citizen->year,
            'type' => $type,
        ]);
        $sballot->save();
    }

    public function isVotingTime()
    {
        $startDate = $this->options->getOption('vote-launch')->value;
        $endDate = $this->options->getOption('vote-deadline')->value;
        $today = new Carbon();
        if ($today->lt($startDate)){
            return false;
        }
        if ($today->gt($endDate)){
            return false;
        }
        return true;
    }

    public function retrieveBallotFromCode($code)
    {
        if (!isset($code)) {
            return 'notSentCode';
        }
        $ballot = $this->db->query('App:OnlineBallot')
            ->where('code', $code)
            ->first();
        if (!isset($ballot)) {
            return 'invalidCode';
        }
        if (isset($ballot->sent)) {
            return 'usedCode';
        }
        return $ballot;
    }

    private function getHash($new, $prevHash)
    {
        return hash('sha256', $new->project_id . $prevHash);
    }
}
