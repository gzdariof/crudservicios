<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ServicioService
{
    protected Client $client;
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('API_BASE_URL', 'http://localhost:5698'), '/');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 10,
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ]);
    }

    public function getAll(): array
    {
        try {
            $response = $this->client->get('/api/servicio');
            return json_decode($response->getBody()->getContents(), true) ?? [];
        } catch (RequestException $e) {
            Log::error('ServicioService::getAll - ' . $e->getMessage());
            return [];
        }
    }

    public function getById(int $id): ?array
    {
        try {
            $response = $this->client->get("/api/servicio/{$id}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("ServicioService::getById({$id}) - " . $e->getMessage());
            return null;
        }
    }

    public function create(array $data): array
    {
        try {
            $response = $this->client->post('/api/servicio', [
                'json' => $data,
            ]);
            return [
                'success' => true,
                'data'    => json_decode($response->getBody()->getContents(), true),
                'status'  => $response->getStatusCode(),
            ];
        } catch (RequestException $e) {
            $body = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Log::error('ServicioService::create - ' . $body);
            return [
                'success' => false,
                'message' => $this->parseError($body),
                'status'  => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
            ];
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $response = $this->client->put("/api/servicio/{$id}", [
                'json' => $data,
            ]);
            return [
                'success' => true,
                'data'    => json_decode($response->getBody()->getContents(), true),
                'status'  => $response->getStatusCode(),
            ];
        } catch (RequestException $e) {
            $body = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Log::error("ServicioService::update({$id}) - " . $body);
            return [
                'success' => false,
                'message' => $this->parseError($body),
                'status'  => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
            ];
        }
    }

    public function delete(int $id): array
    {
        try {
            $response = $this->client->delete("/api/servicio/{$id}");
            return [
                'success' => true,
                'status'  => $response->getStatusCode(),
            ];
        } catch (RequestException $e) {
            $body = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Log::error("ServicioService::delete({$id}) - " . $body);
            return [
                'success' => false,
                'message' => $this->parseError($body),
                'status'  => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500,
            ];
        }
    }

    private function parseError(string $body): string
    {
        $decoded = json_decode($body, true);
        if (is_array($decoded)) {
            return $decoded['message'] ?? $decoded['title'] ?? $body;
        }
        return $body ?: 'Error de conexión con el servidor.';
    }
}
