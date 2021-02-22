<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class AdminController
{
    public function index(): Response
    {
        return new Response(
            '<html><body>Добавить растения</body></html>'
        );
    }

    public function addPlants(): Response
    {
        return new Response(
            '<html><body>Добавить растениe</body></html>'
        );
    }
}
