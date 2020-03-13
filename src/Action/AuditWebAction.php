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