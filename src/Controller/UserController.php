<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\GardenRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

final class UserController
{
    /** @var GardenRepository */
    private $gardenRepository;

    /** @var Security */
    private $security;

    public function __construct(
        GardenRepository $gardenRepository,
        Security $security
    ) {
        $this->gardenRepository = $gardenRepository;
        $this->security = $security;
    }

    public function index(): Response
    {
        $user = $this->security->getUser();
        $garden = $this->gardenRepository->findOneBy(['owner' => $user]);

        if($garden === null) {
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
                                    <a class="nav-bar-item" href="/planning">Планировать новые посадки</a>
                                </div>
                                <div>
                                    <a class="nav-bar-item" href="/todo">To-do лист</a>
                                </div>
                            </div>
                        </body>
                    </html>'
        );
    }
}
