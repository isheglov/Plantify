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
                            <title>Plantify</title>
                            <link rel="preconnect" href="https://fonts.gstatic.com">
                            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
                            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                            <link rel="stylesheet" href="../../css/style.css">
                        <body>
                            <div class="nav-bar">
                                <div>
                                    <a class="nav-bar-item" href="/garden/create">Создание участка</a>
                                </div>
                                <div>
                                    <a class="nav-bar-item" href="/planning">Планирование посадок</a>
                                </div>
                            </div>
                        </body>
                    </html>'
            );
        }

        return new Response(
            '<html>
                            <title>Plantify</title>
                            <link rel="preconnect" href="https://fonts.gstatic.com">
                            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
                            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                            <link rel="stylesheet" href="../../css/style.css">
                        <body>
                            <div class="nav-bar">
                                <div>
                                    <a class="nav-bar-item" href="/garden">К участку</a>
                                </div>
                                <div>
                                    <a class="nav-bar-item" href="/planning">Планирование посадок</a>
                                </div>
                            </div>
                        </body>
                    </html>'
        );
    }
}
