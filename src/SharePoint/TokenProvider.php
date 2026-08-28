<?php
namespace App\SharePoint;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * TokenProvider
 *
 * Encapsula la obtención y cache del token OAuth2 (client_credentials) desde
 * Entra ID (Azure AD). Mantiene el token en memoria y lo reutiliza hasta su
 * expiración para evitar llamadas innecesarias.
 */
final class TokenProvider
{
    /** Cached access token */
    private ?string $token = null;

    /** Unix timestamp cuando el token expira */
    private ?int $tokenExpiresAt = null;

    /**
     * @param string $tenantId Entra (Azure AD) tenant id
     * @param string $clientId Application client id
     * @param string $clientSecret Application client secret
     * @param string $scope OAuth scope (e.g. 'https://graph.microsoft.com/.default')
     */
    public function __construct(
        private readonly string $tenantId,
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $scope
    ) {}

    /**
     * Get an access token, using a cached value when still valid.
     *
     * @return string Bearer token
     * @throws \RuntimeException on configuration or network errors
     */
    public function getToken(): string
    {
        // Return cached token if not expired
        if ($this->token !== null && $this->tokenExpiresAt !== null && time() < $this->tokenExpiresAt) {
            return $this->token;
        }

        if ($this->tenantId === '' || $this->clientId === '' || $this->clientSecret === '') {
            throw new \RuntimeException('Falta configurar Entra ID en .env');
        }

        try {
            $client = new Client();
            // Request token using application credentials
            $response = $client->post(
                "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token",
                [
                    'form_params' => [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'scope' => $this->scope,
                        'grant_type' => 'client_credentials',
                    ],
                    // Timeouts to avoid hanging the app when Entra is lento
                    'timeout' => 10,
                    'connect_timeout' => 5,
                ]
            );
        } catch (GuzzleException $e) {
            // Log minimal info for diagnostics; do not leak secrets
            error_log('[TokenProvider] error fetching token: ' . $e->getMessage());
            throw new \RuntimeException('Error obteniendo token de Entra ID: ' . $e->getMessage());
        }

        $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data['access_token'])) {
            error_log('[TokenProvider] invalid token response: ' . substr((string) $response->getBody(), 0, 1000));
            throw new \RuntimeException('Respuesta inválida al pedir token');
        }

        // Compute expiry time and cache token
        $expiresIn = isset($data['expires_in']) ? (int) $data['expires_in'] : 3600;
        // Set expiry a bit earlier than actual to account for clock skew
        $this->tokenExpiresAt = time() + max(30, $expiresIn - 60);
        return $this->token = $data['access_token'];
    }
}
