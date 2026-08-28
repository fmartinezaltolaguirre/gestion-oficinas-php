<?php
namespace App\Controller;

final class HomeController
{
    public function index(): void
    {
        $title = 'Gestion de Oficinas';
        require dirname(__DIR__, 2) . '/views/home.php';
    }
}
