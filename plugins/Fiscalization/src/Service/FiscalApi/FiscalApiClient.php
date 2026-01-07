<?php declare(strict_types=1);

namespace Fortuno\Fiscalization\Service\FiscalApi;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class FiscalApiClient
{
    private Client $client;

    public function __construct(
        private readonly string $endpoint, 
        private readonly string $oib, 
        string $token, 
        private readonly LoggerInterface $logger
    ) {
        $this->client = new Client([
            'base_uri' => rtrim($endpoint, '/'),
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
    }

    public function processInvoice(array $data): array
    {
        if (($data['operator_oib'] ?? '') !== $this->oib) {
            throw new \Exception('Operator OIB mismatch configuration.');
        }

        try {
            $response = $this->client->post('/invoice', ['json' => $data]);
            $body = json_decode($response->getBody()->getContents(), true);

            if (!($body['success'] ?? false)) {
                $err = $body['error'] ?? 'Unknown API error';
                throw new \Exception($err);
            }

            return $body;
        } catch (\Throwable $e) {
            $this->logger->error('Fiscal API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}