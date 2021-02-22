<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class UserController
{
    public function index(): Response
    {
        return new Response(
            '<html>
                        <body>
                            <div>Создание участка</div>
                            <div>Рекомендации по посадке</div>
                            <div>Посадка растения</div>
                        </body>
                    </html>'
        );
    }
}
