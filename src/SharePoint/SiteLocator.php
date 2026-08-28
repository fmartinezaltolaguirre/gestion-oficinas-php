<?php
namespace App\SharePoint;

final class SiteLocator
{
    public function __construct(private readonly GraphClient $graph) {}

    public function siteId(): string
    {
        $host = $_ENV['SHAREPOINT_HOST'] ?? '';
        $path = $_ENV['SHAREPOINT_SITE_PATH'] ?? '';
        if ($host === '' || $path === '') {
            throw new \RuntimeException('Falta configurar el sitio SharePoint');
        }
        return $this->graph->get("sites/{$host}:{$path}")['id'];
    }
}
