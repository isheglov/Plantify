<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UserController
{
    public function index(Request $request): Response
    {
//        var_dump($request->toArray());
        return new Response(
            '<html>
                        <body>
                            <div><a href="/garden/create">Создание участка</a></div>
                            <div>Рекомендации по посадке</div>
                            '. ' '/*print_r($request->toArray(),1)*/.'
                            <div>Посадка растения</div>
                        </body>
                    </html>'
        );
    }
}
