<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class AdminController extends AbstractController
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

    public function test(): Response
    {
        return $this->render('test.html.twig');
    }
}
