<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class AdminController
{
    public function index(): Response
    {
        return new Response(
            '<html><body>
                        <div><a href="/admin/plants/add">Добавить растения</a></div>
                        <div><a href="/admin/plants/list">Список растений</a></div>
                        <div><a href="/admin">adminPanel</a></div>
                    </body></html>'

        );
    }

    public function addPlants(): Response
    {
        return new Response(
            '<html><body>
                        Добавить растениe
                        <div><a href="/admin">adminPanel</a></div>
                    </body></html>'
        );
    }

    public function getListPlants(): Response
    {
        return new Response(
            '<html><body>
                        Список растений
                        <div><a href="/admin">adminPanel</a></div>
                    </body></html>'
        );
    }
}
