<?php

namespace App\Action;

use App\Util\ContainerClient;
use App\Util\Utils;
use App\Util\Exception\AppException;
use App\Util\Exception\UnauthorizedException;

class AuditWebAction extends ContainerClient
{
    public function showSellos($request, $response, $params)
    {
        $trails = $this->db->query('App:AuditTrail')->get();
        return $response->withJSON([
            'trails' => $trails->toArray(),
        ]);
    }

    public function downloadRecibo($request, $response, $params)
    {
        $trail = $this->helper->getEntityFromId(
            'App:AuditTrail', 'aud', $params
        );
        $body = $response->getBody();
        $body->write('"' . $trail->extra['permanent_rd'] . '"');
        return $response->withBody($body)
            ->withHeader('Content-type', 'application/octet-stream')
            ->withHeader('Content-Disposition', 'attachment; filename="recibo.rd"');
    }

    public function downloadDataset($request, $response, $params)
    {
        $state = $this->options->getOption('current-state')->value;
        if ($state != 'results') {
            throw new AppException('El dataset estará disponible cuando se anuncien los resultados');
        }
        $trail = $this->helper->getEntityFromId(
            'App:AuditTrail', 'aud', $params
        );
        $path = '/datasets/' . $trail->file_name;
        $mime = $this->filesystem->getMimetype($path);
        $file = $this->filesystem->readStream($path);
        $stream = \GuzzleHttp\Psr7\stream_for($file);
        return $response->withBody($stream)
            ->withHeader('Content-type', $mime)
            ->withHeader('Content-Disposition', "attachment; filename=\"{$trail->file_name}\"");
    }

