<?php

namespace App\Service;

use GuzzleHttp\Client;

class TimestampService
{
    private $client;
    private $dummy;
    private $logger;
    
    public function __construct($dummy = true, $logger = null)
    {
        $this->dummy = $dummy;
        $this->client = new Client([
            'base_uri' => 'https://tsaapi.bfa.ar/api/tsa/',
            'timeout'  => 10.0,
        ]);
        $this->logger = $logger;
    }

    public function getTemporaryRd($hash)
    {
        if ($this->dummy) {
            return 'ca9ec93c862c4acb29b3d19df4b049692d5bc0b52eaad0368817a9887002c4f9';
        }
        $response = $this->client->request('POST', 'stamp/', [
            'form_params' => [
                'file_hash' => $hash,
            ],
        ]);
        $data = json_decode($response->getBody(), true);
        return $data['temporary_rd'];
    }

    public function getPermanentRd($hash, $rd)
    {
        if ($this->dummy) {
            return [
                "status" => "success",
                "permanent_rd" => "MXgtY2E5ZWM5M2M4NjJjNGFjYjI5YjNkMTlkZjRiMDQ5NjkyZDViYzBiNTJlYWFkMDM2ODgxN2E5ODg3MDAyYzRmOS1hNGI1MDZlMzAxMmQyNDVmZjRkMTE1ODkxNDJjZDkyNDM3YjliOTNhOTYwZTQ3OTkxMmU1MGFiYjVlMDc4ZWVmMDEtMHhmMzcwZWZjOTNmMTNlODk2Mzk5NGQ1OWUwN2Y5NjA4MmIyYTc3MWU5YTQ5ZGI0NGE3YmUxYzE1Y2Y1ZWQyZmI2LTUzMTY3ODc=",
                "messages" => "El archivo ca9ec93c862c4acb29b3d19df4b049692d5bc0b52eaad0368817a9887002c4f9 fue ingresado en el bloque 5316787 el 02/08/2019 19:43:57",
                "attestation_time" => "02/08/2019 19:43:57"
            ];
        }
        $params = [
            'file_hash' => $hash,
            'rd' => "\"$rd\"",
        ];
        $response = $this->client->request('POST', 'verify/', [
            'form_params' => $params,
        ]);
        // if (isset($this->logger)) {
        //     $this->logger->info($params);
        //     $this->logger->info($response->getBody());
        // }
        $data = json_decode($response->getBody(), true);
        if (!isset($data['status']) || $data['status'] != 'success') {
            $this->logger->info($response->getBody());
            return [
                'status' => 'error',
            ];
        }
        return $data;
    }
}
