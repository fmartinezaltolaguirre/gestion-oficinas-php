<?php
namespace App\Repository;

use App\SharePoint\GraphClient;
use App\SharePoint\SiteLocator;

/**
 * Repository para las oficinas. Encapsula las llamadas a Microsoft Graph para
 * listar y crear ítems en la lista de SharePoint configurada.
 */
final class OficinaRepository
{
    public function __construct(private readonly GraphClient $graph) {}

    /**
     * Build the base items path for the configured SharePoint list.
     *
     * @return string Path usable with GraphClient (sites/{siteId}/lists/{listId}/items)
     * @throws \RuntimeException when SP_LIST_OFICINAS_ID is not configured
     */
    private function basePath(): string
    {
        $listId = $_ENV['SP_LIST_OFICINAS_ID'] ?? '';
        if ($listId === '') {
            throw new \RuntimeException('Falta SP_LIST_OFICINAS_ID');
        }
        $siteId = (new SiteLocator($this->graph))->siteId();
        return "sites/{$siteId}/lists/{$listId}/items";
    }

    /**
     * Fetch all offices from the configured SharePoint list.
     *
     * On error an empty array is returned so views can handle gracefully.
     *
     * @return array List of items (each item includes 'fields' subarray)
     */
    public function all(): array
    {
        try {
            $resp = $this->graph->get($this->basePath(), ['$expand' => 'fields']);
            return $resp['value'] ?? [];
        } catch (\Throwable $e) {
            // Record error for diagnostics and return an empty list
            error_log('[OficinaRepository] failed to fetch all: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a new office item in SharePoint.
     *
     * @param array $fields Map of SharePoint fields (e.g. ['Title' => 'Nombre'])
     * @return array Decoded response from Graph (new item)
     * @throws \Throwable When underlying GraphClient or TokenProvider fail
     */
    public function create(array $fields): array
    {
        try {
            return $this->graph->post($this->basePath(), ['fields' => $fields]);
        } catch (\Throwable $e) {
            // Log the error with the attempted fields (no secrets included)
            error_log('[OficinaRepository] create error: ' . $e->getMessage() . ' - fields: ' . json_encode($fields, JSON_UNESCAPED_UNICODE));
            throw $e;
        }
    }
}