    public function runSellar($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->hasRoles($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $votos = $this->db->query('App:OnlineVote')->get();
        $name = '' .  time() . '.csv';
        $dire = $this->settings['filesystem']['args'][0] . '/datasets';
        $path = $dire . '/' . $name;
        if (!file_exists($dire)) {
            mkdir($dire, 0777, true);
        }
        $writer = \Box\Spout\Writer\WriterFactory::create(\Box\Spout\Common\Type::CSV);
        $writer->openToFile($path);
        $writer->addRow(['ID', 'hash', 'proyecto']);
        $writer->addRows($votos->map(function ($item) {
            return array_values($item->toArray());
        })->toArray());
        $writer->close();
        $hash = hash_file('sha256', $path);
        $trail = $this->db->new('App:AuditTrail');
        $trail->file_name = $name;
        $extra = [
            'hash' => $hash,
        ];
        $extra['temporary_rd'] = $this->ts->getTemporaryRd($hash);
        $trail->description = 'La transacción se encuentra pendiente de subida a la Blockchain';
        $trail->extra = $extra;
        $trail->state = 'created';
        $trail->save();
        return $response->withJSON([
            'trail' => $trail->toArray(),
        ]);
    }

    // 2025-06-14 FEAT: Unattended blockchain sealing endpoint (by @guillecro)
    // This endpoint will be used by a cronjob to seal the "current" votes in the blockchain
    // The idea is that everyday, at a specific time, the cronjob will call this endpoint
    // and seal the votes that have been casted until that moment in the blockchain.
    public function runSellarUnattended($request, $response, $params)
    {

        // This needs to be done in unattended mode, so it will not require any user interaction.
        // But interacting with the blockchain should be done during the voting period.
        $this->logger->info('Running unattended sellar action',[
            'request' => $request->getQueryParams(),
            'action' => 'runSellarUnattended',
            'url' => $request->getUri()->getPath(),
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $request->getAttribute('ip_address', 'unknown'),
            'user_agent' => $request->getHeaderLine('User-Agent'),
            'params' => $params,
        ]);
        // get the key from the query
        $keyUrl = $request->getQueryParam('key', null);
        // if the key is not provided, return an error
        if(!$keyUrl) {
            $this->logger->error('Key not provided in the request for unattended sellar action',[
                'action' => 'runSellarUnattended',
            ]);
            return $response->withJSON([
                'status' => 'error',
                'message' => 'Key is required',
            ], 401);
            // throw new UnauthorizedException('Key is required');
        }
        
        // The audit.cronjobKey is set in the settings.php file
        // The idea is to add a query parameter to the url called key
        // a cronjob will call this endpoint with the key
        // NOTE: THIS MUST BE SET BY A DEVOPS.
        $keySettings = $this->settings['audit']['cronjobKey'];
        if ($keyUrl !== $keySettings) {
            //throw new UnauthorizedException('Invalid key provided');
            $this->logger->error('Invalid key provided for unattended sellar action',[
                'action' => 'runSellarUnattended',
            ]);
            return $response->withJSON([
                'status' => 'error',
                'message' => 'Invalid key provided',
            ], 401);
        }

        // Check if the app is in voting state
        $state = $this->options->getOption('current-state')->value;
        if ($state != 'vote') {
            $this->logger->error('App is not in voting state for unattended sellar action',[
                'action' => 'runSellarUnattended',
            ]);
            return $response->withJSON([
                'status' => 'error',
                'message' => 'App is not in voting state',
            ], 401);
        }

        // Check if the voting period is valid
        // For that we get the start and end date from the options
        // The start date is the date when the voting starts
        // The end date is the date when the voting ends

        $start_date = $this->options->getOption('vote-launch')->value;
        $end_date = $this->options->getOption('vote-deadline')->value;
        // add 1 day to the end date
        $end_date = date('Y-m-d H:i:s', strtotime($end_date . ' +1 day'));
        // get the current date
        $today = date('Y-m-d H:i:s');

        if($today <= $start_date){
            $this->logger->error('Voting has not started yet for unattended sellar action',[
                'action' => 'runSellarUnattended',
            ]);
            // throw new UnauthorizedException('¡La votación no ha comenzado!');
            return $response->withJSON([
                'status' => 'error',
                'message' => 'Voting has not started yet',
            ], 401);
        }

        // Check if the voting period has ended
        // If the current date is greater than or equal to the end date, then the voting has ended
        
        if($today >= $end_date){
            // throw new UnauthorizedException('¡La votación ha terminado!');
            $this->logger->error('Voting has ended for unattended sellar action',[
                'action' => 'runSellarUnattended',
            ]);
            return $response->withJSON([
                'status' => 'error',
                'message' => 'No more votes are allowed, voting has ended',
            ], 401);
        }

        // At this point, we know that the voting period is valid

        // Still, skip the unattended sellar if the votes are not ready
        $cantVotosTotal = $this->db->query('App:OnlineVote')->count();
        if ($cantVotosTotal <= 0) {
            $this->logger->error('No votes to seal for unattended sellar action',[
                'action' => 'runSellarUnattended',
            ]);
            return $response->withJSON([
                'status' => 'error',
                'message' => 'No votes to seal',
            ], 401);
        }

        // Now we can proceed to seal the votes
        $votos = $this->db->query('App:OnlineVote')->get();
        $name = '' .  time() . '.csv';
        $dire = $this->settings['filesystem']['args'][0] . '/datasets';
        $path = $dire . '/' . $name;
        if (!file_exists($dire)) {
            mkdir($dire, 0777, true);
        }
        $writer = \Box\Spout\Writer\WriterFactory::create(\Box\Spout\Common\Type::CSV);
        $writer->openToFile($path);
        $writer->addRow(['ID', 'hash', 'proyecto']);
        $writer->addRows($votos->map(function ($item) {
            return array_values($item->toArray());
        })->toArray());
        $writer->close();
        $hash = hash_file('sha256', $path);
        $trail = $this->db->new('App:AuditTrail');
        $trail->file_name = $name;
        $extra = [
            'hash' => $hash,
            'unattended' => true,
        ];
        $extra['temporary_rd'] = $this->ts->getTemporaryRd($hash);
        $trail->description = 'La transacción se encuentra pendiente de subida a la Blockchain';
        $trail->extra = $extra;
        $trail->state = 'created';
        $trail->save();

        // Success
        $this->logger->info('Successfully sealed votes in unattended mode', [
            'action' => 'runSellarUnattended',
            'hash' => $hash,
            'file_name' => $name,
            'trail_id' => $trail->id,

        ]);
        // Return
        return $response->withJSON([
            'status' => 'success',
            'message' => 'Unattended sellar action executed successfully.',
            'votos_count' => $cantVotosTotal,
        ]);
    }

    // ------------

    public function runComprobar($request, $response, $params)
    {
        $subject = $request->getAttribute('subject');
        if (!$this->authorization->hasRoles($subject, 'admin')) {
            throw new UnauthorizedException();
        }
        $trail = $this->helper->getEntityFromId(
            'App:AuditTrail', 'aud', $params
        );
        $extra = $trail->extra;
        $data = $this->ts->getPermanentRd($extra['hash'], $extra['temporary_rd']);
        if ($data['status'] != 'success') {
            return $response->withJSON([
                'status' => $data['status'],
            ]);
        }
        $extra['permanent_rd'] = $data['permanent_rd'];
        $extra['attestation_time'] = $data['attestation_time'];
        $trail->description = $data['messages'];
        $trail->extra = $extra;
        $trail->state = 'attested';
        $trail->save();
        return $response->withJSON([
            'status' => 'success',
            'trail' => $trail->toArray(),
        ]);
    }
}