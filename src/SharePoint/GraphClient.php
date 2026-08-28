<?php
namespace App\SharePoint;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Lightweight client for Microsoft Graph API.
 *
 * Wraps Guzzle and centralizes headers, timeouts and error handling so the
 * rest of the application can work with arrays and throw exceptions on errors.
 */
final class GraphClient
{
    private Client $http;

    /**
     * @param TokenProvider $tokens Provider for access tokens
     */
    public function __construct(private readonly TokenProvider $tokens)
    {
        // Base URI points to v1.0 of Graph API
        $this->http = new Client(['base_uri' => 'https://graph.microsoft.com/v1.0/']);
    }

    /**
     * Perform a GET request and return decoded JSON as array.
     *
     * @param string $path Graph path (e.g. 'sites/{id}/lists/{id}/items')
     * @param array $query Query parameters
     * @return array Decoded JSON response
     */
    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    /**
     * Perform a POST request with JSON body.
     *
     * @param string $path Graph path
     * @param array $body Body to be JSON encoded
     * @return array Decoded JSON response
     */
    public function post(string $path, array $body): array
    {
        return $this->request('POST', $path, ['json' => $body]);
    }

    /**
     * Internal request wrapper that sets headers, timeouts and converts
     * errors into RuntimeExceptions with logged messages for diagnostics.
     *
     * @param string $method HTTP method
     * @param string $path Path relative to base_uri
     * @param array $options Guzzle options
     * @return array Decoded JSON response
     * @throws \RuntimeException on network/HTTP/JSON errors
     */
    private function request(string $method, string $path, array $options = []): array
    {
        $options['headers'] = [
            'Authorization' => 'Bearer ' . $this->tokens->getToken(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        // sensible defaults
        $options['timeout'] = $options['timeout'] ?? 10;
        $options['connect_timeout'] = $options['connect_timeout'] ?? 5;

        try {
            $response = $this->http->request($method, ltrim($path, '/'), $options);
        } catch (GuzzleException $e) {
            // Log error for diagnostics
            error_log('[GraphClient] request error: ' . $e->getMessage());
            throw new \RuntimeException('Error en petición a Graph: ' . $e->getMessage());
        }

        $status = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($status < 200 || $status >= 300) {
            $msg = $body !== '' ? $body : "HTTP {$status}";
            error_log(sprintf('[GraphClient] unexpected status %d for %s: %s', $status, $path, $msg));
            throw new \RuntimeException('Respuesta HTTP inesperada de Graph: ' . $msg);
        }

        if ($body === '') {
            // Some endpoints may return no body; represent as empty array
            return [];
        }

        try {
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            error_log('[GraphClient] invalid json response: ' . $e->getMessage());
            throw new \RuntimeException('Respuesta JSON inválida de Graph: ' . $e->getMessage());
        }
    }
}
