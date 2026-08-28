<?php
namespace App\Http;

/**
 * Simple HTTP router for the small application.
 *
 * Responsibilities:
 * - Register GET and POST handlers
 * - Dispatch requests by normalized path
 * - Render a friendly error page on exceptions
 *
 * This router is intentionally small: it stores handlers in-memory and is
 * designed for a single-process, development-style environment.
 */
final class Router
{
    /** @var array<string, array<string, callable|array>> */
    private array $routes = [];

    /**
     * Register a GET route handler.
     *
     * @param string $path Exact path (e.g. '/oficinas')
     * @param callable|array $handler A callable or [object, 'method']
     */
    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Register a POST route handler.
     *
     * @param string $path Exact path
     * @param callable|array $handler Handler to invoke for POST requests
     */
    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Dispatch an incoming request to a registered handler.
     *
     * Normalizes the path by trimming a trailing slash (except for root).
     * If the handler throws an exception, a friendly error page is shown.
     *
     * @param string $method HTTP method (GET, POST)
     * @param string $path Request path (from parse_url)
     */
    public function dispatch(string $method, string $path): void
    {
        // Normalize path: remove empty or trailing slash
        $path = $path ?: '/';
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }

        $handler = $this->routes[$method][$path] ?? null;
        if ($handler === null) {
            http_response_code(404);
            echo 'Pagina no encontrada';
            return;
        }

        try {
            // Call the handler. Using call_user_func keeps this router tiny.
            call_user_func($handler);
        } catch (\Throwable $exception) {
            http_response_code(500);
            // In debug mode show the exception message, otherwise hide details
            $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);
            $message = $debug ? $exception->getMessage() : 'Error interno';
            require dirname(__DIR__, 2) . '/views/error.php';
        }
    }
}
