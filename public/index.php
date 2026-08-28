<?php
declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\OficinasController;
use App\Http\Router;
use App\SharePoint\GraphClient;
use App\SharePoint\TokenProvider;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';
Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

// Start session early for CSRF and flash messages
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$tokens = new TokenProvider(
    $_ENV['ENTRA_TENANT_ID'] ?? '',
    $_ENV['ENTRA_CLIENT_ID'] ?? '',
    $_ENV['ENTRA_CLIENT_SECRET'] ?? '',
    $_ENV['GRAPH_SCOPE'] ?? 'https://graph.microsoft.com/.default'
);
$graph = new GraphClient($tokens);
$router = new Router();
$oficinas = new OficinasController($graph);

$router->get('/', [new HomeController(), 'index']);
$router->get('/oficinas', [$oficinas, 'index']);
$router->get('/oficinas/nueva', [$oficinas, 'create']);
$router->post('/oficinas', [$oficinas, 'store']);
$router->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
