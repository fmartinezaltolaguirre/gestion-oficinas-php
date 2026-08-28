<?php
namespace App\Controller;

use App\Repository\OficinaRepository;
use App\SharePoint\GraphClient;

final class OficinasController
{
    private OficinaRepository $repository;

    public function __construct(GraphClient $graph)
    {
        $this->repository = new OficinaRepository($graph);
    }

    public function index(): void
    {
        $title = 'Oficinas';
        $items = $this->repository->all();
        require dirname(__DIR__, 2) . '/views/oficinas/index.php';
    }

    public function create(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // Ensure a CSRF token exists for the form
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrfToken = $_SESSION['csrf_token'];

        $title = 'Nueva oficina';
        // Allow old input or error to be displayed if set
        $error = $_SESSION['form_error'] ?? null;
        $old = $_SESSION['form_old'] ?? [];
        unset($_SESSION['form_error'], $_SESSION['form_old']);

        require dirname(__DIR__, 2) . '/views/oficinas/create.php';
    }

    public function store(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        $codigo = trim((string) ($_POST['codigo'] ?? ''));
        $csrf = (string) ($_POST['csrf_token'] ?? '');

        // CSRF check
        if (empty($csrf) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            http_response_code(400);
            $_SESSION['form_error'] = 'Token CSRF inválido. Por favor recarga el formulario.';
            $_SESSION['form_old'] = ['nombre' => $nombre, 'codigo' => $codigo];
            header('Location: /oficinas/nueva');
            exit;
        }

        // Validate input lengths
        if ($nombre === '') {
            $_SESSION['form_error'] = 'El nombre es obligatorio.';
            $_SESSION['form_old'] = ['nombre' => $nombre, 'codigo' => $codigo];
            header('Location: /oficinas/nueva');
            exit;
        }
        if (mb_strlen($nombre) > 255 || mb_strlen($codigo) > 100) {
            $_SESSION['form_error'] = 'Los campos exceden la longitud permitida.';
            $_SESSION['form_old'] = ['nombre' => $nombre, 'codigo' => $codigo];
            header('Location: /oficinas/nueva');
            exit;
        }

        try {
            $this->repository->create(['Title' => $nombre, 'Codigo' => $codigo]);
            // Regenerate CSRF token after successful post
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            header('Location: /oficinas');
            exit;
        } catch (\Throwable $e) {
            // Log or handle the error; show a friendly message
            http_response_code(500);
            $_SESSION['form_error'] = 'No se pudo crear la oficina. Inténtalo más tarde.';
            $_SESSION['form_old'] = ['nombre' => $nombre, 'codigo' => $codigo];
            header('Location: /oficinas/nueva');
            exit;
        }
    }
}
