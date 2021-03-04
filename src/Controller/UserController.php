<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\GardenRepository;
use Symfony\Component\HttpFoundation\Response;

final class UserController
{
    /** @var GardenRepository */
    private $gardenRepository;

    public function __construct(
        GardenRepository $gardenRepository
    ) {
        $this->gardenRepository = $gardenRepository;
    }

    public function index(): Response
    {
        $gardenList = $this->gardenRepository->findAll();

        if(empty($gardenList)) {
            return new Response(
                '<html>
                        <body>
                            <div><a href="/garden/create">Создание участка</a></div>
                            <div><a href="/planning">Планирование посадок(в разработке)</a></div>
                        </body>
                    </html>'
            );
        }

        return new Response(
            '<html>
                        <body>
                            <div><a href="/garden">К участку</a></div>
                            <div><a href="/planning">Планирование посадок(в разработке)</a></div>
                        </body>
                    </html>'
        );
    }
}
