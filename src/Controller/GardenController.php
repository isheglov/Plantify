<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class GardenController
{
    public function index(): Response
    {
        return new Response(
            '<html>
                        <body>
                            <div>Создание участка</div>
                            <div><a href="/">main</a></div>
                        </body>
                    </html>'
        );
    }
}
